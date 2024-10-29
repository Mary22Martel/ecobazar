<?php

namespace App\Http\Controllers;
use App\Models\Canasta;
use App\Models\Product;

use Illuminate\Http\Request;

class CanastaController extends Controller
{
    public function index()
    {
        $canastas = Canasta::all();
        return view('admin.canastas.index', compact('canastas'));
    }

    public function create()
{
    $productos = Product::all(); // Obtener todos los productos
    return view('admin.canastas.create', compact('productos'));
}



public function store(Request $request)
{
    // Primero creamos la canasta
    $canasta = Canasta::create($request->only(['nombre', 'precio', 'descripcion']));

    // Obtenemos los productos seleccionados
    $productos = $request->input('productos', []);

    // Si hay productos seleccionados, los agregamos a la canasta
    if (!empty($productos)) {
        foreach ($productos as $productoId => $productoData) {
            $canasta->productos()->attach($productoId, ['cantidad' => $productoData['cantidad']]);
        }
    }

    return redirect()->route('admin.canastas.index')->with('success', 'Canasta creada correctamente.');
}


public function edit(Canasta $canasta)
{
    $productos = Product::all(); // Asegúrate de obtener todos los productos
    return view('admin.canastas.edit', compact('canasta', 'productos'));
}


public function update(Request $request, Canasta $canasta)
{
    // Primero actualizamos la canasta
    $canasta->update($request->only(['nombre', 'precio', 'descripcion']));

    // Sincronizamos los productos con la canasta (eliminando los que ya no están seleccionados)
    $productos = $request->input('productos', []);
    $syncData = [];

    if (!empty($productos)) {
        foreach ($productos as $productoId => $productoData) {
            $syncData[$productoId] = ['cantidad' => $productoData['cantidad']];
        }
    }

    $canasta->productos()->sync($syncData);

    return redirect()->route('admin.canastas.index')->with('success', 'Canasta actualizada correctamente.');
}

    public function destroy(Canasta $canasta)
    {
        $canasta->delete();

        return redirect()->route('admin.canastas.index')->with('success', 'Canasta eliminada con éxito.');
    }
}
