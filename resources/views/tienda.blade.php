@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="lg:flex">
        <!-- Overlay para móvil -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed lg:relative lg:translate-x-0 transform -translate-x-full transition-transform duration-300 ease-in-out w-80 lg:w-1/4 xl:w-1/5 bg-white shadow-2xl z-50 h-full lg:h-auto overflow-y-auto">
            <!-- Header del sidebar móvil -->
            <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Filtros</h2>
                <button id="close-sidebar" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-4 lg:p-6">
                <!-- Categorías -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Categorías
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('tienda') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:text-green-700 rounded-xl transition-all duration-200 group">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <span class="font-medium">Todos los productos</span>
                            </a>
                        </li>
                        @foreach($categorias as $cat)
                            <li>
                                <a href="{{ route('productos.filtrarPorCategoria', $cat->id) }}" class="flex items-center px-4 py-3 {{ request()->is('productos/categoria/'.$cat->id) ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:text-green-700' }} rounded-xl transition-all duration-200 group">
                                    <div class="w-2 h-2 {{ request()->is('productos/categoria/'.$cat->id) ? 'bg-white' : 'bg-green-500' }} rounded-full mr-3 {{ request()->is('productos/categoria/'.$cat->id) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-all"></div>
                                    <span class="font-medium">{{ $cat->nombre }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Productores -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Productores
                    </h3>
                    <ul class="space-y-2">
                        @foreach($productores as $productor)
                            <li>
                                <a href="{{ route('productos.filtrarPorProductor', $productor->id) }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:text-blue-700 rounded-xl transition-all duration-200 group">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <span class="font-medium">{{ $productor->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 lg:w-3/4 xl:w-4/5">
            <!-- Header móvil con botón de filtros y búsqueda -->
            <div class="lg:hidden sticky top-0 z-30 bg-white shadow-md border-b border-gray-200">
                <div class="p-4">
                    <div class="flex items-center space-x-4">
                        <button id="open-sidebar" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex-1 relative">
                            <input type="text" id="search-mobile" name="query" placeholder="Buscar productos..." class="w-full border border-gray-300 rounded-xl px-4 py-2 pl-10 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Resultados de búsqueda móvil -->
                    <div id="search-results-mobile" class="absolute w-full left-0 right-0 bg-white shadow-lg z-50 mt-1 rounded-xl border border-gray-200 hidden mx-4">
                        <!-- Los resultados se agregarán dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="p-4 lg:p-6">
                <!-- Formulario de búsqueda desktop -->
                <div class="hidden lg:block relative mb-8">
                    <div class="max-w-2xl">
                        <div class="relative">
                            <input type="text" id="search" name="query" placeholder="Buscar productos..." class="w-full border border-gray-300 rounded-xl px-6 py-4 pl-12 text-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all shadow-sm">
                            <svg class="w-6 h-6 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div id="search-results" class="absolute w-full bg-white shadow-xl z-50 mt-2 rounded-xl border border-gray-200 hidden">
                            <!-- Los resultados se agregarán dinámicamente aquí -->
                        </div>
                    </div>
                </div>

                <!-- Grid de productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                    @if($productos->isEmpty())
                        <div class="col-span-full text-center py-16">
                            <div class="max-w-md mx-auto">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay productos disponibles</h3>
                                <p class="text-gray-500">En este momento no tenemos productos para mostrar.</p>
                            </div>
                        </div>
                    @else
                        @foreach ($productos as $producto)
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                            <div class="relative overflow-hidden">
                                <a href="{{ route('producto.show', $producto->id) }}">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-48 sm:h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-48 sm:h-56 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                @if($producto->cantidad_disponible <= 5 && $producto->cantidad_disponible > 0)
                                    <div class="absolute top-3 left-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        ¡Últimas {{ $producto->cantidad_disponible }}!
                                    </div>
                                @elseif($producto->cantidad_disponible == 0)
                                    <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        Agotado
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 lg:p-5">
                                <a href="{{ route('producto.show', $producto->id) }}" class="block mb-2">
                                    <h3 class="font-bold text-lg text-gray-800 group-hover:text-green-600 transition-colors line-clamp-2">{{ $producto->nombre }}</h3>
                                </a>
                                
                                <!-- Precio con unidad de medida -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex flex-col">
                                        <span class="text-2xl font-bold text-green-600">S/{{ number_format($producto->precio, 2) }}</span>
                                        @if($producto->medida)
                                            <span class="text-sm text-gray-600 font-medium">por {{ $producto->medida->nombre }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full block">
                                            Stock: {{ $producto->cantidad_disponible }}
                                        </span>
                                        @if($producto->medida)
                                            <span class="text-xs text-gray-400 mt-1 block">
                                                {{ $producto->medida->nombre }}{{ $producto->cantidad_disponible > 1 && $producto->medida->nombre != 'Unidad' ? 's' : '' }} disponible{{ $producto->cantidad_disponible > 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Selector de cantidad y formulario para agregar al carrito -->
                                @if($producto->cantidad_disponible > 0)
                                    <form class="add-to-cart-form" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                                        @csrf
                                        
                                        <!-- Selector de cantidad -->
                                        <div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded-xl">
                                            <label class="text-sm font-medium text-gray-700">
                                                Cantidad
                                                @if($producto->medida)
                                                    <span class="text-gray-500">({{ $producto->medida->nombre }}{{ $producto->medida->nombre != 'Unidad' ? 's' : '' }})</span>
                                                @endif
                                            </label>
                                            <div class="flex items-center space-x-2">
                                                <button type="button" class="quantity-btn minus-btn p-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition-colors" data-action="decrease">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input type="number" name="cantidad" class="quantity-input w-16 px-3 py-2 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                                       value="1" min="1" max="{{ $producto->cantidad_disponible }}" readonly>
                                                <button type="button" class="quantity-btn plus-btn p-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition-colors" data-action="increase">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Precio total dinámico -->
                                        <div class="text-center mb-3 p-2 bg-green-50 rounded-lg">
                                            <span class="text-sm text-gray-600">Total: </span>
                                            <span class="total-price text-lg font-bold text-green-600" data-unit-price="{{ $producto->precio }}">
                                                S/{{ number_format($producto->precio, 2) }}
                                            </span>
                                        </div>

                                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                                            </svg>
                                            <span>Agregar al carrito</span>
                                        </button>
                                    </form>
                                @else
                                    <!-- Producto agotado -->
                                    <div class="text-center py-4">
                                        <button disabled class="w-full bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold py-3 px-4 rounded-xl cursor-not-allowed flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>
                                            <span>Agotado</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante del carrito -->
    <div class="fixed bottom-6 right-6 z-40">
        <a href="{{ route('carrito.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 flex items-center space-x-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            <span class="hidden sm:inline font-semibold">Ver carrito</span>
            <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">0</span>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Control del sidebar móvil
    $('#open-sidebar').on('click', function() {
        $('#sidebar').removeClass('-translate-x-full');
        $('#sidebar-overlay').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    $('#close-sidebar, #sidebar-overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Función de búsqueda unificada
    function handleSearch(searchInput, resultsContainer) {
        let searchTimeout;
        
        $(searchInput).on('input', function() {
            clearTimeout(searchTimeout);
            let query = $(this).val();

            if (query.length > 2) {
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('buscar.productos.ajax') }}",
                        method: 'GET',
                        data: { q: query },
                        success: function(response) {
                            let searchResults = $(resultsContainer);
                            searchResults.empty();

                            if (response.length > 0) {
                                searchResults.removeClass('hidden');

                                response.forEach(function(product) {
                                    let medidaText = product.medida ? ` / ${product.medida.nombre}` : '';
                                    let productItem = `
                                        <a href="/producto/${product.id}" class="flex items-center p-4 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0">
                                            <div class="w-12 h-12 rounded-lg overflow-hidden mr-4 flex-shrink-0">
                                                ${product.imagen ? 
                                                    `<img src="/storage/${product.imagen}" alt="${product.nombre}" class="w-full h-full object-cover">` :
                                                    `<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>`
                                                }
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-800">${product.nombre}</p>
                                                <p class="text-lg font-bold text-green-600">S/${product.precio}${medidaText}</p>
                                                <p class="text-sm text-gray-500">Stock: ${product.cantidad_disponible}${medidaText ? ` ${product.medida.nombre}${product.cantidad_disponible > 1 && product.medida.nombre != 'Unidad' ? 's' : ''}` : ''}</p>
                                            </div>
                                        </a>
                                    `;
                                    searchResults.append(productItem);
                                });
                            } else {
                                searchResults.removeClass('hidden');
                                searchResults.append('<div class="p-4 text-center text-gray-500">No se encontraron productos</div>');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error en búsqueda:', xhr);
                        }
                    });
                }, 300); // Esperar 300ms antes de buscar
            } else {
                $(resultsContainer).addClass('hidden');
            }
        });
    }

    // Aplicar búsqueda a ambos inputs
    handleSearch('#search', '#search-results');
    handleSearch('#search-mobile', '#search-results-mobile');

    // Cerrar resultados de búsqueda al hacer clic fuera
    $(document).click(function(event) {
        if (!$(event.target).closest('#search, #search-results').length) {
            $('#search-results').addClass('hidden');
        }
        if (!$(event.target).closest('#search-mobile, #search-results-mobile').length) {
            $('#search-results-mobile').addClass('hidden');
        }
    });

    // Variable para prevenir múltiples envíos
    let isAddingToCart = false;

    // Manejo de botones de cantidad
    $('.quantity-btn').on('click', function() {
        let form = $(this).closest('form');
        let input = form.find('.quantity-input');
        let totalPriceElement = form.find('.total-price');
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
        
        // Actualizar estado de botones
        form.find('.minus-btn').prop('disabled', newQuantity <= minValue);
        form.find('.plus-btn').prop('disabled', newQuantity >= maxValue);
    });

    // También permitir escribir directamente en el input
    $('.quantity-input').on('input', function() {
        let form = $(this).closest('form');
        let totalPriceElement = form.find('.total-price');
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
        
        // Actualizar precio total
        let totalPrice = unitPrice * currentValue;
        totalPriceElement.text('S/' + totalPrice.toFixed(2));
        
        // Actualizar estado de botones
        form.find('.minus-btn').prop('disabled', currentValue <= minValue);
        form.find('.plus-btn').prop('disabled', currentValue >= maxValue);
    });

    // Manejo del formulario "Agregar al carrito"
    $('.add-to-cart-form').off('submit').on('submit', function(e) {
        e.preventDefault();

        // Prevenir múltiples clics
        if (isAddingToCart) {
            return false;
        }

        let form = $(this);
        let button = form.find('button[type="submit"]');
        let originalText = button.html();

        // Marcar como enviando
        isAddingToCart = true;
        
        // Cambiar botón a estado de carga
        button.prop('disabled', true).html(`
            <svg class="animate-spin w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `);

        // Enviar formulario con timeout para evitar bloqueos
        let actionUrl = form.attr('action');
        
        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: form.serialize(),
            timeout: 10000, // 10 segundos de timeout
            success: function(response) {
                if (response.success) {
                    // Actualizar badge del carrito
                    $('#cart-badge').text(response.totalItems);

                    // Mostrar notificación de éxito
                    let cantidadAgregada = form.find('.quantity-input').val();
                    let nombreProducto = form.closest('.group').find('h3').text();
                    let medidaTexto = form.find('label span').text().replace(/[()]/g, '') || '';
                    
                    Swal.fire({
                        title: '¡Producto añadido!',
                        text: `${cantidadAgregada} ${medidaTexto} de ${nombreProducto} agregado al carrito`,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true,
                    });

                    // Actualizar elementos del carrito si existen
                    if ($('#cart-total-items').length) {
                        $('#cart-total-items').text(response.totalItems);
                    }
                    if ($('#cart-total-price').length) {
                        $('#cart-total-price').text(response.totalPrice.toFixed(2));
                    }

                    // Actualizar modal del carrito si existe
                    if ($('#cart-items-list').length && response.items) {
                        $('#cart-items-list').empty();
                        response.items.forEach(function(item) {
                            $('#cart-items-list').append(`
                                <div class="flex justify-between items-center mb-2 p-2 bg-gray-50 rounded">
                                    <span class="font-medium">${item.nombre}</span>
                                    <span class="text-gray-600">${item.cantidad}</span>
                                    <span class="font-bold text-green-600">S/${item.subtotal.toFixed(2)}</span>
                                </div>
                            `);
                        });
                    }

                    if ($('#cart-popup-total-price').length) {
                        $('#cart-popup-total-price').text(response.totalPrice.toFixed(2));
                    }
                } else {
                    // Mostrar error específico
                    Swal.fire({
                        title: 'Error',
                        text: response.error || 'No se pudo agregar el producto',
                        icon: 'error',
                        confirmButtonColor: '#10B981',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al agregar al carrito:', xhr, status, error);
                
                let errorMessage = 'Error al agregar el producto al carrito.';
                
                if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado. Por favor, intenta nuevamente.';
                } else if (xhr.status === 0) {
                    errorMessage = 'No hay conexión a internet. Verifica tu conexión.';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 401) {
                    // Usuario no autenticado
                    Swal.fire({
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para agregar productos al carrito.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        confirmButtonText: 'Ir a login',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#10B981',
                });
            },
            complete: function() {
                // Restaurar botón
                setTimeout(function() {
                    button.prop('disabled', false).html(originalText);
                    isAddingToCart = false;
                }, 1000);
            }
        });
    });

    // Actualizar stock del producto periódicamente
    function updateProductStock(productId) {
        $.ajax({
            url: '/producto/' + productId,
            method: 'GET',
            success: function(response) {
                let stockElement = $(`#producto-${productId} .cantidad-disponible`);
                if (stockElement.length) {
                    stockElement.text('Stock: ' + response.cantidad_disponible);
                }
            }
        });
    }

    // Cerrar sidebar al cambiar el tamaño de ventana
    $(window).resize(function() {
        if ($(window).width() >= 1024) {
            $('#sidebar').removeClass('-translate-x-full');
            $('#sidebar-overlay').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }
    });
});
</script>
@endsection