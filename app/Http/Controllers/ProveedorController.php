<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function index()
    {
        return Inertia::render('Proveedores/Index', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Proveedores/Create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'nullable|string',
            'condicion_pago' => 'required|in:contado,credito',
        ]);

        Proveedor::create($datos);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return Inertia::render('Proveedores/Edit', [
            'proveedor' => $proveedor,
        ]);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'nullable|string',
            'condicion_pago' => 'required|in:contado,credito',
        ]);

        $proveedor->update($datos);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}