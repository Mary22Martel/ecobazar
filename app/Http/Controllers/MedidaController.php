<?php
namespace App\Http\Controllers;

use App\Models\Medida;
use Illuminate\Http\Request;

class MedidaController extends Controller
{
    public function index()
    {
        $medidas = Medida::all();
        return view('admin.medidas.index', compact('medidas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:medidas|max:255',
        ]);

        Medida::create($request->all());

        return redirect()->route('admin.medidas.index')->with('success', 'Medida creada correctamente.');
    }

    public function edit(Medida $medida)
    {
        return view('admin.medidas.edit', compact('medida'));
    }

    public function update(Request $request, Medida $medida)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:medidas,nombre,' . $medida->id,
        ]);

        $medida->update($request->all());

        return redirect()->route('admin.medidas.index')->with('success', 'Medida actualizada correctamente.');
    }

    public function destroy(Medida $medida)
    {
        $medida->delete();

        return redirect()->route('admin.medidas.index')->with('success', 'Medida eliminada correctamente.');
    }
}
