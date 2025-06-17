@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50">
    <div class="container mx-auto px-3 py-4 max-w-6xl">
        
        <!-- Header responsivo -->
        <div class=" text-green-600 p-4 sm:p-6 mb-6 ">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2 flex items-center justify-center">
                    🛒 <span class="ml-2">Mi Carrito</span>
                </h1>
                <p class="text-green-100 text-sm sm:text-base">
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
                
                <!-- Items del carrito - Vista móvil y desktop -->
                <div class="lg:col-span-2 space-y-4">
                    
                    <!-- Vista móvil (cards) -->
                    <div class="block lg:hidden space-y-4">
                        @foreach ($carrito->items as $item)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden" data-item-id="{{ $item->id }}">
                            <div class="p-4">
                                <!-- Header del producto -->
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
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $item->product->nombre }}</h3>
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-green-600">S/ {{ number_format($item->product->precio, 2) }}</span>
                                            <form action="{{ route('carrito.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Control de cantidad -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 mb-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 font-medium">Cantidad:</span>
                                        <div class="flex items-center space-x-3">
                                            <button type="button" class="bg-white text-gray-600 w-8 h-8 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition-shadow update-quantity" data-action="decrement">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" class="w-12 h-8 border rounded-lg text-center item-quantity bg-white shadow-sm" readonly>
                                            <button type="button" class="bg-white text-gray-600 w-8 h-8 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition-shadow update-quantity" data-action="increment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 font-medium">Subtotal:</span>
                                    <span class="text-lg font-bold text-green-600 item-subtotal">S/ {{ number_format($item->product->precio * $item->cantidad, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Vista desktop (tabla) -->
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
                                    <tr class="hover:bg-green-50 transition-colors" data-item-id="{{ $item->id }}">
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
                                            <div class="flex items-center justify-center space-x-3">
                                                <button type="button" class="bg-gray-100 text-gray-600 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="decrement">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" class="w-12 h-8 border rounded-lg text-center item-quantity" readonly>
                                                <button type="button" class="bg-gray-100 text-gray-600 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors update-quantity" data-action="increment">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <span class="text-sm font-bold text-green-600 item-subtotal">S/ {{ number_format($item->product->precio * $item->cantidad, 2) }}</span>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <form action="{{ route('carrito.remove', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-colors shadow-md hover:shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Resumen de compra -->
                <div class="bg-white rounded-2xl shadow-xl p-6 h-fit sticky top-6">
                    <h3 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <span class="mr-3">📋</span>
                        Resumen de compra
                    </h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Productos ({{ $carrito->items->count() }}):</span>
                            <span class="font-semibold">{{ $carrito->items->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-lg font-bold" id="cart-subtotal">S/ {{ number_format($carrito->total(), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-t-2 border-green-200 bg-green-50 rounded-lg px-3">
                            <span class="text-xl font-bold text-green-800">Total:</span>
                            <span class="text-xl font-bold text-green-600" id="cart-total">S/ {{ number_format($carrito->total(), 2) }}</span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="space-y-3">
                        <a href="{{ route('checkout') }}" 
                           class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-xl font-bold text-lg hover:from-green-600 hover:to-green-700 transition-all text-center inline-block shadow-lg hover:shadow-xl transform hover:scale-105">
                            💳 Proceder a Pagar
                        </a>
                        
                        <a href="{{ route('tienda') }}" 
                           class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-xl font-semibold text-center inline-block hover:bg-gray-200 transition-colors flex items-center justify-center">
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
                                <p class="text-xs text-blue-700">Los productos se entregarán el día sábado en la feria Punto Verde Agroecológico.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Estado vacío -->
            <div class="bg-white border-2 border-dashed border-green-200 rounded-2xl p-8 sm:p-12 text-center shadow-lg">
                <div class="max-w-md mx-auto">
                    <div class="text-5xl sm:text-6xl mb-6 animate-bounce">🛒</div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">Tu carrito está vacío</h2>
                    <p class="text-gray-600 mb-8 text-base sm:text-lg leading-relaxed">
                        ¡Descubre productos frescos y locales de nuestros agricultores!
                    </p>
                    <a href="{{ route('tienda') }}" 
                       class="inline-flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg hover:shadow-xl">
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
        const updateButtons = document.querySelectorAll('.update-quantity');

        updateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const row = this.closest('[data-item-id]');
                const quantityInput = row.querySelector('.item-quantity');
                let quantity = parseInt(quantityInput.value);
                const itemId = row.getAttribute('data-item-id');

                // Increment or decrement quantity
                if (action === 'increment') {
                    quantity++;
                } else if (action === 'decrement' && quantity > 1) {
                    quantity--;
                }

                // Update the input value
                quantityInput.value = quantity;

                // Show loading state
                button.disabled = true;
                button.style.opacity = '0.5';

                // Make AJAX request to update the quantity in the backend
                fetch(`/carrito/actualizar/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cantidad: quantity })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data) {
                        // Update subtotal for the item
                        row.querySelector('.item-subtotal').textContent = `S/ ${data.itemSubtotal.toFixed(2)}`;

                        // Update cart subtotal and total
                        document.getElementById('cart-subtotal').textContent = `S/ ${data.cartSubtotal.toFixed(2)}`;
                        document.getElementById('cart-total').textContent = `S/ ${data.cartTotal.toFixed(2)}`;
                        
                        // Show success feedback
                        row.style.backgroundColor = '#f0fdf4';
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert quantity on error
                    quantityInput.value = action === 'increment' ? quantity - 1 : quantity + 1;
                    
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    errorDiv.textContent = 'Error al actualizar el carrito';
                    document.body.appendChild(errorDiv);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                })
                .finally(() => {
                    // Restore button state
                    button.disabled = false;
                    button.style.opacity = '1';
                });
            });
        });
    });
</script>
@endsection