// Product Card JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeProductCards();
});

function initializeProductCards() {
    // Solo procesar las cards que no han sido procesadas
    const productCards = document.querySelectorAll('.add-to-cart-form:not([data-processed])');
    
    productCards.forEach(function(form) {
        form.setAttribute('data-processed', 'true');
        
        const quantityInput = form.querySelector('.quantity-input');
        const minusBtn = form.querySelector('.minus-btn');
        const plusBtn = form.querySelector('.plus-btn');
        const totalPriceElement = form.querySelector('.total-price');
        const unitPrice = parseFloat(totalPriceElement.dataset.unitPrice);
        const maxQuantity = parseInt(quantityInput.getAttribute('max'));
        
        // Función para actualizar el precio total
        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value) || 1;
            const total = unitPrice * quantity;
            totalPriceElement.textContent = 'S/' + total.toFixed(2);
            
            // Actualizar estado de botones
            minusBtn.disabled = quantity <= 1;
            plusBtn.disabled = quantity >= maxQuantity;
        }
        
        // Event listeners para botones de cantidad
        minusBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let currentValue = parseInt(quantityInput.value) || 1;
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                updateTotalPrice();
            }
        });
        
        plusBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let currentValue = parseInt(quantityInput.value) || 1;
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
                updateTotalPrice();
            }
        });
        
        // Event listener para input directo
        quantityInput.addEventListener('input', function() {
            let value = parseInt(this.value) || 1;
            
            // Validar límites
            if (value < 1) {
                this.value = 1;
            } else if (value > maxQuantity) {
                this.value = maxQuantity;
            }
            
            updateTotalPrice();
        });
        
        // Event listener para cambio de foco (blur)
        quantityInput.addEventListener('blur', function() {
            if (this.value === '' || parseInt(this.value) < 1) {
                this.value = 1;
                updateTotalPrice();
            }
        });
        
        // Inicializar estado
        updateTotalPrice();
    });
}

// Quick view functionality
function showQuickView(productId) {
    // Create modal for quick view
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Vista Rápida</h3>
                    <button onclick="this.closest('.fixed').remove(); document.body.style.overflow = '';" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="quick-view-content">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                        <span class="ml-2 text-gray-600">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Load product details
    fetch(`/producto/${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extraer información básica del producto
            const productName = doc.querySelector('h1, .product-title')?.textContent?.trim() || 'Producto';
            const productPrice = doc.querySelector('.price, .precio, .product-price')?.textContent?.trim() || 'Precio no disponible';
            const productDescription = doc.querySelector('.description, .descripcion, .product-description')?.textContent?.trim() || 'Sin descripción disponible';
            const productImage = doc.querySelector('img')?.src || '';
            
            document.getElementById('quick-view-content').innerHTML = `
                <div class="text-center">
                    ${productImage ? `<img src="${productImage}" alt="${productName}" class="w-full h-48 object-cover rounded-lg mb-4">` : ''}
                    <h4 class="text-lg font-semibold mb-2">${productName}</h4>
                    <p class="text-green-600 font-bold text-xl mb-3">${productPrice}</p>
                    <p class="text-gray-600 text-sm mb-4 max-h-20 overflow-hidden">${productDescription}</p>
                    <a href="/producto/${productId}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Ver detalles completos
                    </a>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading product:', error);
            document.getElementById('quick-view-content').innerHTML = '<p class="text-center text-red-500 py-4">Error al cargar el producto</p>';
        });
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
            document.body.style.overflow = '';
        }
    });
    
    // Close on ESC key
    const escHandler = function(e) {
        if (e.key === 'Escape') {
            modal.remove();
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}

// Función para reinicializar cards cuando se cargan nuevos productos (AJAX)
function reinitializeProductCards() {
    initializeProductCards();
}