<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['proveedor', 'sucursal', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'fecha' => $c->fecha->format('d/m/Y'),
                'proveedor' => $c->proveedor->nombre,
                'sucursal' => $c->sucursal->nombre,
                'usuario' => $c->user->name,
                'total' => $c->total,
                'tiene_factura' => $c->tiene_factura,
            ]);

        return Inertia::render('Compras/Index', [
            'compras' => $compras,
        ]);
    }

    public function create()
    {
        return Inertia::render('Compras/Create', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
            'productos' => Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo']),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha' => 'required|date',
            'tiene_factura' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($datos, $request) {
            $total = collect($datos['items'])->sum(
                fn ($item) => $item['cantidad'] * $item['precio_unitario']
            );

            $compra = Compra::create([
                'proveedor_id' => $datos['proveedor_id'],
                'sucursal_id' => $datos['sucursal_id'],
                'user_id' => $request->user()->id,
                'fecha' => $datos['fecha'],
                'total' => $total,
                'tiene_factura' => $datos['tiene_factura'] ?? false,
            ]);

            foreach ($datos['items'] as $item) {
                $compra->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);

                // Sube el stock en el inventario de esa sucursal
                $inventario = Inventario::firstOrCreate(
                    ['producto_id' => $item['producto_id'], 'sucursal_id' => $datos['sucursal_id']],
                    ['stock_actual' => 0, 'stock_minimo' => 0]
                );
                $inventario->increment('stock_actual', $item['cantidad']);

                // Registra el movimiento
                MovimientoInventario::create([
                    'producto_id' => $item['producto_id'],
                    'sucursal_id' => $datos['sucursal_id'],
                    'user_id' => $request->user()->id,
                    'tipo' => 'entrada',
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'Compra #' . $compra->id,
                ]);
            }
        });

        return redirect()->route('compras.index')
            ->with('success', 'Compra registrada y stock actualizado.');
    }
}