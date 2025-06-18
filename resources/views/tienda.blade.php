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

    /* Mejorar visibilidad de resultados de búsqueda */
    #search-results {
        z-index: 100;
    }

    /* Mejorar sidebar en móvil */
    @media (max-width: 1023px) {
        #sidebar {
            width: 85%;
            max-height: 85vh;
            overflow-y: auto;
        }
    }

    /* ARREGLO 1: Buscador más pequeño en desktop */
    .search-container {
        max-width: 100%;
    }
    
    @media (min-width: 768px) {
        .search-container {
            max-width: 320px;
        }
    }

    /* ARREGLO 2: Controles de cantidad visibles en móvil */
    .quantity-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
    }

    .quantity-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .quantity-input {
        width: 60px;
        height: 36px;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        flex-shrink: 0;
    }

    .quantity-input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 1px #10b981;
    }

    .quantity-btn:hover {
        background-color: #f3f4f6;
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    /* Tamaño normal por defecto (escritorio) */
    .quantity-btn {
        width: 30px;
        height: 30px;
        padding: 4px;
    }

    .quantity-btn svg {
        width: 18px;
        height: 18px;
    }

    .quantity-input {
        width: 50px;
        height: 30px;
        font-size: 12px;
        text-align: center;
    }

    /* Tamaño reducido para pantallas pequeñas (móviles) */
    @media (max-width: 640px) {
        .quantity-btn {
            width: 24px;
            height: 24px;
            padding: 0;
        }

        .quantity-btn svg {
            width: 16px;
            height: 16px;
        }

        .quantity-input {
            width: 40px;
            height: 24px;
            font-size: 14px;
        }
    }

    </style>

<!-- Hero Section optimizada -->
<div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-3 sm:py-6">
    <div class="container mx-auto px-4">
        <div class="text-center mb-3 sm:mb-6">
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold mb-1 sm:mb-2">
                🌱 Productos Frescos del Campo
            </h1>
            <p class="text-green-100 text-xs sm:text-sm max-w-2xl mx-auto">
                Directamente de nuestros agricultores a tu mesa
            </p>
        </div>
        
        <!-- Barra de búsqueda ARREGLADA -->
        <div class="search-container mx-auto">
            <div class="relative">
                <input type="text" 
                       id="search"
                       name="query"
                       placeholder="Buscar productos..." 
                       class="w-full px-3 py-2 pl-10 pr-16 text-sm text-gray-900 bg-white rounded-lg shadow focus:ring-2 focus:ring-green-300 focus:outline-none">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <button type="button" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded-md text-xs font-medium transition-colors">
                    Buscar
                </button>
            </div>
            
            <!-- Resultados de búsqueda -->
            <div id="search-results" class="absolute w-max bg-white shadow-xl z-50 mt-1 rounded-lg border border-gray-200 hidden max-h-60 overflow-y-auto">
                <!-- Resultados dinámicos -->
            </div>
        </div>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 py-4 lg:flex lg:gap-6">
        
        <!-- Sidebar Filtros -->
        <aside class="lg:w-64 xl:w-72">
            <!-- Botón para móvil optimizado -->
            <div class="lg:hidden mb-4">
                <button id="open-sidebar" 
                        class="w-full flex items-center justify-between bg-white p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <span class="font-medium text-gray-800 text-sm">
                        <i class="fas fa-filter mr-2 text-green-600"></i>Filtros y Categorías
                    </span>
                    <i class="fas fa-chevron-down transition-transform text-gray-600" id="filter-icon"></i>
                </button>
            </div>

            <!-- Overlay móvil -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
            
            <!-- Sidebar optimizado -->
            <div id="sidebar" class="fixed lg:relative lg:translate-x-0 transform -translate-x-full transition-transform duration-300 ease-in-out w-full max-w-xs lg:w-full bg-white shadow-2xl z-50 h-full lg:h-auto lg:overflow-y-visible">
                <!-- Header móvil -->
                <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200 bg-white sticky top-0 z-10">
                    <h2 class="text-lg font-bold text-gray-800">Filtros</h2>
                    <button id="close-sidebar" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido Filtros -->
                <div class="p-4 lg:p-0 space-y-4">
                    
                    <!-- Categorías optimizadas -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                                       class="category-link flex items-center px-3 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200 group">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <span>Todos los productos</span>
                                    </a>
                                </li>
                                @foreach($categorias as $categoria)
                                    <li>
                                        <a href="{{ route('productos.filtrarPorCategoria', $categoria->id) }}" 
                                           class="category-link flex items-center px-3 py-2.5 text-sm {{ request()->is('productos/categoria/'.$categoria->id) ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }} rounded-lg transition-all duration-200 group">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3 {{ request()->is('productos/categoria/'.$categoria->id) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-all"></div>
                                            <span class="truncate">{{ $categoria->nombre }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Productores optimizados -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                                           class="producer-link flex items-center px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-all duration-200 group">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <span class="truncate">{{ $productor->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Tarjeta informativa -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg p-4">
                        <div class="text-center">
                            <i class="fas fa-leaf text-xl mb-2 opacity-80"></i>
                            <h4 class="font-semibold text-sm mb-1">¡100% Agroecológico!</h4>
                            <p class="text-xs text-green-100 leading-relaxed">
                                Productos cultivados sin químicos dañinos, directo del campo a tu mesa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 lg:w-0">
            
            <!-- Resumen Resultados -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 bg-white rounded-lg p-3 shadow-sm border border-gray-200 gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm">Todos los productos</h2>
                    <p class="text-xs text-gray-600">{{ $productos->count() }} productos encontrados</p>
                </div>
                
                <!-- Opciones Orden -->
                <div class="flex items-center space-x-2">
                    <label for="sort" class="text-xs text-gray-600 hidden sm:block">Ordenar:</label>
                    <select id="sort" class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-green-500 focus:border-transparent min-w-0">
                        <option value="name_asc">Nombre A-Z</option>
                        <option value="name_desc">Nombre Z-A</option>
                        <option value="price_asc">Precio menor</option>
                        <option value="price_desc">Precio mayor</option>
                        <option value="stock_desc">Más stock</option>
                    </select>
                </div>
            </div>

            <!-- Grid de Productos -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
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
                    <div class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 card-hover">
                        <div class="relative overflow-hidden">
                            <a href="{{ route('producto.show', $producto->id) }}">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-36 sm:h-40 lg:h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-36 sm:h-40 lg:h-48 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            
                            <!-- Badge Estado -->
                            @if($producto->cantidad_disponible > 0)
                                <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Disponible
                                </div>
                            @else
                                <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Agotado
                                </div>
                            @endif

                            @if($producto->cantidad_disponible <= 5 && $producto->cantidad_disponible > 0)
                                <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    ¡Últimas {{ $producto->cantidad_disponible }}!
                                </div>
                            @endif
                        </div>

                        <div class="p-3 lg:p-4">
                            <a href="{{ route('producto.show', $producto->id) }}" class="block mb-2">
                                <h3 class="font-bold text-sm lg:text-base text-gray-800 group-hover:text-green-600 transition-colors line-clamp-2 leading-tight">{{ $producto->nombre }}</h3>
                            </a>
                            
                            <!-- Info Productor -->
                            @if($producto->user)
                                <div class="flex items-center text-xs text-gray-600 mb-2">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="truncate">{{ $producto->user->name }}</span>
                                </div>
                            @endif
                            
                            <!-- Precio -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex flex-col">
                                    <span class="text-sm lg:text-base font-bold text-green-600">S/{{ number_format($producto->precio, 2) }}</span>
                                    @if($producto->medida)
                                        <span class="text-xs text-gray-600 font-medium">por {{ $producto->medida->nombre }}</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        Stock: {{ $producto->cantidad_disponible }}
                                    </span>
                                </div>
                            </div>

                            <!-- Selector Cantidad ARREGLADO -->
                            @if($producto->cantidad_disponible > 0)
                                <form class="add-to-cart-form" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                                    @csrf
                                    
                                    <!-- Controles Cantidad ARREGLADOS -->
                                    <div class="bg-gray-50 rounded-lg p-3 mb-1">
                                        <div class="mb-3">
                                            <label class="text-xs font-medium text-gray-700 block mb-2">Cantidad</label>
                                            <!-- CONTROLES ARREGLADOS AQUÍ -->
                                         <div class="quantity-wrapper">
                                            <button type="button" class="quantity-btn minus-btn" data-action="decrease">
                                                <svg class="text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input type="number" name="cantidad" class="quantity-input"
                                                value="1" min="1" max="{{ $producto->cantidad_disponible }}">
                                            <button type="button" class="quantity-btn plus-btn" data-action="increase">
                                                <svg class="text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>


                                        </div>

                                        <!-- Precio Total -->
                                        <div class="text-center p-2 bg-green-50 rounded">
                                            <span class="text-xs text-gray-600">Total: </span>
                                            <span class="total-price text-sm font-bold text-green-600" data-unit-price="{{ $producto->precio }}">
                                                S/{{ number_format($producto->precio, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs lg:text-sm font-semibold py-2 lg:py-3 px-3 rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                                        </svg>
                                        <span>Agregar</span>
                                    </button>
                                </form>
                            @else
                                <!-- Producto Agotado -->
                                <div class="text-center py-3">
                                    <button disabled class="w-full bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold py-2 lg:py-3 px-3 rounded-lg cursor-not-allowed flex items-center justify-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                        </svg>
                                        <span class="text-xs lg:text-sm">Agotado</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </main>
    </div>

    <!-- Botón Carrito Flotante Optimizado -->
    <div class="fixed bottom-4 right-4 z-40">
    <a href="{{ route('carrito.index') }}" 
       class="relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-3 lg:p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 flex items-center space-x-2 group">
        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
        </svg>
        <span class="hidden sm:inline font-semibold text-sm lg:text-base">Ir a carrito</span>
        <span id="floating-cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 lg:h-6 lg:w-6 flex items-center justify-center font-bold">
            @auth
                @php
                    $userCarrito = \App\Models\Carrito::where('user_id', auth()->id())->with('items')->first();
                    echo $userCarrito ? $userCarrito->items->sum('cantidad') : 0;
                @endphp
            @else
                0
            @endauth
        </span>
        
        <!-- Tooltip opcional -->
        <div class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none">
                Yendo al carrito
                <div class="absolute top-full right-3 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-green-600"></div>
            </div>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Control Sidebar Móvil
    $('#open-sidebar').on('click', function() {
        $('#sidebar').removeClass('-translate-x-full');
        $('#sidebar-overlay').removeClass('hidden');
        $('body').addClass('overflow-hidden');
        $('#filter-icon').addClass('rotate-180');
    });

    $('#close-sidebar, #sidebar-overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
        $('body').removeClass('overflow-hidden');
        $('#filter-icon').removeClass('rotate-180');
    });

    // Función Búsqueda Optimizada
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
                                        <a href="/producto/${product.id}" class="flex items-center p-3 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden mr-3 flex-shrink-0">
                                                ${product.imagen ? 
                                                    `<img src="/storage/${product.imagen}" alt="${product.nombre}" class="w-full h-full object-cover">` :
                                                    `<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>`
                                                }
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-800 text-sm truncate">${product.nombre}</p>
                                                <p class="text-sm font-bold text-green-600">S/${product.precio}${medidaText}</p>
                                                <p class="text-xs text-gray-500">Stock: ${product.cantidad_disponible}</p>
                                            </div>
                                        </a>
                                    `;
                                    searchResults.append(productItem);
                                });
                            } else {
                                searchResults.removeClass('hidden');
                                searchResults.append('<div class="p-4 text-center text-gray-500 text-sm">No se encontraron productos</div>');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error en búsqueda:', xhr);
                        }
                    });
                }, 300);
            } else {
                $(resultsContainer).addClass('hidden');
            }
        });
    }

    // Inicializar Búsqueda
    handleSearch('#search', '#search-results');

    // Cerrar resultados al hacer clic fuera
    $(document).click(function(event) {
        if (!$(event.target).closest('#search, #search-results').length) {
            $('#search-results').addClass('hidden');
        }
    });

    // Control Cantidad Producto ARREGLADO
    $('.quantity-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
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
        
        // Actualizar estado botones
        form.find('.minus-btn').prop('disabled', newQuantity <= minValue);
        form.find('.plus-btn').prop('disabled', newQuantity >= maxValue);
    });

    // Manejo Input Cantidad
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
        
        // Actualizar precio
        let totalPrice = unitPrice * currentValue;
        totalPriceElement.text('S/' + totalPrice.toFixed(2));
        
        // Actualizar botones
        form.find('.minus-btn').prop('disabled', currentValue <= minValue);
        form.find('.plus-btn').prop('disabled', currentValue >= maxValue);
    });

 

    // Agregar al Carrito - SOLUCIÓN IMPLEMENTADA
   // Variables globales para el carrito
let isAddingToCart = false;

// MODIFICAR el evento de agregar al carrito para que actualice el badge flotante
$('.add-to-cart-form').off('submit').on('submit', function(e) {
    e.preventDefault();

    if (isAddingToCart) return false;

    let form = $(this);
    let button = form.find('button[type="submit"]');
    let originalText = button.html();

    // Guardar la cantidad ANTES de resetear
    let cantidadAgregada = parseInt(form.find('.quantity-input').val()) || 1;
    let nombreProducto = form.closest('.group').find('h3').text();

    // Estado de carga
    isAddingToCart = true;
    button.prop('disabled', true).html(`
        <svg class="animate-spin w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24">
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
                // Actualizar TODOS los badges del carrito
                $('#cart-badge').text(response.totalItems || 0); // Navbar
                $('#floating-cart-badge').text(response.totalItems || 0); // Flotante
                $('#cart-badge-mobile').text(response.totalItems || 0); // Móvil (si existe)

                // Resetear cantidad del formulario
                form.find('.quantity-input').val(1);
                form.find('.total-price').text('S/' + parseFloat(form.find('.total-price').data('unit-price')).toFixed(2));

                // Animación en el botón flotante
                $('#floating-cart-badge').addClass('cart-bounce');
                setTimeout(() => {
                    $('#floating-cart-badge').removeClass('cart-bounce');
                }, 600);

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
                    didOpen: () => {
                        // Agregar un pequeño efecto al toast
                        const toast = Swal.getPopup();
                        toast.style.animation = 'slideInRight 0.3s ease-out';
                    }
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
                        window.location.href = "{{ route('login') }}";
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

// Agregar animación CSS adicional si no existe
if (!document.querySelector('#cart-animation-styles')) {
    const style = document.createElement('style');
    style.id = 'cart-animation-styles';
    style.textContent = `
        .cart-bounce {
            animation: cartBounce 0.6s ease;
        }
        
        @keyframes cartBounce {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(0.9); }
            75% { transform: scale(1.1); }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    `;
    document.head.appendChild(style);
}
});
</script>
@endsection