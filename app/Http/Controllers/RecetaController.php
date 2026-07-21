<?php

namespace App\Http\Controllers;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RecetaController extends Controller
{
    public function index()
    {
        // Solo productos que requieren receta
        $productos = Producto::where('requiere_receta', true)
            ->with('receta.insumo', 'receta.unidad')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'insumos' => $p->receta->map(fn ($r) => [
                    'insumo' => $r->insumo->nombre,
                    'cantidad' => $r->cantidad,
                    'unidad' => $r->unidad->abreviatura,
                ]),
            ]);

        return Inertia::render('Recetas/Index', ['productos' => $productos]);
    }

    public function edit(Producto $producto)
    {
        $producto->load('receta');

        return Inertia::render('Recetas/Edit', [
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'items' => $producto->receta->map(fn ($r) => [
                    'insumo_id' => $r->insumo_id,
                    'cantidad' => (float) $r->cantidad,
                    'unidad_id' => $r->unidad_id,
                ]),
            ],
            // Insumos disponibles = todos los productos (un insumo es cualquier producto)
            'insumos' => Producto::orderBy('nombre')->get(['id', 'nombre']),
            'unidades' => UnidadMedida::orderBy('nombre')->get(['id', 'nombre', 'abreviatura']),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'items' => 'required|array',
            'items.*.insumo_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
            'items.*.unidad_id' => 'required|exists:unidades_medida,id',
        ]);

        DB::transaction(function () use ($datos, $producto) {
            // Borrar la receta anterior y crear la nueva
            $producto->receta()->delete();

            foreach ($datos['items'] as $item) {
                $producto->receta()->create([
                    'insumo_id' => $item['insumo_id'],
                    'cantidad' => $item['cantidad'],
                    'unidad_id' => $item['unidad_id'],
                ]);
            }
        });

        return redirect()->route('recetas.index')->with('success', 'Receta actualizada.');
    }

    public function produccion()
    {
        // Platos que tienen receta definida
        $platos = Producto::where('requiere_receta', true)
            ->whereHas('receta')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Recetas/Produccion', [
            'platos' => $platos,
        ]);
    }

    public function producir(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $sucursalId = $request->user()->sucursal_id;
        $plato = Producto::with('receta.unidad', 'receta.insumo.unidad')->findOrFail($datos['producto_id']);

        if ($plato->receta->isEmpty()) {
            return back()->withErrors(['produccion' => 'Este plato no tiene receta definida.']);
        }

        DB::transaction(function () use ($plato, $datos, $sucursalId, $request) {
            foreach ($plato->receta as $ingrediente) {
                // Cantidad total del insumo = cantidad de la receta × lotes producidos
                $cantidadReceta = (float) $ingrediente->cantidad * (float) $datos['cantidad'];

                // Convertir a la unidad en que se guarda el inventario del insumo
                $unidadReceta = $ingrediente->unidad;
                $unidadInsumo = $ingrediente->insumo->unidad;

                $cantidadDescontar = $cantidadReceta;
                if ($unidadReceta && $unidadInsumo && $unidadReceta->id !== $unidadInsumo->id) {
                    // Usa el método de conversión del modelo UnidadMedida
                    $cantidadDescontar = $unidadReceta->convertirA($unidadInsumo, $cantidadReceta);
                }

                // Descontar el insumo del inventario
                $inv = Inventario::where('producto_id', $ingrediente->insumo_id)
                    ->where('sucursal_id', $sucursalId)
                    ->first();
                if ($inv) {
                    $inv->decrement('stock_actual', $cantidadDescontar);
                }

                MovimientoInventario::create([
                    'producto_id' => $ingrediente->insumo_id,
                    'sucursal_id' => $sucursalId,
                    'user_id' => $request->user()->id,
                    'tipo' => 'salida_produccion',
                    'cantidad' => $cantidadDescontar,
                    'motivo' => 'Producción de ' . $datos['cantidad'] . ' × ' . $plato->nombre,
                ]);
            }

            // Opcional: sumar el plato preparado al inventario
            $invPlato = Inventario::firstOrCreate(
                ['producto_id' => $plato->id, 'sucursal_id' => $sucursalId],
                ['stock_actual' => 0, 'stock_minimo' => 0]
            );
            $invPlato->increment('stock_actual', $datos['cantidad']);

            MovimientoInventario::create([
                'producto_id' => $plato->id,
                'sucursal_id' => $sucursalId,
                'user_id' => $request->user()->id,
                'tipo' => 'entrada',
                'cantidad' => $datos['cantidad'],
                'motivo' => 'Producción',
            ]);
        });

        return back()->with('success', 'Producción registrada. Insumos descontados del inventario.');
    }
}