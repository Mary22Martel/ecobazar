{{-- resources/views/layouts/partials/cart-modal.blade.php --}}
<div id="cart-summary" class="fixed hidden right-4 top-20 w-80 md:w-96 bg-white shadow-xl rounded-lg z-50 border">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Carrito de Compras</h3>
            <button onclick="$('#cart-summary').addClass('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Cart Items List -->
        <div id="cart-items-list" class="max-h-64 overflow-y-auto">
            <!-- Los productos se cargarán dinámicamente aquí -->
        </div>
        
        <!-- Empty Cart Message -->
        <div id="empty-cart-message" class="text-center py-8 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.68 9.74a2 2 0 01-1.99 1.76H6.67a2 2 0 01-1.99-1.76L3 3z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            <p class="text-sm">Tu carrito está vacío</p>
            <a href="{{ route('tienda') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                Explorar productos
            </a>
        </div>
        
        <!-- Cart Footer -->
        <div class="border-t pt-4 mt-4">
            <div class="flex items-center justify-between mb-4">
                <span class="font-bold text-gray-800">Total:</span>
                <span class="font-bold text-green-600 text-lg">S/<span id="cart-popup-total-price">0.00</span></span>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-2">
                <a href="{{ route('carrito.index') }}" 
                   class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center block transition-colors font-medium">
                    Ver Carrito Completo
                </a>
                <button onclick="$('#cart-summary').addClass('hidden')" 
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    Continuar Comprando
                </button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="cart-loading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
        <div class="flex items-center space-x-2">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
            <span class="text-gray-600">Cargando...</span>
        </div>
    </div>
</div>