<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mercado;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /** 1. Listar todos los agricultores */
    public function index()
    {
        $agricultores = User::with('mercado')
    ->where('role', 'agricultor')
    ->get();
        return view('admin.usuarios.index', compact('agricultores'));
    }

    /** 2. Mostrar formulario para asignar mercado */
    public function edit(User $usuario)
    {
        $mercados = Mercado::all();
        return view('admin.usuarios.edit', compact('usuario', 'mercados'));
    }

    /** 3. Guardar la asignación de mercado */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'mercado_id' => 'required|exists:mercados,id',
        ]);

        $usuario->update([
            'mercado_id' => $request->mercado_id,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Mercado asignado correctamente.');
    }

    // (Opcional: show y destroy si los necesitas)
}
