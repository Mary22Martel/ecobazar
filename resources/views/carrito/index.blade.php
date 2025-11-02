@extends('layouts.app')
@php
    use App\Helpers\HorarioHelper;
    $tiendaAbierta = HorarioHelper::tiendaAbierta();
    $mensajeCierre = HorarioHelper::mensajeCierre();
@endphp

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
        <!-- Alertas de Stock -->
            @if(session('stock_error'))
            <div class="mb-6">
                <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-red-800 mb-2">⚠️ Productos sin stock suficiente</h3>
                            <div class="space-y-2">
                                @foreach(session('stock_error') as $problema)
                                <div class="bg-white rounded-lg p-3 border border-red-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <p class="font-semibold text-red-900 text-sm">{{ $problema['nombre'] }}</p>
                                            <p class="text-xs text-red-700">
                                                Solicitaste: <span class="font-semibold">{{ $problema['cantidad_solicitada'] }} unidades</span>
                                            </p>
                                            <p class="text-xs text-red-600">
                                                Stock disponible: <span class="font-semibold">{{ $problema['stock_disponible'] }} unidades</span>
                                            </p>
                                        </div>
                                      
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-3 p-2 bg-red-100 rounded-md">
                                <p class="text-xs text-red-800">
                                    <strong>💡 Tip:</strong> Ajusta las cantidades o elimina los productos para continuar con tu compra.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
                                            <button type="button" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110 remove-item-btn" data-item-id="{{ $item->id }}" data-product-name="{{ $item->product->nombre }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
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
                                            <button type="button" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110 remove-item-btn" data-item-id="{{ $item->id }}" data-product-name="{{ $item->product->nombre }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
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
                            <span class="text-gray-600 text-sm">Productos:</span>
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

        
                    <!-- En lugar del botón normal de checkout -->
                        @if($tiendaAbierta)
                        <!-- Botón normal de checkout -->
                        <a href="{{ route('checkout') }}" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span>Proceder al pago</span>
                        </a>
                    @else
                        <!-- Botón desactivado fuera de horario -->
                        <button disabled class="w-full bg-gray-400 text-gray-600 font-bold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Cerrado temporalmente</span>
                        </button>
                        <p class="text-center text-xs text-gray-600 mt-2">
                            {{ strip_tags($mensajeCierre) }}
                        </p>
                        @endif

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
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            // Usar SweetAlert para confirmar eliminación cuando la cantidad llega a 0
            const productName = document.querySelector(`[data-item-id*="${itemId}"]`)?.querySelector('h3, p')?.textContent || 'este producto';
            
            Swal.fire({
                title: '¿Eliminar producto?',
                text: `¿Estás seguro de que quieres eliminar "${productName}" del carrito?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 py-3 font-semibold',
                    cancelButton: 'rounded-lg px-6 py-3 font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    removeItem(itemId);
                }
            });
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

    // Función para mostrar confirmación con SweetAlert antes de eliminar
    function confirmRemoveItem(itemId, productName) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: `¿Estás seguro de que quieres eliminar "${productName}" del carrito?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3 font-semibold',
                cancelButton: 'rounded-lg px-6 py-3 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                removeItem(itemId);
            }
        });
    }

    // Función optimizada para eliminar item
    async function removeItem(itemId) {
        const mobileItem = document.querySelector(`[data-item-id="mobile-${itemId}"]`);
        const desktopItem = document.querySelector(`[data-item-id="desktop-${itemId}"]`);
        const cartItems = [mobileItem, desktopItem].filter(item => item !== null);
        
        // Mostrar loading en SweetAlert
        Swal.fire({
            title: 'Eliminando producto...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        cartItems.forEach(item => {
            item.classList.add('opacity-50', 'pointer-events-none');
        });
        
        try {
            const response = await fetch(`/carrito/eliminar-ajax/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Cerrar el loading y mostrar éxito
                Swal.fire({
                    title: '¡Eliminado!',
                    text: 'El producto ha sido eliminado del carrito.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
                
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
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                            
                            // Actualizar badge
                            updateCartBadge();
                        }, 300);
                    }, 10);
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Error al eliminar el producto',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-6 py-3 font-semibold'
                    }
                });
                
                cartItems.forEach(item => {
                    item.classList.remove('opacity-50', 'pointer-events-none');
                });
            }
        } catch (error) {
            console.error('Error:', error);
            
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Inténtalo de nuevo.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 py-3 font-semibold'
                }
            });
            
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
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3 font-semibold'
            }
        });
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
        
        const removeBtn = e.target.closest('.remove-item-btn');
        if (removeBtn) {
            e.preventDefault();
            const itemId = removeBtn.getAttribute('data-item-id');
            const productName = removeBtn.getAttribute('data-product-name');
            confirmRemoveItem(itemId, productName);
        }
    });

    // Función para ajustar cantidad automáticamente
window.ajustarCantidad = async function(itemId, stockDisponible) {
    if (stockDisponible === 0) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: 'Este producto no tiene stock disponible. ¿Deseas eliminarlo del carrito?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                removeItem(itemId);
            }
        });
        return;
    }

    try {
        const response = await fetch(`/carrito/actualizar/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ cantidad: stockDisponible })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: '¡Cantidad ajustada!',
                text: `La cantidad ha sido ajustada a ${stockDisponible} unidades.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            }).then(() => {
                location.reload();
            });
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al ajustar la cantidad');
    }
}
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

    /* Estilos personalizados para SweetAlert2 */
    .swal2-popup {
        font-family: inherit !important;
    }
    
    .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
    }
    
    .swal2-content {
        font-size: 1rem !important;
    }
    
    .swal2-confirm {
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    
    .swal2-cancel {
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    
    .swal2-confirm:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4) !important;
    }
    
    .swal2-cancel:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4) !important;
    }
</style>
@endsection