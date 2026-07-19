<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'unidad'])
            ->orderBy('codigo')
            ->get();

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Productos/Create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
            'unidades' => UnidadMedida::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo',
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id' => 'required|exists:unidades_medida,id',
            'requiere_receta' => 'boolean',
        ]);

        Producto::create($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return Inertia::render('Productos/Edit', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'unidades' => UnidadMedida::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo,' . $producto->id,
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id' => 'required|exists:unidades_medida,id',
            'requiere_receta' => 'boolean',
        ]);

        $producto->update($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}