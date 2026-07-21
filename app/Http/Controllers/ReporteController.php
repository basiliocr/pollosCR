<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        // Rango de fechas: por defecto, el mes actual
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        // Ventas completadas en el rango
        $ventas = Venta::where('estado', 'completada')
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with(['user', 'sucursal'])
            ->orderByDesc('created_at')
            ->get();

        $totalVendido = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalVendido / $cantidadVentas : 0;

        // Productos más vendidos en el rango
        $masVendidos = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total_cantidad'), DB::raw('SUM(subtotal) as total_monto'))
            ->whereNotNull('producto_id')
            ->whereHas('venta', function ($q) use ($desde, $hasta) {
                $q->where('estado', 'completada')
                  ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
            })
            ->groupBy('producto_id')
            ->orderByDesc('total_cantidad')
            ->with('producto')
            ->get()
            ->map(fn ($d) => [
                'producto' => $d->producto->nombre,
                'cantidad' => (float) $d->total_cantidad,
                'monto' => (float) $d->total_monto,
            ]);

        // Detalle de ventas para la tabla
        $listaVentas = $ventas->map(fn ($v) => [
            'fecha' => $v->created_at->format('d/m/Y H:i'),
            'usuario' => $v->user->name,
            'sucursal' => $v->sucursal->nombre,
            'total' => $v->total,
        ]);

        return Inertia::render('Reportes/Ventas', [
            'filtros' => ['desde' => $desde, 'hasta' => $hasta],
            'metricas' => [
                'total_vendido' => $totalVendido,
                'cantidad_ventas' => $cantidadVentas,
                'ticket_promedio' => round($ticketPromedio, 2),
            ],
            'mas_vendidos' => $masVendidos,
            'ventas' => $listaVentas,
        ]);
    }
    
    public function ventasPdf(Request $request)
    {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $ventas = Venta::where('estado', 'completada')
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with(['user', 'sucursal'])
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('reportes.ventas', [
            'desde' => $desde,
            'hasta' => $hasta,
            'ventas' => $ventas,
            'total' => $ventas->sum('total'),
            'cantidad' => $ventas->count(),
        ]);

        return $pdf->stream('reporte-ventas-' . $desde . '-a-' . $hasta . '.pdf');
    }
    
}