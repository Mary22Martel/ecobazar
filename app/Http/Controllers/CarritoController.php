<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Product;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    /**
     * Mostrar el carrito del usuario.
     */
    public function index()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with('items.product')
                          ->first();

        return view('carrito.index', compact('carrito'));
    }

    /**
     * Agregar un producto al carrito, validando
     * que pertenezca al mercado actual.
     */
    public function add(Request $request, $productId)
    {
        $producto = Product::findOrFail($productId);

        // Mercado en sesión
        $mercadoActual     = session('mercado_actual');
        // Mercado del agricultor que vende el producto
        $mercadoDelProducto = $producto->user->mercado_id;

        if (! $mercadoActual || $mercadoDelProducto != $mercadoActual) {
            return back()->with('error',
                'Este producto no pertenece a la feria que estás visitando.'
            );
        }

        // Obtener o crear carrito
        $carrito = Carrito::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // Buscar si el ítem ya existe
        $item = $carrito->items()
                        ->where('producto_id', $productId)
                        ->first();

        if ($item) {
            $item->cantidad += $request->input('cantidad', 1);
            $item->save();
        } else {
            CarritoItem::create([
                'carrito_id'  => $carrito->id,
                'producto_id' => $productId,
                'cantidad'    => $request->input('cantidad', 1),
            ]);
        }

        // Recargar relación para calcular totales
        $carrito->load('items.product');

        $totalItems = $carrito->items->sum('cantidad');
        $totalPrice = $carrito->items->sum(function($it){
            return $it->product->precio * $it->cantidad;
        });

        // Responder en JSON para AJAX
        return response()->json([
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
            'items'      => $carrito->items->map(function ($it) {
                return [
                    'nombre'     => $it->product->nombre,
                    'cantidad'   => $it->cantidad,
                    'subtotal'   => $it->product->precio * $it->cantidad,
                    'imagen_url' => $it->product->imagen
                        ? asset('storage/productos/' . $it->product->imagen)
                        : asset('images/default-product.png'),
                ];
            }),
        ]);
    }

    /**
     * Carga datos rápidos del carrito (para vistas o AJAX).
     */
    public function loadCartData()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with('items.product')
                          ->first();

        $totalItems = 0;
        $totalPrice = 0.00;

        if ($carrito) {
            $totalItems = $carrito->items->sum('cantidad');
            $totalPrice = $carrito->items->sum(function ($it) {
                return $it->product->precio * $it->cantidad;
            });
        }

        return [
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
        ];
    }

    /**
     * Detalles completos del carrito en formato JSON.
     */
    public function getDetails()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with('items.product')
                          ->first();

        if (! $carrito) {
            return response()->json([
                'items'      => [],
                'totalPrice' => 0.00,
                'totalItems' => 0,
            ]);
        }

        $items = $carrito->items->map(function ($it) {
            return [
                'nombre'     => $it->product->nombre,
                'cantidad'   => $it->cantidad,
                'subtotal'   => $it->product->precio * $it->cantidad,
                'imagen_url' => $it->product->imagen
                    ? asset('storage/productos/' . $it->product->imagen)
                    : asset('images/default-product.png'),
            ];
        });

        $totalPrice = $carrito->items->sum(function ($it) {
            return $it->product->precio * $it->cantidad;
        });

        $totalItems = $carrito->items->sum('cantidad');

        return response()->json([
            'items'      => $items,
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * Eliminar un ítem del carrito.
     */
    public function remove($itemId)
    {
        $item = CarritoItem::findOrFail($itemId);
        $item->delete();

        return redirect()->route('carrito.index');
    }

    /**
     * Actualizar cantidad de un ítem en el carrito.
     */
    public function update(Request $request, $itemId)
    {
        $item = CarritoItem::findOrFail($itemId);
        $item->cantidad = $request->input('cantidad');
        $item->save();

        // Recalcular totales
        $carrito      = Carrito::where('user_id', Auth::id())
                                ->with('items.product')
                                ->first();
        $itemSubtotal = $item->product->precio * $item->cantidad;
        $cartTotal    = $carrito->items->sum(function ($it) {
            return $it->product->precio * $it->cantidad;
        });

        return response()->json([
            'itemSubtotal' => $itemSubtotal,
            'cartTotal'    => $cartTotal,
        ]);
    }

    /**
     * Mostrar checkout (zonas, totales, etc.).
     */
    public function checkout()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with('items.product')
                          ->first();

        if (! $carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')
                             ->with('error', 'El carrito está vacío.');
        }

        $zones = Zone::all();

        return view('carrito.checkout', compact('carrito','zones'));
    }

    /**
     * Vaciar por completo el carrito del usuario.
     */
    public function clear()
    {
        $carrito = Carrito::where('user_id', Auth::id())->first();
        if ($carrito) {
            $carrito->items()->delete();
            $carrito->delete();
        }
    }
}
