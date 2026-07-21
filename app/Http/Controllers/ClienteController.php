<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function index()
    {
        return Inertia::render('Clientes/Index', [
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string',
            'nit' => 'nullable|string',
        ]);

        Cliente::create($datos);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado.');
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clientes/Edit', ['cliente' => $cliente]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $datos = $request->validate([
            'nombre' => 'required|string',
            'nit' => 'nullable|string',
        ]);

        $cliente->update($datos);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }
}