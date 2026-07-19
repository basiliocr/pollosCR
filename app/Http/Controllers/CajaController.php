<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use App\Models\VentaPago;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id;

        // ¿Hay una caja abierta en esta sucursal?
        $cajaAbierta = Caja::where('sucursal_id', $sucursalId)
            ->whereNull('cerrada_en')
            ->with('user')
            ->first();

        $resumen = null;
        if ($cajaAbierta) {
            $resumen = $this->calcularResumen($cajaAbierta);
        }

        // Historial de cajas cerradas
        $historial = Caja::where('sucursal_id', $sucursalId)
            ->whereNotNull('cerrada_en')
            ->with('user')
            ->orderByDesc('cerrada_en')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'usuario' => $c->user->name,
                'apertura' => $c->monto_apertura,
                'cierre' => $c->monto_cierre,
                'abierta_en' => $c->abierta_en?->format('d/m/Y H:i'),
                'cerrada_en' => $c->cerrada_en?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Caja/Index', [
            'caja_abierta' => $cajaAbierta ? [
                'id' => $cajaAbierta->id,
                'usuario' => $cajaAbierta->user->name,
                'monto_apertura' => $cajaAbierta->monto_apertura,
                'abierta_en' => $cajaAbierta->abierta_en?->format('d/m/Y H:i'),
            ] : null,
            'resumen' => $resumen,
            'historial' => $historial,
        ]);
    }

    public function abrir(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id;

        // No permitir dos cajas abiertas
        $existe = Caja::where('sucursal_id', $sucursalId)->whereNull('cerrada_en')->exists();
        if ($existe) {
            return back()->withErrors(['caja' => 'Ya hay una caja abierta en esta sucursal.']);
        }

        $datos = $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        Caja::create([
            'user_id' => $request->user()->id,
            'sucursal_id' => $sucursalId,
            'monto_apertura' => $datos['monto_apertura'],
            'abierta_en' => now(),
        ]);

        return back()->with('success', 'Caja abierta correctamente.');
    }

    // Calcula lo que debería haber en la caja según las ventas del turno
    private function calcularResumen(Caja $caja)
    {
        // Ventas completadas desde que se abrió la caja, en esa sucursal
        $ventas = Venta::where('sucursal_id', $caja->sucursal_id)
            ->where('estado', 'completada')
            ->where('created_at', '>=', $caja->abierta_en)
            ->pluck('id');

        // Pagos de esas ventas, agrupados por método
        $pagosPorMetodo = VentaPago::whereIn('venta_id', $ventas)
            ->select('metodo_pago_id', DB::raw('SUM(monto) as total'))
            ->groupBy('metodo_pago_id')
            ->with('metodoPago')
            ->get()
            ->map(fn ($p) => [
                'metodo' => $p->metodoPago->nombre,
                'total' => (float) $p->total,
            ]);

        $totalVentas = $pagosPorMetodo->sum('total');

        // Efectivo esperado = apertura + ventas en efectivo
        $efectivoVentas = $pagosPorMetodo->firstWhere('metodo', 'Efectivo')['total'] ?? 0;
        $efectivoEsperado = (float) $caja->monto_apertura + $efectivoVentas;

        return [
            'pagos_por_metodo' => $pagosPorMetodo,
            'total_ventas' => $totalVentas,
            'cantidad_ventas' => $ventas->count(),
            'efectivo_esperado' => $efectivoEsperado,
        ];
    }

    public function cerrar(Request $request, Caja $caja)
    {
        // No cerrar una caja ya cerrada
        if ($caja->cerrada_en) {
            return back()->withErrors(['caja' => 'Esta caja ya está cerrada.']);
        }

        $datos = $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
        ]);

        $caja->update([
            'monto_cierre' => $datos['monto_cierre'],
            'cerrada_en' => now(),
        ]);

        // Registrar en auditoría
        \App\Models\AuditoriaLog::create([
            'user_id' => $request->user()->id,
            'accion' => 'Cerró caja',
            'tabla_afectada' => 'caja',
            'registro_id' => $caja->id,
        ]);

        return redirect()->route('caja.index')
            ->with('success', 'Caja cerrada correctamente.');
    }
}