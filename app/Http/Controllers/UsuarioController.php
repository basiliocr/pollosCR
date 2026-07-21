<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('sucursal')->orderBy('name')->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'sucursal' => $u->sucursal?->nombre ?? 'Sin sucursal',
                'roles' => $u->getRoleNames(),
            ]);

        return Inertia::render('Usuarios/Index', ['usuarios' => $usuarios]);
    }

    public function create()
    {
        return Inertia::render('Usuarios/Create', [
            'roles' => Role::orderBy('name')->pluck('name'),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(6)],
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'sucursal_id' => $datos['sucursal_id'] ?? null,
        ]);

        $user->syncRoles($datos['roles']);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {
        return Inertia::render('Usuarios/Edit', [
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'sucursal_id' => $usuario->sucursal_id,
                'roles' => $usuario->getRoleNames(),
            ],
            'roles' => Role::orderBy('name')->pluck('name'),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => ['nullable', Password::min(6)],
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->sucursal_id = $datos['sucursal_id'] ?? null;
        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }
        $usuario->save();

        $usuario->syncRoles($datos['roles']);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario)
    {
        // No permitir que el admin se borre a sí mismo
        if ($usuario->id === $request->user()->id) {
            return back()->withErrors(['usuario' => 'No puedes eliminar tu propio usuario.']);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}