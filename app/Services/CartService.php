<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CartService
{
    /**
     * Obtiene el resumen del carrito del usuario autenticado
     */
    public function getCartSummary(): array
    {
        if (!Auth::check()) {
            return [
                'totalItems' => 0,
                'totalPrice' => 0.00,
                'items' => []
            ];
        }

        $cacheKey = 'cart_summary_' . Auth::id();
        
        return Cache::remember($cacheKey, 300, function () {
            $carrito = Carrito::where('user_id', Auth::id())
                ->with(['items.product'])
                ->first();

            if (!$carrito || $carrito->items->isEmpty()) {
                return [
                    'totalItems' => 0,
                    'totalPrice' => 0.00,
                    'items' => []
                ];
            }

            $totalItems = $carrito->items->sum('cantidad');
            $totalPrice = $carrito->items->sum(function ($item) {
                return $item->product->precio * $item->cantidad;
            });

            $items = $carrito->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->product->nombre,
                    'precio' => $item->product->precio,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $item->product->precio * $item->cantidad,
                    'imagen' => $item->product->imagen_url ?? null,
                    'product_id' => $item->product->id
                ];
            });

            return [
                'totalItems' => $totalItems,
                'totalPrice' => $totalPrice,
                'items' => $items->toArray()
            ];
        });
    }

    /**
     * Añade un producto al carrito
     */
    public function addToCart(int $productId, int $cantidad = 1): array
    {
        if (!Auth::check()) {
            throw new \Exception('Usuario no autenticado');
        }

        $carrito = Carrito::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $carritoItem = CarritoItem::where('carrito_id', $carrito->id)
            ->where('product_id', $productId)
            ->first();

        if ($carritoItem) {
            $carritoItem->cantidad += $cantidad;
            $carritoItem->save();
        } else {
            CarritoItem::create([
                'carrito_id' => $carrito->id,
                'product_id' => $productId,
                'cantidad' => $cantidad
            ]);
        }

        $this->clearCartCache();
        
        return $this->getCartSummary();
    }

    /**
     * Elimina un item del carrito
     */
    public function removeFromCart(int $itemId): array
    {
        if (!Auth::check()) {
            throw new \Exception('Usuario no autenticado');
        }

        $carrito = Carrito::where('user_id', Auth::id())->first();
        
        if ($carrito) {
            CarritoItem::where('id', $itemId)
                ->where('carrito_id', $carrito->id)
                ->delete();
        }

        $this->clearCartCache();
        
        return $this->getCartSummary();
    }

    /**
     * Actualiza la cantidad de un item en el carrito
     */
    public function updateCartItem(int $itemId, int $cantidad): array
    {
        if (!Auth::check()) {
            throw new \Exception('Usuario no autenticado');
        }

        if ($cantidad <= 0) {
            return $this->removeFromCart($itemId);
        }

        $carrito = Carrito::where('user_id', Auth::id())->first();
        
        if ($carrito) {
            CarritoItem::where('id', $itemId)
                ->where('carrito_id', $carrito->id)
                ->update(['cantidad' => $cantidad]);
        }

        $this->clearCartCache();
        
        return $this->getCartSummary();
    }

    /**
     * Vacía completamente el carrito
     */
    public function clearCart(): void
    {
        if (!Auth::check()) {
            return;
        }

        $carrito = Carrito::where('user_id', Auth::id())->first();
        
        if ($carrito) {
            $carrito->items()->delete();
        }

        $this->clearCartCache();
    }

    /**
     * Limpia el cache del carrito
     */
    private function clearCartCache(): void
    {
        if (Auth::check()) {
            Cache::forget('cart_summary_' . Auth::id());
        }
    }

    /**
     * Obtiene el carrito completo con detalles para checkout
     */
    public function getCartForCheckout(): array
    {
        if (!Auth::check()) {
            return [
                'items' => [],
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0
            ];
        }

        $cartSummary = $this->getCartSummary();
        $subtotal = $cartSummary['totalPrice'];
        $tax = $subtotal * 0.18; // IGV 18%
        $total = $subtotal + $tax;

        return [
            'items' => $cartSummary['items'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'itemCount' => $cartSummary['totalItems']
        ];
    }

    /**
     * Valida si el carrito tiene items disponibles
     */
    public function validateCartAvailability(): array
    {
        $cartSummary = $this->getCartSummary();
        $unavailableItems = [];
        $availableItems = [];

        foreach ($cartSummary['items'] as $item) {
            // Aquí puedes agregar lógica para verificar stock, disponibilidad, etc.
            $product = \App\Models\Product::find($item['product_id']);
            
            if (!$product || !$product->activo) {
                $unavailableItems[] = $item;
            } else {
                $availableItems[] = $item;
            }
        }

        return [
            'isValid' => empty($unavailableItems),
            'availableItems' => $availableItems,
            'unavailableItems' => $unavailableItems,
            'message' => empty($unavailableItems) 
                ? 'Todos los productos están disponibles' 
                : 'Algunos productos no están disponibles'
        ];
    }
}