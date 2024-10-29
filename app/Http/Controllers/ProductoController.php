<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medida;
use App\Models\Categoria;

class ProductoController extends Controller
{
    public function __construct()
{
    $this->middleware('auth')->except([
        'tienda',
        'buscarProductos',
        'show',
        'filtrarPorCategoria',
        'filtrarPorPrecio',
        'buscar',
        'filtrarPorProductor'
    ]);
}

public function buscarProductos(Request $request)
{
    $query = $request->input('q');

    // Verificar si está recibiendo la consulta
    if (!$query) {
        // Si no se envía ninguna consulta, devuelve todos los productos
        $productos = Product::all();
    } else {
        // Realiza la búsqueda
        $productos = Product::where('nombre', 'like', '%' . $query . '%')
            ->orWhere('descripcion', 'like', '%' . $query . '%')
            ->get();
    }

    // Obtener todas las categorías para el sidebar
    $categorias = Categoria::all();
    $productores = User::whereHas('productos')->get();

    // Retornar la vista de tienda con los productos encontrados
    return view('tienda', compact('productos', 'categorias', 'productores'));
}

public function buscarProductosAjax(Request $request)
{
    $query = $request->input('q');

    $productos = Product::where('nombre', 'like', '%' . $query . '%')
        ->orWhere('descripcion', 'like', '%' . $query . '%')
        ->get();

    return response()->json($productos);
}


    
    public function show($id)
    {
        $producto = Product::findOrFail($id);
        return view('productos.show', compact('producto'));
    }
    



    // Función para autorizar roles
    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }

    // Mostrar los productos del agricultor
    public function index()
    {
        // Autorizar solo a agricultores
        $this->authorizeRoles(['agricultor']);

        // Solo mostramos los productos del agricultor autenticado
        $productos = Product::where('user_id', Auth::id())->get();

        return view('productos.index', compact('productos'));
    }

    // Mostrar formulario de creación de productos
    public function create()
    {
        // Autorizar solo a agricultores
        $this->authorizeRoles(['agricultor']);
    
        // Obtener las medidas y categorías
        $medidas = Medida::all();
        $categorias = Categoria::all();
    
        return view('productos.create', compact('medidas', 'categorias'));
    }

    // Guardar un nuevo producto
    public function store(Request $request)
    {
        // Autorizar solo a agricultores
        $this->authorizeRoles(['agricultor']); 

    // Validación de los datos
    $request->validate([
        'nombre' => 'required|string|max:255',
        'medida_id' => 'required|exists:medidas,id',
        'descripcion' => 'nullable|string',
        'precio' => 'required|numeric',
        'cantidad_disponible' => 'required|integer',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'categoria_id' => 'required|exists:categorias,id'
    ]);

    // Subir la imagen
    $imagePath = null;
    if ($request->hasFile('imagen')) {
        $imagePath = $request->file('imagen')->store('productos', 'public');
    }

    // Crear el producto con todos los datos, incluyendo categoria_id y medida_id
    Product::create([
        'user_id' => auth()->id(),
        'nombre' => $request->nombre,
        'medida_id' => $request->medida_id,  // Asegúrate de incluir medida_id
        'descripcion' => $request->descripcion,
        'precio' => $request->precio,
        'cantidad_disponible' => $request->cantidad_disponible,
        'imagen' => $imagePath,
        'categoria_id' => $request->categoria_id  // Asegúrate de incluir categoria_id
    ]);

    return redirect()->route('productos.index')->with('success', 'Producto creado con éxito.');
}

    // Mostrar formulario de edición de producto
    public function edit(Product $producto)
    {
        // Autorizar solo a agricultores
        $this->authorizeRoles(['agricultor']);

        // Obtener todas las medidas y categorías
        $medidas = Medida::all();
        $categorias = Categoria::all();

        // Retornar la vista con los datos del producto, medidas y categorías
        return view('productos.edit', compact('producto', 'medidas', 'categorias'));
    }

    // Actualizar el producto
    public function update(Request $request, Product $producto)
    {
        $request->validate([
            'nombre' => 'required|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad_disponible' => 'required|integer',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoria_id' => 'required|exists:categorias,id',
            'medida_id' => 'required|exists:medidas,id',
        ]);
    
        // Manejar la subida de imagen si hay una nueva
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('productos', 'public');
            $producto->update(['imagen' => $imagePath]);
        }
    
        // Actualizar el producto
        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'cantidad_disponible' => $request->cantidad_disponible,
            'categoria_id' => $request->categoria_id,
            'medida_id' => $request->medida_id,
        ]);
    
        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }
    

    // Eliminar un producto
    public function destroy(Product $producto)
    {
        // Autorizar solo a agricultores
        $this->authorizeRoles(['agricultor']);

        // Verificar que el producto pertenezca al agricultor autenticado
        if ($producto->user_id != Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este producto.');
        }

        // Eliminar el producto
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }

    // Método para mostrar los productos filtrados por categoría
    public function filtrarPorCategoria(Categoria $categoria)
    {
        // Obtener productos que pertenecen a la categoría seleccionada
        $productos = Product::where('categoria_id', $categoria->id)->get();
    
        // Obtener todas las categorías para el sidebar
        $categorias = Categoria::all();
    
        // Obtener todos los productores que tienen productos
        $productores = User::whereHas('productos')->get();
    
        // Retornar la vista con los productos filtrados, las categorías y los productores
        return view('tienda', compact('productos', 'categorias', 'productores', 'categoria'));
    }
    

    // El método index para mostrar todos los productos y categorías
    public function tienda()
{
    // Obtener todos los productos disponibles con la URL de la imagen completa
    $productos = Product::all()->map(function ($producto) {
        $producto->imagen_url = $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('images/default-product.png');
        return $producto;
    });
    $categorias = Categoria::all();
    $productores = User::whereHas('productos')->get();

    return view('tienda', compact('productos', 'categorias', 'productores'));
}

    //Buscar
    public function buscar(Request $request)
{
    // Obtener el término de búsqueda
    $query = $request->input('query');

    // Buscar productos que coincidan con el nombre o la descripción
    $productos = Product::where('nombre', 'LIKE', "%$query%")
                        ->orWhere('descripcion', 'LIKE', "%$query%")
                        ->get();

    // Obtener todas las categorías para mostrar en el sidebar
    $categorias = Categoria::all();

    // Retornar la vista de tienda con los productos encontrados
    return view('tienda', compact('productos', 'categorias'));
}

public function filtrarPorPrecio(Request $request)
{
    // Obtener el rango de precios del formulario
    $min_price = $request->input('min_price', 1);
    $max_price = $request->input('max_price', 1500);

    // Filtrar productos según el rango de precios
    $productos = Product::whereBetween('precio', [$min_price, $max_price])->get();

    // Retornar la vista con los productos filtrados
    return view('productos.index', compact('productos'));
}

public function filtrarPorProductor($idProductor)
{
    // Obtener los productos del productor específico
    $productos = Product::where('user_id', $idProductor)->get();

    // Obtener todas las categorías para el sidebar
    $categorias = Categoria::all();

    // Obtener todos los productores con productos
    $productores = User::whereHas('productos')->get();

    // Retornar la vista con los productos filtrados, las categorías y los productores
    return view('tienda', compact('productos', 'categorias', 'productores'));
}




}
