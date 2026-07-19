<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        // Ventas de hoy (solo completadas)
        $ventasHoy = Venta::whereDate('created_at', $hoy)
            ->where('estado', 'completada')
            ->get();

        $totalHoy = $ventasHoy->sum('total');
        $cantidadHoy = $ventasHoy->count();

        // Productos con stock bajo o igual al mínimo
        $stockBajo = Inventario::with(['producto', 'sucursal'])
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->get()
            ->map(fn ($inv) => [
                'producto' => $inv->producto->nombre,
                'sucursal' => $inv->sucursal->nombre,
                'stock_actual' => $inv->stock_actual,
                'stock_minimo' => $inv->stock_minimo,
            ]);

        // Top 5 productos más vendidos (por cantidad, en ventas completadas)
        $masVendidos = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereNotNull('producto_id')
            ->whereHas('venta', fn ($q) => $q->where('estado', 'completada'))
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->with('producto')
            ->get()
            ->map(fn ($d) => [
                'producto' => $d->producto->nombre,
                'cantidad' => (float) $d->total_vendido,
            ]);

        return Inertia::render('Dashboard', [
            'metricas' => [
                'total_hoy' => $totalHoy,
                'cantidad_hoy' => $cantidadHoy,
                'stock_bajo_count' => $stockBajo->count(),
            ],
            'stock_bajo' => $stockBajo,
            'mas_vendidos' => $masVendidos,
        ]);
    }
}