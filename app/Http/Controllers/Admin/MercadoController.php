<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mercado;
use Illuminate\Http\Request;

class MercadoController extends Controller
{
    /**
     * Mostrar el listado de mercados.
     */
    public function index()
    {
        $mercados = Mercado::all();
        return view('admin.mercados.index', compact('mercados'));
    }

    /**
     * Mostrar el formulario para crear un nuevo mercado.
     */
    public function create()
    {
        return view('admin.mercados.create');
    }

    /**
     * Almacenar un mercado recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'zona'   => 'nullable|string|max:255',
        ]);

        // Creación del mercado
        Mercado::create([
            'nombre' => $request->nombre,
            'zona'   => $request->zona,
        ]);

        return redirect()
            ->route('admin.mercados.index')
            ->with('success', 'Mercado creado exitosamente.');
    }

    /**
     * Mostrar los detalles de un mercado específico.
     */
    public function show(Mercado $mercado)
    {
        return view('admin.mercados.show', compact('mercado'));
    }

    /**
     * Mostrar el formulario para editar un mercado existente.
     */
    public function edit(Mercado $mercado)
    {
        return view('admin.mercados.edit', compact('mercado'));
    }

    /**
     * Actualizar el mercado en la base de datos.
     */
    public function update(Request $request, Mercado $mercado)
    {
        // Validación de datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'zona'   => 'nullable|string|max:255',
        ]);

        // Actualización del mercado
        $mercado->update([
            'nombre' => $request->nombre,
            'zona'   => $request->zona,
        ]);

        return redirect()
            ->route('admin.mercados.index')
            ->with('success', 'Mercado actualizado correctamente.');
    }

    /**
     * Eliminar un mercado de la base de datos.
     */
    public function destroy(Mercado $mercado)
    {
        $mercado->delete();

        return redirect()
            ->route('admin.mercados.index')
            ->with('success', 'Mercado eliminado.');
    }
}
