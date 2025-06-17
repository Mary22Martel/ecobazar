<!-- Cart Modal Component -->
<div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                Mi Carrito
            </h3>
            <button id="cart-close" 
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto max-h-80">
            <div id="cart-items-container" class="p-4">
                
                <!-- Loading State -->
                <div id="cart-loading" class="text-center py-8 hidden">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-3"></div>
                    <p class="text-sm text-gray-600">Cargando carrito...</p>
                </div>
                
                <!-- Empty State -->
                <div id="cart-empty" class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                    </div>
                    <h4 class="font-semibold text-gray-700 mb-2">Tu carrito está vacío</h4>
                    <p class="text-sm text-gray-500 mb-4">¡Descubre nuestros productos frescos!</p>
                    <button onclick="closeCartModal()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-seedling mr-2"></i>
                        Explorar productos
                    </button>
                </div>
                
                <!-- Items List -->
                <div id="cart-items-list" class="space-y-3">
                    <!-- Dynamic cart items will be inserted here -->
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div id="cart-footer" class="border-t border-gray-200 p-4 bg-gray-50 hidden">
            
            <!-- Total -->
            <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-4">
                <span>Total:</span>
                <span class="text-green-600">
                    S/<span id="cart-total-amount">0.00</span>
                </span>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-2">
                <a href="{{ route('carrito.index') }}" 
                   class="block w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-center py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Ver Carrito Completo
                </a>
                
                <button id="continue-shopping" 
                        class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-center py-3 rounded-lg font-semibold transition-colors duration-200">
                    Continuar Comprando
                </button>
            </div>
            
            <!-- Delivery Info -->
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center text-blue-700">
                    <i class="fas fa-truck mr-2 text-sm"></i>
                    <span class="text-xs font-medium">Entrega disponible los sábados</span>
                </div>
                <p class="text-xs text-blue-600 mt-1">
                    Punto Verde Agroecológico - Amarilis
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initCartModal();
});

function initCartModal() {
    const cartModal = document.getElementById('cart-modal');
    const cartClose = document.getElementById('cart-close');
    const continueShoppingBtn = document.getElementById('continue-shopping');

    // Close modal events
    if (cartClose) {
        cartClose.addEventListener('click', closeCartModal);
    }
    
    if (continueShoppingBtn) {
        continueShoppingBtn.addEventListener('click', closeCartModal);
    }
    
    // Close on background click
    if (cartModal) {
        cartModal.addEventListener('click', function(e) {
            if (e.target === cartModal) {
                closeCartModal();
            }
        });
    }
    
    // FAB cart button
    const fabCart = document.getElementById('fab-cart');
    if (fabCart) {
        fabCart.addEventListener('click', openCartModal);
    }

    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !cartModal.classList.contains('hidden')) {
            closeCartModal();
        }
    });
}

function openCartModal() {
    const cartModal = document.getElementById('cart-modal');
    if (cartModal) {
        cartModal.classList.remove('hidden');
        cartModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        loadCartItems();
    }
}

