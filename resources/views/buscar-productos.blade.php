@extends('layouts.app')

@section('content')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Estilos para los controles mini de cantidad */
    .quantity-btn-mini:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-input-mini:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 1px #10b981;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-6 sm:py-8">
        <div class="container mx-auto px-4">
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2">
                    <i class="fas fa-search mr-2"></i>
                    Resultados de búsqueda
                </h1>
                
                @if(request('q'))
                    <p class="text-green-100 text-sm sm:text-base">
                        Buscando por: <strong>"{{ request('q') }}"</strong>
                    </p>
                    <p class="text-green-200 text-xs sm:text-sm mt-1">
                        {{ $productos->count() }} productos encontrados
                    </p>
                @else
                    <p class="text-green-100 text-sm sm:text-base">
                        Ingresa un término para buscar productos
                    </p>
                @endif
            </div>
            
            <!-- Formulario de búsqueda responsivo -->
            <div class="max-w-2xl mx-auto">
                <form method="GET" action="{{ route('buscar.productos') }}" id="search-form" class="bg-white rounded-lg shadow-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Buscar productos..." 
                                   class="w-full px-4 py-3 pl-12 text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold text-sm sm:text-base flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i>
                            <span class="hidden sm:inline">Buscar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        
        <!-- Breadcrumb responsivo -->
        <div class="flex flex-wrap items-center gap-2 mb-6 text-sm text-gray-600">
            <a href="{{ route('tienda') }}" class="hover:text-green-600 transition-colors">
                <i class="fas fa-home mr-1"></i>Tienda
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">Búsqueda</span>
        </div>

        @if($productos->isEmpty())
            <!-- Sin resultados - Responsivo -->
            <div class="bg-white rounded-xl shadow-sm p-8 sm:p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3">
                        @if(request('q'))
                            No encontramos productos
                        @else
                            Realiza una búsqueda
                        @endif
                    </h3>
                    <p class="text-gray-600 mb-6 text-sm sm:text-base">
                        @if(request('q'))
                            No hay productos que coincidan con "<strong>{{ request('q') }}</strong>". 
                            <br class="hidden sm:block">Intenta con otros términos de búsqueda.
                        @else
                            Usa el formulario de arriba para buscar productos frescos de nuestros agricultores.
                        @endif
                    </p>
                    
                    <!-- Sugerencias responsivas -->
                    <div class="space-y-3">
                        @if(request('q'))
                            <a href="{{ route('buscar.productos') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm">
                                <i class="fas fa-search mr-2"></i>
                                Nueva búsqueda
                            </a>
                        @endif
                        <a href="{{ route('tienda') }}" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                            <i class="fas fa-store mr-2"></i>
                            Ver todos los productos
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Filtros y ordenamiento responsivo -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base sm:text-lg">
                            Resultados para "{{ request('q') }}"
                        </h2>
                        <p class="text-sm text-gray-600">
                            {{ $productos->count() }} productos encontrados
                        </p>
                    </div>
                    
                    <!-- Ordenamiento -->
                    <div class="flex items-center gap-3">
                        <label for="sort" class="text-sm text-gray-600 whitespace-nowrap">Ordenar por:</label>
                        <select id="sort" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent min-w-0">
                            <option value="name_asc">Nombre A-Z</option>
                            <option value="name_desc">Nombre Z-A</option>
                            <option value="price_asc">Precio menor</option>
                            <option value="price_desc">Precio mayor</option>
                            <option value="stock_desc">Más stock</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid de productos responsivo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($productos as $producto)
                <div class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 card-hover">
                    <div class="relative overflow-hidden">
                        <a href="{{ route('producto.show', $producto->id) }}">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                     alt="{{ $producto->nombre }}" 
                                     class="w-full h-48 sm:h-52 lg:h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-48 sm:h-52 lg:h-56 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Badges responsivos -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            @if($producto->cantidad_disponible > 0)
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Disponible
                                </span>
                            @else
                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Agotado
                                </span>
                            @endif
                        </div>

                        @if($producto->cantidad_disponible <= 5 && $producto->cantidad_disponible > 0)
                            <div class="absolute top-3 right-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                ¡Últimas {{ $producto->cantidad_disponible }}!
                            </div>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5">
                        <a href="{{ route('producto.show', $producto->id) }}" class="block mb-3">
                            <h3 class="font-bold text-base sm:text-lg text-gray-800 group-hover:text-green-600 transition-colors line-clamp-2 leading-tight">
                                {{ $producto->nombre }}
                            </h3>
                        </a>
                        
                        <!-- Info Productor -->
                        @if($producto->user)
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="truncate">{{ $producto->user->name }}</span>
                            </div>
                        @endif
                        
                        <!-- Precio y stock responsivo -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-lg sm:text-xl font-bold text-green-600">
                                    S/{{ number_format($producto->precio, 2) }}
                                </span>
                                @if($producto->medida)
                                    <span class="text-sm text-gray-600 block">
                                        por {{ $producto->medida->nombre }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                Stock: {{ $producto->cantidad_disponible }}
                            </span>
                        </div>

                        <!-- Agregar al carrito o ver producto -->
                        @if($producto->cantidad_disponible > 0)
                            <!-- Selector de cantidad mini -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <form class="add-to-cart-form" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                                    @csrf
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-xs font-medium text-gray-700">Cantidad:</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="quantity-btn-mini minus-btn w-6 h-6 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                                <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input type="number" name="cantidad" class="quantity-input-mini w-12 h-6 text-center text-xs border border-gray-300 rounded" 
                                                   value="1" min="1" max="{{ $producto->cantidad_disponible }}">
                                            <button type="button" class="quantity-btn-mini plus-btn w-6 h-6 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                                <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Precio total mini -->
                                    <div class="text-center mb-3">
                                        <span class="text-xs text-gray-600">Total: </span>
                                        <span class="total-price-mini font-bold text-green-600 text-sm" data-unit-price="{{ $producto->precio }}">
                                            S/{{ number_format($producto->precio, 2) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="submit" 
                                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-1 text-xs">
                                            <i class="fas fa-cart-plus"></i>
                                            <span class="hidden sm:inline">Agregar</span>
                                        </button>
                                        <a href="{{ route('producto.show', $producto->id) }}" 
                                           class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-1 text-xs">
                                            <i class="fas fa-eye"></i>
                                            <span class="hidden sm:inline">Ver</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- Producto agotado -->
                            <div class="grid grid-cols-2 gap-2">
                                <button disabled 
                                        class="bg-gray-400 text-white font-semibold py-2 px-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-1 text-xs">
                                    <i class="fas fa-times"></i>
                                    <span class="hidden sm:inline">Agotado</span>
                                </button>
                                <a href="{{ route('producto.show', $producto->id) }}" 
                                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-1 text-xs">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden sm:inline">Ver</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        <!-- Botón volver responsivo -->
        <div class="mt-8 sm:mt-12 text-center">
            <a href="{{ route('tienda') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a la tienda</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Validación del formulario de búsqueda
    $('#search-form').on('submit', function(e) {
        let query = $(this).find('input[name="q"]').val().trim();
        if (!query) {
            e.preventDefault();
            Swal.fire({
                title: 'Búsqueda vacía',
                text: 'Por favor ingresa un término de búsqueda',
                icon: 'warning',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Entendido'
            });
        }
    });

    // Control de cantidad mini
    $('.quantity-btn-mini').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let form = $(this).closest('form');
        let input = form.find('.quantity-input-mini');
        let totalPriceElement = form.find('.total-price-mini');
        let unitPrice = parseFloat(totalPriceElement.data('unit-price'));
        let currentValue = parseInt(input.val());
        let maxValue = parseInt(input.attr('max'));
        let minValue = parseInt(input.attr('min'));
        
        if ($(this).hasClass('plus-btn') && currentValue < maxValue) {
            input.val(currentValue + 1);
        } else if ($(this).hasClass('minus-btn') && currentValue > minValue) {
            input.val(currentValue - 1);
        }
        
        // Actualizar precio total
        let newQuantity = parseInt(input.val());
        let totalPrice = unitPrice * newQuantity;
        totalPriceElement.text('S/' + totalPrice.toFixed(2));
        
        // Actualizar estado botones
        form.find('.minus-btn').prop('disabled', newQuantity <= minValue);
        form.find('.plus-btn').prop('disabled', newQuantity >= maxValue);
    });

    // Manejo input cantidad mini
    $('.quantity-input-mini').on('input', function() {
        let form = $(this).closest('form');
        let totalPriceElement = form.find('.total-price-mini');
        let unitPrice = parseFloat(totalPriceElement.data('unit-price'));
        let currentValue = parseInt($(this).val()) || 1;
        let maxValue = parseInt($(this).attr('max'));
        let minValue = parseInt($(this).attr('min'));
        
        // Validar límites
        if (currentValue > maxValue) {
            $(this).val(maxValue);
            currentValue = maxValue;
        } else if (currentValue < minValue) {
            $(this).val(minValue);
            currentValue = minValue;
        }
        
        // Actualizar precio
        let totalPrice = unitPrice * currentValue;
        totalPriceElement.text('S/' + totalPrice.toFixed(2));
        
        // Actualizar botones
        form.find('.minus-btn').prop('disabled', currentValue <= minValue);
        form.find('.plus-btn').prop('disabled', currentValue >= maxValue);
    });

    // Agregar al carrito desde búsqueda
    let isAddingToCart = false;
    
    $('.add-to-cart-form').off('submit').on('submit', function(e) {
        e.preventDefault();

        if (isAddingToCart) return false;

        let form = $(this);
        let button = form.find('button[type="submit"]');
        let originalText = button.html();

        // Guardar la cantidad ANTES de resetear
        let cantidadAgregada = parseInt(form.find('.quantity-input-mini').val()) || 1;
        let nombreProducto = form.closest('.card-hover').find('h3').text();

        // Estado de carga
        isAddingToCart = true;
        button.prop('disabled', true).html(`
            <svg class="animate-spin w-3 h-3 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `);

        // AJAX
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    // Actualizar badges del carrito si existen
                    if ($('#cart-badge').length) $('#cart-badge').text(response.totalItems || 0);
                    if ($('#floating-cart-badge').length) $('#floating-cart-badge').text(response.totalItems || 0);

                    // Resetear cantidad del formulario
                    form.find('.quantity-input-mini').val(1);
                    form.find('.total-price-mini').text('S/' + parseFloat(form.find('.total-price-mini').data('unit-price')).toFixed(2));

                    // Mostrar notificación de éxito
                    Swal.fire({
                        title: '¡Producto añadido!',
                        text: `${cantidadAgregada} unidad${cantidadAgregada > 1 ? 'es' : ''} de ${nombreProducto} agregado al carrito`,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Error al agregar el producto';
                
                if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado';
                } else if (xhr.status === 0) {
                    errorMessage = 'Sin conexión a internet';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 401) {
                    Swal.fire({
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para agregar productos',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3B82F6',
                        confirmButtonText: 'Ir a login',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/login';
                        }
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#3B82F6',
                });
            },
            complete: function() {
                // Restaurar el botón SIEMPRE
                button.prop('disabled', false).html(originalText);
                isAddingToCart = false;
            }
        });
    });

    // Funcionalidad de ordenamiento
    $('#sort').on('change', function() {
        let sortValue = $(this).val();
        let products = $('.grid > div').get();
        
        products.sort(function(a, b) {
            let aText, bText;
            
            switch(sortValue) {
                case 'name_asc':
                    aText = $(a).find('h3').text().toLowerCase();
                    bText = $(b).find('h3').text().toLowerCase();
                    return aText.localeCompare(bText);
                
                case 'name_desc':
                    aText = $(a).find('h3').text().toLowerCase();
                    bText = $(b).find('h3').text().toLowerCase();
                    return bText.localeCompare(aText);
                
                case 'price_asc':
                    aText = parseFloat($(a).find('.text-green-600').text().replace('S/', '').replace(',', ''));
                    bText = parseFloat($(b).find('.text-green-600').text().replace('S/', '').replace(',', ''));
                    return aText - bText;
                
                case 'price_desc':
                    aText = parseFloat($(a).find('.text-green-600').text().replace('S/', '').replace(',', ''));
                    bText = parseFloat($(b).find('.text-green-600').text().replace('S/', '').replace(',', ''));
                    return bText - aText;
                
                case 'stock_desc':
                    aText = parseInt($(a).find('.bg-gray-100').text().replace('Stock: ', ''));
                    bText = parseInt($(b).find('.bg-gray-100').text().replace('Stock: ', ''));
                    return bText - aText;
            }
        });
        
        $.each(products, function(index, item) {
            $('.grid').append(item);
        });
    });

    // Animación suave al cargar
    $('.card-hover').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        }).animate({
            'opacity': '1'
        }, 200 + (index * 50)).css('transform', 'translateY(0px)');
    });
});
</script>
@endsection