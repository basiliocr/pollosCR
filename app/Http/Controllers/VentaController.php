<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Combo;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Inventario;
use Inertia\Inertia;
use App\Models\DetalleVenta;
use App\Models\VentaPago;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['user', 'sucursal', 'cliente'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'fecha' => $v->created_at->format('d/m/Y H:i'),
                'usuario' => $v->user->name,
                'sucursal' => $v->sucursal->nombre,
                'cliente' => $v->cliente?->nombre ?? 'Sin cliente',
                'total' => $v->total,
                'estado' => $v->estado,
            ]);

        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
        ]);
    }

    public function create()
    {
        $sucursalId = request()->user()->sucursal_id;

        // Productos con su stock en la sucursal del cajero
        $productos = Producto::where('activo', true)
            ->with(['categoria'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($p) use ($sucursalId) {
                $inv = Inventario::where('producto_id', $p->id)
                    ->where('sucursal_id', $sucursalId)
                    ->first();
                return [
                    'id' => $p->id,
                    'codigo' => $p->codigo,
                    'nombre' => $p->nombre,
                    'precio' => $p->precio,
                    'categoria' => $p->categoria->nombre,
                    'stock' => $inv ? (float) $inv->stock_actual : 0,
                ];
            });

        $combos = Combo::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'precio']);

        return Inertia::render('Ventas/Create', [
            'productos' => $productos,
            'combos' => $combos,
            'metodos_pago' => MetodoPago::all(['id', 'nombre']),
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre', 'nit']),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'items' => 'required|array|min:1',
            'items.*.tipo' => 'required|in:producto,combo',
            'items.*.id' => 'required|integer',
            'items.*.cantidad' => 'required|numeric|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'pagos' => 'required|array|min:1',
            'pagos.*.metodo_pago_id' => 'required|exists:metodos_pago,id',
            'pagos.*.monto' => 'required|numeric|min:0',
        ]);

        $sucursalId = $request->user()->sucursal_id;

        $total = collect($datos['items'])->sum(fn ($i) => $i['cantidad'] * $i['precio']);
        $totalPagado = collect($datos['pagos'])->sum('monto');

        // El pago debe cubrir el total
        if (round($totalPagado, 2) < round($total, 2)) {
            return back()->withErrors(['pagos' => 'El monto pagado no cubre el total de la venta.']);
        }

        $venta = DB::transaction(function () use ($datos, $sucursalId, $request, $total) {
            $venta = Venta::create([
                'cliente_id' => $datos['cliente_id'] ?? null,
                'user_id' => $request->user()->id,
                'sucursal_id' => $sucursalId,
                'estado' => 'completada',
                'total' => $total,
            ]);

            foreach ($datos['items'] as $item) {
                $venta->detalles()->create([
                    'producto_id' => $item['tipo'] === 'producto' ? $item['id'] : null,
                    'combo_id' => $item['tipo'] === 'combo' ? $item['id'] : null,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);

                // Descuento de stock según el tipo de item
                if ($item['tipo'] === 'producto') {
                    $this->descontarStock($item['id'], $sucursalId, $item['cantidad'], $request->user()->id, 'Venta');
                } elseif ($item['tipo'] === 'combo') {
                    // Un combo descuenta cada uno de sus productos componentes
                    $combo = \App\Models\Combo::with('detalles')->find($item['id']);
                    if ($combo) {
                        foreach ($combo->detalles as $componente) {
                            $cantidadTotal = $componente->cantidad * $item['cantidad'];
                            $this->descontarStock($componente->producto_id, $sucursalId, $cantidadTotal, $request->user()->id, 'Venta (combo: ' . $combo->nombre . ')');
                        }
                    }
                }
            }

            foreach ($datos['pagos'] as $pago) {
                if ($pago['monto'] > 0) {
                    $venta->pagos()->create([
                        'metodo_pago_id' => $pago['metodo_pago_id'],
                        'monto' => $pago['monto'],
                    ]);
                }
            }

            return $venta;
        });

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function cancelar(Request $request, Venta $venta)
    {
        // Si ya está cancelada, no hacer nada
        if ($venta->estado === 'cancelada') {
            return back()->withErrors(['venta' => 'Esta venta ya está cancelada.']);
        }

        DB::transaction(function () use ($venta, $request) {
            // Devolver el stock de cada producto de la venta
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto_id) {
                    $this->devolverStock($detalle->producto_id, $venta->sucursal_id, $detalle->cantidad, $request->user()->id, 'Cancelación de venta');
                } elseif ($detalle->combo_id) {
                    $combo = \App\Models\Combo::with('detalles')->find($detalle->combo_id);
                    if ($combo) {
                        foreach ($combo->detalles as $componente) {
                            $cantidadTotal = $componente->cantidad * $detalle->cantidad;
                            $this->devolverStock($componente->producto_id, $venta->sucursal_id, $cantidadTotal, $request->user()->id, 'Cancelación (combo: ' . $combo->nombre . ')');
                        }
                    }
                }
            }

            // Marcar la venta como cancelada (el registro se conserva)
            $venta->update(['estado' => 'cancelada']);

            // Registrar en auditoría quién la canceló
            \App\Models\AuditoriaLog::create([
                'user_id' => $request->user()->id,
                'accion' => 'Canceló venta',
                'tabla_afectada' => 'ventas',
                'registro_id' => null,
            ]);
        });

        return back()->with('success', 'Venta cancelada y stock devuelto.');
    }

    public function recibo(Venta $venta)
    {
        // Cargar la venta con todo lo que el recibo necesita
        $venta->load([
            'detalles.producto',
            'detalles.combo',
            'pagos.metodoPago',
            'cliente',
            'user',
            'sucursal',
        ]);

        $pdf = Pdf::loadView('recibos.venta', [
            'venta' => $venta,
        ]);

        // Descarga con nombre "recibo-XXXX.pdf"
        return $pdf->stream('recibo-' . substr($venta->id, 0, 8) . '.pdf');
    }

    private function descontarStock($productoId, $sucursalId, $cantidad, $userId, $motivo)
    {
        $inv = Inventario::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->first();
        if ($inv) {
            $inv->decrement('stock_actual', $cantidad);
        }

        MovimientoInventario::create([
            'producto_id' => $productoId,
            'sucursal_id' => $sucursalId,
            'user_id' => $userId,
            'tipo' => 'salida_venta',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
        ]);
    }

    private function devolverStock($productoId, $sucursalId, $cantidad, $userId, $motivo)
    {
        $inv = Inventario::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->first();
        if ($inv) {
            $inv->increment('stock_actual', $cantidad);
        }

        MovimientoInventario::create([
            'producto_id' => $productoId,
            'sucursal_id' => $sucursalId,
            'user_id' => $userId,
            'tipo' => 'ajuste',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
        ]);
    }
}