function closeCartModal() {
    const cartModal = document.getElementById('cart-modal');
    if (cartModal) {
        cartModal.classList.add('hidden');
        cartModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
}

function loadCartItems() {
    const cartLoading = document.getElementById('cart-loading');
    const cartEmpty = document.getElementById('cart-empty');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartFooter = document.getElementById('cart-footer');
    const cartTotalAmount = document.getElementById('cart-total-amount');

    // Show loading state
    if (cartLoading) cartLoading.classList.remove('hidden');
    if (cartEmpty) cartEmpty.classList.add('hidden');
    if (cartFooter) cartFooter.classList.add('hidden');
    if (cartItemsList) cartItemsList.innerHTML = '';

    // Make AJAX call to get cart details
    fetch('{{ route("carrito.getDetails") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Hide loading
        if (cartLoading) cartLoading.classList.add('hidden');
        
        if (data.success && data.items && data.items.length > 0) {
            // Show items and footer
            if (cartEmpty) cartEmpty.classList.add('hidden');
            if (cartFooter) cartFooter.classList.remove('hidden');
            
            // Populate items
            displayCartItems(data.items);
            
            // Update total
            if (cartTotalAmount) {
                cartTotalAmount.textContent = data.totalPrice;
            }
        } else {
            // Show empty state
            if (cartEmpty) cartEmpty.classList.remove('hidden');
            if (cartFooter) cartFooter.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        
        // Hide loading and show empty state
        if (cartLoading) cartLoading.classList.add('hidden');
        if (cartEmpty) cartEmpty.classList.remove('hidden');
        if (cartFooter) cartFooter.classList.add('hidden');
        
        // Show error message
        if (cartEmpty) {
            cartEmpty.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                    </div>
                    <h4 class="font-semibold text-gray-700 mb-2">Error al cargar el carrito</h4>
                    <p class="text-sm text-gray-500 mb-4">Ocurrió un error al cargar tu carrito. Por favor, intenta nuevamente.</p>
                    <button onclick="loadCartItems()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-refresh mr-2"></i>
                        Reintentar
                    </button>
                </div>
            `;
        }
    });
}

function displayCartItems(items) {
    const cartItemsList = document.getElementById('cart-items-list');
    if (!cartItemsList) return;

    const itemsHtml = items.map(item => `
        <div class="flex items-center p-3 bg-gray-50 rounded-lg cart-item" data-item-id="${item.id}">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                ${item.imagen_url && item.imagen_url !== '{{ asset("images/default-product.png") }}' ? 
                    `<img src="${item.imagen_url}" alt="${item.nombre}" class="w-full h-full object-cover">` :
                    `<i class="fas fa-seedling text-green-600"></i>`
                }
            </div>
            <div class="flex-1 ml-3 min-w-0">
                <h4 class="font-medium text-gray-800 text-sm truncate">${item.nombre}</h4>
                <p class="text-xs text-gray-500">Cantidad: ${item.cantidad}</p>
                <p class="text-sm font-bold text-green-600">S/${parseFloat(item.subtotal).toFixed(2)}</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="updateCartItemQuantity(${item.id}, ${item.cantidad - 1})" 
                        class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded text-xs flex items-center justify-center transition-colors ${item.cantidad <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${item.cantidad <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-minus"></i>
                </button>
                <span class="text-sm font-medium w-8 text-center">${item.cantidad}</span>
                <button onclick="updateCartItemQuantity(${item.id}, ${item.cantidad + 1})" 
                        class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded text-xs flex items-center justify-center transition-colors ${item.cantidad >= item.stock_disponible ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${item.cantidad >= item.stock_disponible ? 'disabled' : ''}>
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <button onclick="removeCartItem(${item.id})" 
                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200 ml-2">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </div>
    `).join('');

    cartItemsList.innerHTML = itemsHtml;
}

function updateCartItemQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeCartItem(itemId);
        return;
    }

    // Show loading state for this item
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.style.opacity = '0.5';
    }

    fetch(`{{ url('/carrito/actualizar') }}/${itemId}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            cantidad: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload cart to update all totals
            loadCartItems();
            // Update cart badge
            updateCartBadge();
        } else {
            // Show error and restore opacity
            if (itemElement) {
                itemElement.style.opacity = '1';
            }
            alert(data.error || 'Error al actualizar cantidad');
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        if (itemElement) {
            itemElement.style.opacity = '1';
        }
        alert('Error al actualizar cantidad');
    });
}

function removeCartItem(itemId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        return;
    }

    // Show loading state for this item
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.style.opacity = '0.5';
    }

    fetch(`{{ url('/carrito/eliminar') }}/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            // Reload cart to update all totals
            loadCartItems();
            // Update cart badge
            updateCartBadge();
        } else {
            throw new Error('Error al eliminar producto');
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        if (itemElement) {
            itemElement.style.opacity = '1';
        }
        alert('Error al eliminar producto');
    });
}
</script>