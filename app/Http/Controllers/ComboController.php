<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ComboController extends Controller
{
    public function index()
    {
        $combos = Combo::with('detalles.producto')->orderBy('nombre')->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'precio' => $c->precio,
                'activo' => $c->activo,
                'productos' => $c->detalles->map(fn ($d) => [
                    'nombre' => $d->producto->nombre,
                    'cantidad' => $d->cantidad,
                ]),
            ]);

        return Inertia::render('Combos/Index', ['combos' => $combos]);
    }

    public function create()
    {
        return Inertia::render('Combos/Create', [
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($datos) {
            $combo = Combo::create([
                'nombre' => $datos['nombre'],
                'precio' => $datos['precio'],
                'activo' => $datos['activo'] ?? true,
            ]);

            foreach ($datos['items'] as $item) {
                $combo->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }
        });

        return redirect()->route('combos.index')->with('success', 'Combo creado.');
    }

    public function destroy(Combo $combo)
    {
        $combo->delete(); // los detalles se borran solos por el cascadeOnDelete
        return redirect()->route('combos.index')->with('success', 'Combo eliminado.');
    }
}