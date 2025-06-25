// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
    initializeQuickView();
});

function initializeCart() {
    // Quantity controls
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quantity-btn')) {
            handleQuantityChange(e);
        }
    });

    // Add to cart forms
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('add-to-cart-form')) {
            handleAddToCart(e);
        }
    });

    // Quick view buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quick-view-btn')) {
            const btn = e.target.closest('.quick-view-btn');
            const productId = btn.getAttribute('data-product-id');
            handleQuickView(productId);
        }
    });
}

function handleQuantityChange(e) {
    const btn = e.target.closest('.quantity-btn');
    const form = btn.closest('form');
    const input = form.querySelector('.quantity-input');
    const totalPriceElement = form.querySelector('.total-price');
    const unitPrice = parseFloat(totalPriceElement.getAttribute('data-unit-price'));
    
    let currentValue = parseInt(input.value) || 1;
    const maxValue = parseInt(input.getAttribute('max'));
    const minValue = parseInt(input.getAttribute('min'));
    
    if (btn.classList.contains('plus-btn') && currentValue < maxValue) {
        currentValue++;
    } else if (btn.classList.contains('minus-btn') && currentValue > minValue) {
        currentValue--;
    }
    
    input.value = currentValue;
    
    // Update total price
    const totalPrice = unitPrice * currentValue;
    totalPriceElement.textContent = 'S/' + totalPrice.toFixed(2);
    
    // Update button states
    const minusBtn = form.querySelector('.minus-btn');
    const plusBtn = form.querySelector('.plus-btn');
    
    minusBtn.disabled = currentValue <= minValue;
    plusBtn.disabled = currentValue >= maxValue;
}

function handleAddToCart(e) {
    e.preventDefault();
    
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = `
        <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            <span>Agregando...</span>
        </div>
    `;

    // Get form data
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge if it exists
            updateCartBadge();
            
            // Show success message
            showNotification('¡Producto agregado al carrito!', 'success');
            
            // Reset form
            form.querySelector('.quantity-input').value = 1;
            const totalPrice = parseFloat(form.querySelector('.total-price').getAttribute('data-unit-price'));
            form.querySelector('.total-price').textContent = 'S/' + totalPrice.toFixed(2);
        } else {
            showNotification(data.error || 'Error al agregar producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = 'Error al agregar el producto';
        
        if (error.status === 401) {
            errorMessage = 'Debes iniciar sesión para agregar productos';
        }
        
        showNotification(errorMessage, 'error');
    })
    .finally(() => {
        // Restore button
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 1000);
    });
}

function handleQuickView(productId) {
    // Quick view functionality - to be implemented
    console.log('Quick view for product:', productId);
    
    // Example implementation:
    showNotification('Vista rápida próximamente disponible', 'info');
    
    // Future implementation could include:
    /*
    fetch(`/producto/${productId}`)
        .then(response => response.text())
        .then(html => {
            // Show modal with product details
            showQuickViewModal(html);
        })
        .catch(error => {
            console.error('Error loading product:', error);
        });
    */
}

function updateCartBadge() {
    fetch('/carrito/details', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const badges = document.querySelectorAll('#fab-cart-badge, [data-cart-badge]');
        badges.forEach(badge => {
            if (badge) {
                badge.textContent = data.totalItems > 9 ? '9+' : data.totalItems;
                badge.style.display = data.totalItems > 0 ? 'flex' : 'none';
            }
        });
    })
    .catch(error => {
        console.error('Error updating cart badge:', error);
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white text-sm font-medium transform transition-all duration-300 translate-x-full ${getNotificationClass(type)}`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function getNotificationClass(type) {
    switch(type) {
        case 'success': return 'bg-green-500';
        case 'error': return 'bg-red-500';
        case 'warning': return 'bg-yellow-500';
        default: return 'bg-blue-500';
    }
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function initializeQuickView() {
    // Initialize quick view functionality
    // This can be expanded in the future
}

// Export functions for use in other scripts
window.CartManager = {
    updateCartBadge,
    showNotification,
    handleQuickView
};