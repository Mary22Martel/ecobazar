<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Product;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CarritoController extends Controller
{
    /**
     * Mostrar el carrito del usuario.
     */
    public function index()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with(['items.product.user', 'items.product.categoria'])
                          ->first();

        return view('carrito.index', compact('carrito'));
    }

    /**
     * Agregar un producto al carrito con optimizaciones
     */
    public function add(Request $request, $productId)
    {
        try {
            DB::beginTransaction();

            // Validar que el producto existe y tiene stock
            $producto = Product::findOrFail($productId);
            
            $cantidadSolicitada = $request->input('cantidad', 1);
            
            // Verificar stock disponible
            if ($producto->cantidad_disponible < $cantidadSolicitada) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'No hay suficiente stock disponible. Stock actual: ' . $producto->cantidad_disponible
                ], 400);
            }

            // TEMPORAL: Comentar validación de mercado
            /*
            $mercadoActual = session('mercado_actual');
            $mercadoDelProducto = $producto->user->mercado_id;

            if (!$mercadoActual || $mercadoDelProducto != $mercadoActual) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Este producto no pertenece a la feria que estás visitando.'
                ], 400);
            }
            */

            // Obtener o crear carrito
            $carrito = Carrito::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            // Buscar si el ítem ya existe
            $item = $carrito->items()
                            ->where('producto_id', $productId)
                            ->first();

            if ($item) {
                // Verificar que no exceda el stock disponible
                $nuevaCantidad = $item->cantidad + $cantidadSolicitada;
                if ($nuevaCantidad > $producto->cantidad_disponible) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => 'No puedes agregar más de este producto. Stock disponible: ' . $producto->cantidad_disponible
                    ], 400);
                }
                
                $item->cantidad = $nuevaCantidad;
                $item->save();
            } else {
                $carrito->items()->create([
                    'producto_id' => $productId,
                    'cantidad' => $cantidadSolicitada,
                ]);
            }

            // Cargar relaciones necesarias con eager loading
            $carrito->load(['items.product' => function($query) {
                $query->select('id', 'nombre', 'precio', 'imagen', 'cantidad_disponible');
            }]);

            // Calcular totales
            $totalItems = $carrito->items->sum('cantidad');
            $totalPrice = $carrito->items->sum(function($item) {
                return $item->product->precio * $item->cantidad;
            });

             DB::commit();

            // Recargar el carrito con las relaciones necesarias
            $carrito = Carrito::where('user_id', Auth::id())
                              ->with('items')
                              ->first();

            // Calcular totales
            $totalItems = $carrito ? $carrito->items->sum('cantidad') : 0;

            return response()->json([
                'success' => true,
                'totalItems' => $totalItems,
                'message' => 'Producto agregado al carrito'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar producto al carrito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al agregar el producto al carrito. Por favor, intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Carga datos rápidos del carrito
     */
    public function loadCartData()
    {
        try {
            $carrito = Carrito::where('user_id', Auth::id())
                              ->with(['items.product:id,nombre,precio'])
                              ->first();

            $totalItems = 0;
            $totalPrice = 0.00;

            if ($carrito) {
                $totalItems = $carrito->items->sum('cantidad');
                $totalPrice = $carrito->items->sum(function ($item) {
                    return $item->product->precio * $item->cantidad;
                });
            }

            return response()->json([
                'success' => true,
                'totalItems' => $totalItems,
                'totalPrice' => $totalPrice,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar datos del carrito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'totalItems' => 0,
                'totalPrice' => 0,
            ]);
        }
    }

    /**
     * Detalles completos del carrito
     */
   // En tu CarritoController, REEMPLAZAR el método getDetails() por esta versión optimizada:

    public function getDetails()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'totalPrice' => 0.00,
                    'totalItems' => 0,
                ]);
            }

            // OPTIMIZACIÓN: Select solo los campos necesarios
            $carrito = Carrito::where('user_id', Auth::id())
                            ->with(['items' => function($query) {
                                $query->select('id', 'carrito_id', 'producto_id', 'cantidad')
                                        ->with(['product' => function($subQuery) {
                                            $subQuery->select('id', 'nombre', 'precio', 'imagen');
                                        }]);
                            }])
                            ->first(['id', 'user_id']);

            if (!$carrito || $carrito->items->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'totalPrice' => 0.00,
                    'totalItems' => 0,
                ]);
            }

            // OPTIMIZACIÓN: Calcular todo en una sola pasada
            $items = [];
            $totalPrice = 0;
            $totalItems = 0;

            foreach ($carrito->items as $item) {
                $subtotal = $item->product->precio * $item->cantidad;
                $totalPrice += $subtotal;
                $totalItems += $item->cantidad;

                $items[] = [
                    'id' => $item->id,
                    'nombre' => $item->product->nombre,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->product->precio,
                    'subtotal' => $subtotal,
                    'imagen_url' => $item->product->imagen
                        ? asset('storage/' . $item->product->imagen)
                        : asset('images/default-product.png'),
                ];
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'totalPrice' => $totalPrice,
                'totalItems' => $totalItems,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener detalles del carrito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener los detalles del carrito',
                'items' => [],
                'totalPrice' => 0.00,
                'totalItems' => 0,
            ], 500);
        }
    }

    /**
     * Eliminar un ítem del carrito
     */
    

    /**
     * Actualizar cantidad de un ítem
     */
    public function update(Request $request, $itemId)
    {
        try {
            $item = CarritoItem::with('product')
                               ->where('id', $itemId)
                               ->whereHas('carrito', function($query) {
                                   $query->where('user_id', Auth::id());
                               })
                               ->firstOrFail();
            
            $nuevaCantidad = $request->input('cantidad', 1);
            
            // Validar stock disponible
            if ($nuevaCantidad > $item->product->cantidad_disponible) {
                return response()->json([
                    'success' => false,
                    'error' => 'Stock insuficiente. Disponible: ' . $item->product->cantidad_disponible
                ], 400);
            }
            
            if ($nuevaCantidad <= 0) {
                $item->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado del carrito',
                    'itemSubtotal' => 0,
                    'cartTotal' => $this->calcularTotalCarrito(Auth::id())
                ]);
            }
            
            $item->cantidad = $nuevaCantidad;
            $item->save();

            $itemSubtotal = $item->product->precio * $item->cantidad;
            $cartTotal = $this->calcularTotalCarrito(Auth::id());

            return response()->json([
                'success' => true,
                'itemSubtotal' => $itemSubtotal,
                'cartTotal' => $cartTotal,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar item del carrito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el producto'
            ], 500);
        }
    }

    /**
     * Mostrar checkout
     */
    public function checkout()
    {
        $carrito = Carrito::where('user_id', Auth::id())
                          ->with(['items.product.user', 'items.product.categoria'])
                          ->first();

        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')
                           ->with('error', 'El carrito está vacío.');
        }

        // Verificar stock de todos los productos
        foreach ($carrito->items as $item) {
            if ($item->cantidad > $item->product->cantidad_disponible) {
                return redirect()->route('carrito.index')
                               ->with('error', 'Algunos productos no tienen stock suficiente. Por favor, revisa tu carrito.');
            }
        }

        $zones = Zone::orderBy('name')->get();

        return view('carrito.checkout', compact('carrito', 'zones'));
    }

    /**
     * Vaciar el carrito
     */
    public function clear()
    {
        try {
            $carrito = Carrito::where('user_id', Auth::id())->first();
            if ($carrito) {
                $carrito->items()->delete();
                $carrito->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al vaciar carrito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al vaciar el carrito'
            ], 500);
        }
    }

    /**
     * Calcular total del carrito
     */
    private function calcularTotalCarrito($userId)
    {
        $carrito = Carrito::where('user_id', $userId)
                          ->with('items.product:id,precio')
                          ->first();
        
        if (!$carrito) {
            return 0;
        }

        return $carrito->items->sum(function ($item) {
            return $item->product->precio * $item->cantidad;
        });
    }

        public function count()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['totalItems' => 0]);
            }

            $carrito = Carrito::where('user_id', Auth::id())
                            ->with('items')
                            ->first();
            
            $totalItems = $carrito ? $carrito->items->sum('cantidad') : 0;
            
            return response()->json(['totalItems' => $totalItems]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener contador del carrito: ' . $e->getMessage());
            return response()->json(['totalItems' => 0]);
        }
    }

    public function remove($itemId)
{
    try {
        // OPTIMIZACIÓN: Eliminación directa sin cargar relaciones innecesarias
        $deleted = CarritoItem::where('id', $itemId)
                             ->whereHas('carrito', function($query) {
                                 $query->where('user_id', Auth::id());
                             })
                             ->delete();

        if ($deleted) {
            return redirect()->route('carrito.index')
                           ->with('success', 'Producto eliminado del carrito');
        } else {
            return redirect()->route('carrito.index')
                           ->with('error', 'Producto no encontrado');
        }
        
    } catch (\Exception $e) {
        Log::error('Error al eliminar item del carrito: ' . $e->getMessage());
        return redirect()->route('carrito.index')
                       ->with('error', 'Error al eliminar el producto');
    }
}
    
    }