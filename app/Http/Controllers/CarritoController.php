<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class CarritoController extends Controller
{
    public function index()
    {
        $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();
             return view('carrito.index', compact('carrito'));
        $cartData = $this->loadCartData();
            return view('tienda', compact('cartData'));
    }

    public function add(Request $request, $productId)
    {
        $producto = Product::findOrFail($productId);
        $carrito = Carrito::firstOrCreate(['user_id' => Auth::id()]);
    
        $item = $carrito->items()->where('producto_id', $productId)->first();
    
        if ($item) {
            $item->cantidad += $request->input('cantidad', 1);
            $item->save();
        } else {
            $item = CarritoItem::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $productId,
                'cantidad' => $request->input('cantidad', 1),
            ]);
        }
    
        // Recalcular el total del carrito
        $totalItems = $carrito->items->sum('cantidad');
        $totalPrice = $carrito->items->sum(function ($item) {
            return $item->product->precio * $item->cantidad;
        });
    
        // Devolver datos en formato JSON
        return response()->json([
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
            'items' => $carrito->items->map(function ($item) {
                return [
                    'nombre' => $item->product->nombre,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $item->product->precio * $item->cantidad,
                    'imagen_url' => $item->product->imagen ? asset('storage/productos/' . $item->product->imagen) : asset('images/default-product.png'),
                ];
            }),
        ]);
    }
    

    // Controlador para cargar el carrito en la vista
    public function loadCartData()
    {
        $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();

        $totalItems = 0;
        $totalPrice = 0.00;

        if ($carrito) {
            $totalItems = $carrito->items->sum('cantidad');
            $totalPrice = $carrito->items->sum(function ($item) {
                return $item->product->precio * $item->cantidad;
            });
        }

        return [
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
        ];
    }

    public function getDetails()
{
    $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();

    if (!$carrito) {
        return response()->json([
            'items' => [],
            'totalPrice' => 0.00,
            'totalItems' => 0,
        ]);
    }

    $items = $carrito->items->map(function ($item) {
        return [
            'nombre' => $item->product->nombre,
            'cantidad' => $item->cantidad,
            'subtotal' => $item->product->precio * $item->cantidad,
            'imagen_url' => $item->product->imagen ? asset('storage/productos/' . $item->product->imagen) : asset('images/default-product.png'),
        ];
    });

    $totalPrice = $carrito->items->sum(function ($item) {
        return $item->product->precio * $item->cantidad;
    });

    $totalItems = $carrito->items->sum('cantidad');

    return response()->json([
        'items' => $items,
        'totalPrice' => $totalPrice,
        'totalItems' => $totalItems,
    ]);
}

    public function remove($itemId)
    {
        $item = CarritoItem::findOrFail($itemId);
        $item->delete();

        return redirect()->route('carrito.index');
    }

    public function update(Request $request, $itemId)
{
    $item = CarritoItem::findOrFail($itemId);
    $item->cantidad = $request->input('cantidad');
    $item->save();

    // Calcula el subtotal del producto actualizado
    $itemSubtotal = $item->product->precio * $item->cantidad;

    // Calcula el nuevo subtotal y total del carrito
    $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();
    $cartSubtotal = $carrito->items->sum(function ($item) {
        return $item->product->precio * $item->cantidad;
    });
    $cartTotal = $cartSubtotal; // Puedes agregar costos de envío si es necesario

    // Devolver la respuesta en JSON
    return response()->json([
        'itemSubtotal' => $itemSubtotal,
        'cartSubtotal' => $cartSubtotal,
        'cartTotal' => $cartTotal,
    ]);
}

public function checkout()
{
    $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();

    if (!$carrito || $carrito->items->isEmpty()) {
        return redirect()->route('carrito.index')->with('error', 'El carrito está vacío.');
    }

    return view('carrito.checkout', compact('carrito'));
}

public function clear()
{
    $carrito = Carrito::where('user_id', Auth::id())->first();
    if ($carrito) {
        $carrito->items()->delete();
        $carrito->delete();
    }
}


    
}