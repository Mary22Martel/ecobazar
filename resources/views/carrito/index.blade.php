@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50">
    <div class="container mx-auto px-3 py-4 max-w-6xl">
        
        <!-- Header optimizado -->
        <div class="text-green-600 p-4 sm:p-6 mb-6">
            <div class="text-center">
                <h1 class="text-3xl sm:text-3xl lg:text-4xl font-bold mb-2 flex items-center justify-center">
                    🛒 <span class="ml-2">Mi Carrito</span>
                </h1>
                <p class="text-gray-800 text-sm sm:text-base">
                    @if($carrito && $carrito->items->count())
                        {{ $carrito->items->count() }} {{ $carrito->items->count() == 1 ? 'producto' : 'productos' }} en tu carrito
                    @else
                        Tu carrito está vacío
                    @endif
                </p>
            </div>
        </div>

        @if($carrito && $carrito->items->count())
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Sección de productos - Optimizada para móvil -->
                <div class="lg:col-span-2 space-y-4">
                    
                    <!-- Vista móvil (cards mejoradas) -->
                    <div class="block lg:hidden space-y-4">
                        @foreach ($carrito->items as $item)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl" data-item-id="mobile-{{ $item->id }}">
                            <div class="p-4">
                                <!-- Header del producto optimizado -->
                                <div class="flex items-start space-x-3 mb-3">
                                    <div class="flex-shrink-0">
                                        @if($item->product->imagen)
                                            <img src="{{ asset('storage/' . $item->product->imagen) }}" 
                                                 alt="{{ $item->product->nombre }}" 
                                                 class="w-16 h-16 object-cover rounded-xl shadow-md">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center shadow-inner">
                                                <span class="text-2xl">🥬</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-bold text-gray-800 mb-1 truncate">{{ $item->product->nombre }}</h3>
                                        <div class="flex items-center justify-between">
                                            <span class="text-base font-bold text-green-600">S/ {{ number_format($item->product->precio, 2) }}</span>
                                            <form action="{{ route('carrito.remove', $item->id) }}" method="POST" class="inline remove-form">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Control de cantidad mejorado -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 mb-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 font-medium">Cantidad:</span>
                                        <div class="flex items-center justify-center space-x-1">
                                            <button type="button" class="bg-gray-100 text-gray-600 w-6 h-6 sm:w-7 sm:h-7 rounded-md flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="decrement" data-item-id="{{ $item->id }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" class="w-8 h-6 sm:h-7 text-xs sm:text-sm border rounded-md text-center item-quantity" data-item-id="{{ $item->id }}" readonly>
                                            <button type="button" class="bg-gray-100 text-gray-600 w-6 h-6 sm:w-7 sm:h-7 rounded-md flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="increment" data-item-id="{{ $item->id }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div class="flex justify-between items-center mx-3">
                                    <span class="text-sm text-gray-600 font-medium">Subtotal:</span>
                                    <span class="text-sm font-bold text-green-600 item-subtotal" data-item-id="{{ $item->id }}">S/ {{ number_format($item->product->precio * $item->cantidad, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Vista desktop (tabla optimizada) -->
                    <div class="hidden lg:block bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr class="text-gray-700">
                                        <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Producto</th>
                                        <th class="py-4 px-6 font-bold text-center border-b border-gray-200">Precio</th>
                                        <th class="py-4 px-6 font-bold text-center border-b border-gray-200">Cantidad</th>
                                        <th class="py-4 px-6 font-bold text-center border-b border-gray-200">Subtotal</th>
                                        <th class="py-4 px-6 font-bold text-center border-b border-gray-200">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($carrito->items as $item)
                                    <tr class="hover:bg-green-50 transition-colors" data-item-id="desktop-{{ $item->id }}">
                                        <td class="py-6 px-6">
                                            <div class="flex items-center space-x-4">
                                                @if($item->product->imagen)
                                                    <img src="{{ asset('storage/' . $item->product->imagen) }}" 
                                                         alt="{{ $item->product->nombre }}" 
                                                         class="w-16 h-16 object-cover rounded-xl shadow-md">
                                                @else
                                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center shadow-inner">
                                                        <span class="text-2xl">🥬</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm">{{ $item->product->nombre }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <span class="text-sm font-bold text-green-600">S/ {{ number_format($item->product->precio, 2) }}</span>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                           <div class="flex items-center justify-center space-x-1">
                                                <button type="button" class="bg-gray-100 text-gray-600 w-7 h-7 rounded-md flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="decrement" data-item-id="{{ $item->id }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" class="w-8 h-7 text-sm border rounded-md text-center item-quantity" data-item-id="{{ $item->id }}" readonly>
                                                <button type="button" class="bg-gray-100 text-gray-600 w-7 h-7 rounded-md flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="increment" data-item-id="{{ $item->id }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <span class="text-sm font-bold text-green-600 item-subtotal" data-item-id="{{ $item->id }}">S/ {{ number_format($item->product->precio * $item->cantidad, 2) }}</span>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <form action="{{ route('carrito.remove', $item->id) }}" method="POST" class="inline remove-form">
                                                @csrf
                                                <button type="submit" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resumen de compra optimizado -->
                <div class="bg-white rounded-2xl shadow-xl p-6 h-fit sticky top-6">
                    <h3 class="text-base sm:text-lg font-bold mb-6 text-gray-800 flex items-center">
                        <span class="mr-3">📋</span>
                        Resumen de compra
                    </h3>
                    
                    <div class="space-y-4 mb-6 ">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm mx-3 ">
                            <span class="text-gray-600 text-sm">Productos ({{ $carrito->items->count() }}):</span>
                            <span class="font-semibold text-base">{{ $carrito->items->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm mx-3">
                            <span class="text-gray-600 text-sm">Subtotal:</span>
                            <span class="text-sm font-bold" id="cart-subtotal">S/ {{ number_format($carrito->total(), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-t-2 border-green-200 bg-green-50 rounded-lg px-3">
                            <span class="text-base font-bold text-green-800">Total:</span>
                            <span class="text-base font-bold text-green-600" id="cart-total">S/ {{ number_format($carrito->total(), 2) }}</span>
                        </div>
                    </div>

                    <!-- Botones de acción mejorados -->
                    <div class="space-y-3">
                        <a href="{{ route('checkout') }}" 
                           class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-4 px-6 rounded-xl font-bold text-base hover:from-green-600 hover:to-green-700 transition-all duration-300 text-center inline-block shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                            💳 Proceder a Pagar
                        </a>
                        
                        <a href="{{ route('tienda') }}" 
                           class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-xl font-semibold text-center inline-block hover:bg-gray-200 text-base transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Seguir comprando
                        </a>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-800 font-semibold mb-1">📦 Información de entrega</p>
                                <p class="text-xs text-blue-700">Los productos se entregarán solo el día sábado para recojo o delivery.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Estado vacío mejorado -->
            <div class="bg-white border-2 border-dashed border-green-200 rounded-2xl p-8 sm:p-12 text-center shadow-lg transition-all duration-300 hover:shadow-xl">
                <div class="max-w-md mx-auto">
                    <div class="text-5xl sm:text-6xl mb-6 animate-bounce">🛒</div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">Tu carrito está vacío</h2>
                    <p class="text-gray-600 mb-8 text-base sm:text-lg leading-relaxed">
                        ¡Descubre productos frescos y locales de nuestros agricultores!
                    </p>
                    <a href="{{ route('tienda') }}" 
                       class="inline-flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Explorar productos
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    // Optimización: Pre-cache de elementos
    const cartSubtotalEl = document.getElementById('cart-subtotal');
    const cartTotalEl = document.getElementById('cart-total');
    
    // Función optimizada para actualizar cantidad
    async function updateQuantity(itemId, action) {
        // Buscar tanto en vista móvil como escritorio
        const mobileItem = document.querySelector(`[data-item-id="mobile-${itemId}"]`);
        const desktopItem = document.querySelector(`[data-item-id="desktop-${itemId}"]`);
        
        // Obtener elementos de cantidad por data-item-id específico
        const quantityInputs = document.querySelectorAll(`.item-quantity[data-item-id="${itemId}"]`);
        const subtotalElements = document.querySelectorAll(`.item-subtotal[data-item-id="${itemId}"]`);
        
        if (quantityInputs.length === 0) return;
        
        let quantity = parseInt(quantityInputs[0].value);
        
        if (action === 'increment') {
            quantity++;
        } else if (action === 'decrement' && quantity > 1) {
            quantity--;
        } else if (action === 'decrement' && quantity === 1) {
            removeItem(itemId);
            return;
        }
        
        // Feedback visual inmediato en ambas vistas
        quantityInputs.forEach(input => input.value = quantity);
        [mobileItem, desktopItem].forEach(item => {
            if (item) {
                item.classList.add('opacity-75', 'cursor-wait');
            }
        });
        
        try {
            const response = await fetch(`/carrito/actualizar/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ cantidad: quantity })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar subtotales en ambas vistas
                subtotalElements.forEach(el => {
                    el.textContent = `S/ ${data.itemSubtotal.toFixed(2)}`;
                });
                
                // Actualizar totales del carrito
                if (cartSubtotalEl) cartSubtotalEl.textContent = `S/ ${data.cartTotal.toFixed(2)}`;
                if (cartTotalEl) cartTotalEl.textContent = `S/ ${data.cartTotal.toFixed(2)}`;
                
                // Feedback visual
                [mobileItem, desktopItem].forEach(item => {
                    if (item) {
                        item.classList.remove('opacity-75', 'cursor-wait');
                        item.classList.add('bg-green-50');
                        setTimeout(() => item.classList.remove('bg-green-50'), 1000);
                    }
                });
                
                // Actualizar badge
                updateCartBadge();
            } else {
                // Revertir cambios
                const revertedQuantity = action === 'increment' ? quantity - 1 : quantity + 1;
                quantityInputs.forEach(input => input.value = revertedQuantity);
                showError(data.error || 'Error al actualizar');
                
                [mobileItem, desktopItem].forEach(item => {
                    if (item) item.classList.remove('opacity-75', 'cursor-wait');
                });
            }
        } catch (error) {
            console.error('Error:', error);
            const revertedQuantity = action === 'increment' ? quantity - 1 : quantity + 1;
            quantityInputs.forEach(input => input.value = revertedQuantity);
            showError('Error de conexión');
            
            [mobileItem, desktopItem].forEach(item => {
                if (item) item.classList.remove('opacity-75', 'cursor-wait');
            });
        }
    }

    // Función optimizada para eliminar item
    async function removeItem(itemId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) return;
        
        const mobileItem = document.querySelector(`[data-item-id="mobile-${itemId}"]`);
        const desktopItem = document.querySelector(`[data-item-id="desktop-${itemId}"]`);
        const cartItems = [mobileItem, desktopItem].filter(item => item !== null);
        
        cartItems.forEach(item => {
            item.classList.add('opacity-50', 'pointer-events-none');
        });
        
        try {
            const response = await fetch(`/carrito/eliminar/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Eliminar elementos del DOM con animación
                cartItems.forEach(item => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.maxHeight = `${item.offsetHeight}px`;
                    item.style.opacity = '0';
                    
                    setTimeout(() => {
                        item.style.maxHeight = '0';
                        item.style.marginBottom = '0';
                        item.style.padding = '0';
                        item.style.border = '0';
                        
                        setTimeout(() => {
                            item.remove();
                            
                            // Actualizar totales
                            if (cartSubtotalEl) cartSubtotalEl.textContent = `S/ ${data.cartTotal.toFixed(2)}`;
                            if (cartTotalEl) cartTotalEl.textContent = `S/ ${data.cartTotal.toFixed(2)}`;
                            
                            // Si no quedan items, recargar
                            if (!document.querySelector('[data-item-id^="mobile-"], [data-item-id^="desktop-"]')) {
                                location.reload();
                            }
                            
                            // Actualizar badge
                            updateCartBadge();
                        }, 300);
                    }, 10);
                });
            } else {
                showError(data.error || 'Error al eliminar');
                cartItems.forEach(item => {
                    item.classList.remove('opacity-50', 'pointer-events-none');
                });
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error de conexión');
            cartItems.forEach(item => {
                item.classList.remove('opacity-50', 'pointer-events-none');
            });
        }
    }

    // Función para actualizar badge
    async function updateCartBadge() {
        try {
            const response = await fetch('/carrito/count');
            const data = await response.json();
            
            const badges = [
                document.getElementById('cart-badge'),
                document.getElementById('floating-cart-badge'),
                document.getElementById('cart-badge-mobile')
            ];
            
            badges.forEach(badge => {
                if (badge) badge.textContent = data.totalItems || 0;
            });
        } catch (error) {
            console.error('Error al actualizar badge:', error);
        }
    }

    // Función para mostrar errores (optimizada)
    function showError(message) {
        const existingError = document.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 animate-fadeIn';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.classList.add('animate-fadeOut');
            setTimeout(() => errorDiv.remove(), 300);
        }, 3000);
    }

    // Event delegation para mejor rendimiento
    document.addEventListener('click', function(e) {
        const updateBtn = e.target.closest('.update-quantity');
        if (updateBtn) {
            e.preventDefault();
            const action = updateBtn.getAttribute('data-action');
            const itemId = updateBtn.getAttribute('data-item-id');
            updateQuantity(itemId, action);
        }
        
        const removeForm = e.target.closest('.remove-form');
        if (removeForm) {
            e.preventDefault();
            // Extraer el ID del item desde la URL del formulario
            const actionUrl = removeForm.getAttribute('action');
            const itemId = actionUrl.split('/').pop();
            removeItem(itemId);
        }
    });
});
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-20px); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }
    .animate-fadeOut { animation: fadeOut 0.3s ease forwards; }
</style>
@endsection