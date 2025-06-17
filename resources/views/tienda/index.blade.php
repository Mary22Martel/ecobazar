@extends('layouts.app')

@section('title', 'Tienda - Productos Agroecológicos')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    
    <!-- Hero Section with Search -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-6 sm:py-8">
        <div class="container mx-auto px-4">
            <div class="text-center mb-4 sm:mb-6">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2">
                    🌱 Productos Frescos del Campo
                </h1>
                <p class="text-green-100 text-sm sm:text-base max-w-2xl mx-auto">
                    Directamente de nuestros agricultores agroecológicos a tu mesa
                </p>
            </div>
            
            <!-- Search Bar -->
            <div class="max-w-xl mx-auto">
                <div class="relative">
                    <input type="text" 
                           id="search-input" 
                           placeholder="Buscar productos frescos..." 
                           class="w-full px-4 py-3 pl-12 text-gray-900 bg-white rounded-xl shadow-lg focus:ring-2 focus:ring-green-300 focus:outline-none text-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Search Results -->
                <div id="search-results" class="absolute w-full bg-white shadow-xl z-50 mt-2 rounded-xl border border-gray-200 hidden max-h-60 overflow-y-auto">
                    <!-- Dynamic search results -->
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-4 lg:flex lg:gap-6">
        
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 xl:w-72">
            <!-- Mobile Filter Toggle -->
            <div class="lg:hidden mb-4">
                <button id="filter-toggle" 
                        class="w-full flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <span class="font-medium text-gray-800 text-sm">
                        <i class="fas fa-filter mr-2 text-green-600"></i>Filtros y Categorías
                    </span>
                    <i class="fas fa-chevron-down transition-transform" id="filter-icon"></i>
                </button>
            </div>

            <!-- Filter Content -->
            <div id="filter-content" class="hidden lg:block space-y-4">
                
                <!-- Categories -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-green-100 px-4 py-3 border-b border-green-200">
                        <h3 class="font-semibold text-gray-800 text-sm flex items-center">
                            <i class="fas fa-th-large mr-2 text-green-600"></i>
                            Categorías
                        </h3>
                    </div>
                    <div class="p-3">
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('tienda') }}" 
                                   class="category-link flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200 group {{ !request()->has('categoria') ? 'bg-green-50 text-green-700' : '' }}">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity {{ !request()->has('categoria') ? 'opacity-100' : '' }}"></div>
                                    <span>Todos los productos</span>
                                    <span class="ml-auto text-xs text-gray-500">({{ $productos->total() ?? 0 }})</span>
                                </a>
                            </li>
                            @foreach($categorias as $categoria)
                                <li>
                                    <a href="{{ route('productos.filtrarPorCategoria', $categoria->id) }}" 
                                       class="category-link flex items-center px-3 py-2 text-sm {{ request()->is('productos/categoria/'.$categoria->id) ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} rounded-lg transition-all duration-200 group">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3 {{ request()->is('productos/categoria/'.$categoria->id) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-all"></div>
                                        <span>{{ $categoria->nombre }}</span>
                                        <span class="ml-auto text-xs text-gray-500">({{ $categoria->productos_count ?? 0 }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Producers -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-3 border-b border-blue-200">
                        <h3 class="font-semibold text-gray-800 text-sm flex items-center">
                            <i class="fas fa-users mr-2 text-blue-600"></i>
                            Productores
                        </h3>
                    </div>
                    <div class="p-3 max-h-64 overflow-y-auto">
                        <ul class="space-y-1">
                            @foreach($productores as $productor)
                                <li>
                                    <a href="{{ route('productos.filtrarPorProductor', $productor->id) }}" 
                                       class="producer-link flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-all duration-200 group">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <span>{{ $productor->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-4">
                    <div class="text-center">
                        <i class="fas fa-leaf text-2xl mb-2 opacity-80"></i>
                        <h4 class="font-semibold text-sm mb-1">¡100% Agroecológico!</h4>
                        <p class="text-xs text-green-100 leading-relaxed">
                            Productos cultivados sin químicos dañinos, directo del campo a tu mesa.
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:w-0">
            
            <!-- Results Summary -->
            <div class="flex items-center justify-between mb-4 bg-white rounded-xl p-3 shadow-sm border border-gray-200">
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm">
                        @if(request()->has('categoria'))
                            Productos en: {{ $categorias->find(request()->categoria)->nombre ?? 'Categoría' }}
                        @elseif(request()->has('productor'))
                            Productos de: {{ $productores->find(request()->productor)->name ?? 'Productor' }}
                        @else
                            Todos los productos
                        @endif
                    </h2>
                    <p class="text-xs text-gray-600">{{ $productos->total() ?? 0 }} productos encontrados</p>
                </div>
                
                <!-- Sort Options -->
                <div class="flex items-center space-x-2">
                    <label for="sort" class="text-xs text-gray-600 hidden sm:block">Ordenar:</label>
                    <select id="sort" class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="name_asc">Nombre A-Z</option>
                        <option value="name_desc">Nombre Z-A</option>
                        <option value="price_asc">Precio menor</option>
                        <option value="price_desc">Precio mayor</option>
                        <option value="stock_desc">Más stock</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                @forelse ($productos as $producto)
                    @include('components.product-card', ['producto' => $producto])
                @empty
                    <div class="col-span-full">
                        @include('components.empty-state')
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($productos->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $productos->links('components.pagination') }}
                </div>
            @endif
        </main>
    </div>
</div>

<!-- Floating Action Button for Cart -->
<div class="fixed bottom-4 right-4 z-30 sm:bottom-6 sm:right-6">
    <button id="fab-cart" 
            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-3 sm:p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 flex items-center space-x-2">
        <i class="fas fa-shopping-cart text-lg sm:text-xl"></i>
        <span class="hidden sm:inline font-semibold text-sm">Ver Carrito</span>
        <span id="fab-cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 sm:h-6 sm:w-6 flex items-center justify-center font-bold">0</span>
    </button>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize store functionality
    initializeStore();
});

function initializeStore() {
    // Mobile filter toggle
    const filterToggle = document.getElementById('filter-toggle');
    const filterContent = document.getElementById('filter-content');
    const filterIcon = document.getElementById('filter-icon');

    if (filterToggle && filterContent) {
        filterToggle.addEventListener('click', function() {
            const isHidden = filterContent.classList.contains('hidden');
            filterContent.classList.toggle('hidden');
            filterIcon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    }

    // Search functionality
    initializeSearch();
    
    // Product interactions
    initializeProductInteractions();
    
    // Sort functionality
    initializeSorting();
    
    // Update cart badge
    updateCartBadge();
}

function initializeSearch() {
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else {
                searchResults.classList.add('hidden');
            }
        });

        // Close search results on outside click
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }
}

function performSearch(query) {
    const searchResults = document.getElementById('search-results');
    
    // Show loading state
    searchResults.innerHTML = `
        <div class="p-4 text-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div>
            <p class="text-sm text-gray-600 mt-2">Buscando productos...</p>
        </div>
    `;
    searchResults.classList.remove('hidden');

    // Perform AJAX search
    $.ajax({
        url: "{{ route('buscar.productos') }}",
        method: 'GET',
        data: { q: query },
        success: function(response) {
            displaySearchResults(response);
        },
        error: function() {
            searchResults.innerHTML = `
                <div class="p-4 text-center">
                    <p class="text-sm text-red-600">Error al buscar productos</p>
                </div>
            `;
        }
    });
}

function displaySearchResults(products) {
    const searchResults = document.getElementById('search-results');
    
    if (products.length > 0) {
        let html = '';
        products.forEach(function(product) {
            const medidaText = product.medida ? ` / ${product.medida.nombre}` : '';
            const imageUrl = product.imagen ? `/storage/${product.imagen}` : '';
            
            html += `
                <a href="/producto/${product.id}" class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                    <div class="w-12 h-12 rounded-lg overflow-hidden mr-3 flex-shrink-0 bg-gray-200">
                        ${imageUrl ? 
                            `<img src="${imageUrl}" alt="${product.nombre}" class="w-full h-full object-cover" loading="lazy">` :
                            `<div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-seedling text-gray-400"></i>
                             </div>`
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm truncate">${product.nombre}</p>
                        <p class="text-green-600 font-bold text-sm">S/${product.precio}${medidaText}</p>
                        <p class="text-xs text-gray-500">Stock: ${product.cantidad_disponible}${medidaText ? ` ${product.medida.nombre}${product.cantidad_disponible > 1 && product.medida.nombre != 'Unidad' ? 's' : ''}` : ''}</p>
                    </div>
                </a>
            `;
        });
        searchResults.innerHTML = html;
    } else {
        searchResults.innerHTML = `
            <div class="p-4 text-center">
                <i class="fas fa-search text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-600">No se encontraron productos</p>
            </div>
        `;
    }
}

function initializeProductInteractions() {
    // Quantity controls
    $(document).on('click', '.quantity-btn', function() {
        const form = $(this).closest('form');
        const input = form.find('.quantity-input');
        const totalPriceElement = form.find('.total-price');
        const unitPrice = parseFloat(totalPriceElement.data('unit-price'));
        let currentValue = parseInt(input.val());
        const maxValue = parseInt(input.attr('max'));
        const minValue = parseInt(input.attr('min'));
        
        if ($(this).hasClass('plus-btn') && currentValue < maxValue) {
            input.val(currentValue + 1);
        } else if ($(this).hasClass('minus-btn') && currentValue > minValue) {
            input.val(currentValue - 1);
        }
        
        // Update total price
        const newQuantity = parseInt(input.val());
        const totalPrice = unitPrice * newQuantity;
        totalPriceElement.text('S/' + totalPrice.toFixed(2));
        
        // Update button states
        form.find('.minus-btn').prop('disabled', newQuantity <= minValue);
        form.find('.plus-btn').prop('disabled', newQuantity >= maxValue);
    });

    // Add to cart
    $(document).on('submit', '.add-to-cart-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.html();
        
        // Show loading state
        button.prop('disabled', true).html(`
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                <span>Agregando...</span>
            </div>
        `);

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Update cart badge
                    updateCartBadge();
                    
                    // Show success message
                    showNotification('¡Producto agregado al carrito!', 'success');
                    
                    // Reset form
                    form.find('.quantity-input').val(1);
                    form.find('.total-price').text('S/' + parseFloat(form.find('.total-price').data('unit-price')).toFixed(2));
                } else {
                    showNotification(response.error || 'Error al agregar producto', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al agregar el producto';
                if (xhr.status === 401) {
                    errorMessage = 'Debes iniciar sesión para agregar productos';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showNotification(errorMessage, 'error');
            },
            complete: function() {
                // Restore button
                button.prop('disabled', false).html(originalText);
            }
        });
    });
}

function initializeSorting() {
    const sortSelect = document.getElementById('sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        });
        
        // Set current sort value
        const urlParams = new URLSearchParams(window.location.search);
        const currentSort = urlParams.get('sort');
        if (currentSort) {
            sortSelect.value = currentSort;
        }
    }
}

function updateCartBadge() {
    $.ajax({
        url: "{{ route('carrito.getDetails') }}",
        method: 'GET',
        success: function(response) {
            const badges = ['#fab-cart-badge', '[data-cart-badge]'];
            badges.forEach(selector => {
                const badge = document.querySelector(selector);
                if (badge) {
                    badge.textContent = response.totalItems > 9 ? '9+' : response.totalItems;
                    badge.style.display = response.totalItems > 0 ? 'flex' : 'none';
                }
            });
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white text-sm font-medium transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
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
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}
</script>
@endpush