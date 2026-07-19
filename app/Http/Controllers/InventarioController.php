<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventarioController extends Controller
{
    public function index()
    {
        $inventario = Inventario::with(['producto', 'sucursal'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'producto' => $item->producto->nombre,
                    'codigo' => $item->producto->codigo,
                    'sucursal' => $item->sucursal->nombre,
                    'stock_actual' => $item->stock_actual,
                    'stock_minimo' => $item->stock_minimo,
                    'bajo_minimo' => $item->bajoMinimo(),
                ];
            });

        return Inertia::render('Inventario/Index', [
            'inventario' => $inventario,
        ]);
    }

    public function updateMinimo(Request $request, Inventario $inventario)
    {
        $datos = $request->validate([
            'stock_minimo' => 'required|numeric|min:0',
        ]);

        $inventario->update($datos);

        return redirect()->route('inventario.index')
            ->with('success', 'Stock mínimo actualizado.');
    }
}