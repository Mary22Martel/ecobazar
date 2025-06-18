<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.5/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animaciones suaves */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Hover effects */
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #34d399);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        /* Cart button animation */
        .cart-bounce {
            animation: cartBounce 0.6s ease;
        }
        
        @keyframes cartBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Gradient backgrounds */
        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }
        
        .bg-gradient-green-subtle {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        
        /* Mobile menu animation */
        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    @vite('resources/css/app.css')
</head>
  
<body class="bg-gray-50 min-h-screen">
<div id="app">
    <!-- Navbar Mejorado -->
    <nav class="bg-white shadow-lg border-b border-green-100 sticky top-0 z-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                
                <!-- Logo Section -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <div class="relative">
                            <img src="{{ asset('images/logox.png') }}" alt="Punto Verde Logo" 
                                 class="h-10 lg:h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute -inset-1 bg-gradient-green rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </div>
                        <div class="hidden lg:block">
                            <h1 class="text-lg font-bold text-gray-800">Punto Verde</h1>
                            <p class="text-xs text-green-600">Agroecológico</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="{{ url('/') }}" 
                       class="nav-link {{ request()->is('/') ? 'active text-green-600 font-semibold' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <a href="{{ route('nosotros') }}" 
                       class="nav-link {{ request()->is('nosotros') ? 'active text-green-600 font-semibold' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-users mr-2"></i>Nosotros
                    </a>
                    <a href="{{ route('tienda') }}" 
                       class="nav-link {{ request()->is('tienda') ? 'active text-green-600 font-semibold' : 'text-gray-600 hover:text-green-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-store mr-2"></i>Tienda
                    </a>
                    
                    @auth
                        @if(Auth::user()->role == 'repartidor')
                            <a href="{{ route('repartidor.dashboard') }}" 
                               class="nav-link text-blue-600 hover:text-blue-700 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-truck mr-2"></i>Panel Repartidor
                            </a>
                        @elseif(Auth::user()->role == 'agricultor')
                            <a href="{{ route('agricultor.dashboard') }}" 
                               class="nav-link text-green-600 hover:text-green-700 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-seedling mr-2"></i>Panel Agricultor
                            </a>
                        @elseif(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.dashboard') }}" 
                               class="nav-link text-purple-600 hover:text-purple-700 px-3 py-2 text-sm font-medium">
                                <i class="fas fa-cog mr-2"></i>Panel Admin
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Carrito Optimizado -->
                    @auth
                        <button id="cart-button" 
                                class="relative flex items-center space-x-2 bg-gradient-green text-white px-4 py-2 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-shopping-cart text-lg"></i>
                            <div class="hidden sm:flex flex-col items-start">
                                <span class="text-xs font-medium">Ver carrito</span>
                            </div>
                            <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                                @php
                                    $userCarrito = \App\Models\Carrito::where('user_id', auth()->id())->with('items')->first();
                                    echo $userCarrito ? $userCarrito->items->sum('cantidad') : 0;
                                @endphp
                            </span>
                        </button>
                    @endauth

                    <!-- User Menu -->
                    @guest
                        <div class="hidden lg:flex items-center space-x-3">
                            <a href="{{ route('login') }}" 
                               class="text-gray-600 hover:text-green-600 px-3 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('login') ? 'text-green-600 font-semibold' : '' }}">
                                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ request()->is('register') ? 'bg-green-700' : '' }}">
                                    <i class="fas fa-user-plus mr-2"></i>Registrarse
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="relative">
                            <button id="userMenuButton" 
                                    class="flex items-center space-x-2 text-gray-700 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-lg px-3 py-2 transition-all duration-200"
                                    onclick="toggleDropdown('userMenu')">
                                <div class="w-8 h-8 bg-gradient-green rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:block font-medium">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="userMenuIcon"></i>
                            </button>
                            
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50 fade-in">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm text-gray-600">Conectado como</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    </div>
                                    <a href="{{ route('logout') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-3"></i>Cerrar Sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                                </div>
                            </div>
                        </div>
                    @endguest

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" 
                            class="lg:hidden p-2 rounded-lg text-gray-600 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                            onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl" id="mobile-menu-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 mobile-menu-enter">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white">
                    <a href="{{ url('/') }}" 
                       class="block px-3 py-3 rounded-lg text-base font-medium {{ request()->is('/') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                        <i class="fas fa-home mr-3"></i>Inicio
                    </a>
                    <a href="{{ route('nosotros') }}" 
                       class="block px-3 py-3 rounded-lg text-base font-medium {{ request()->is('nosotros') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                        <i class="fas fa-users mr-3"></i>Nosotros
                    </a>
                    <a href="{{ route('tienda') }}" 
                       class="block px-3 py-3 rounded-lg text-base font-medium {{ request()->is('tienda') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                        <i class="fas fa-store mr-3"></i>Tienda
                    </a>
                    
                    @auth
                        @if(Auth::user()->role == 'repartidor')
                            <a href="{{ route('repartidor.dashboard') }}" 
                               class="block px-3 py-3 rounded-lg text-base font-medium text-blue-600 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-truck mr-3"></i>Panel Repartidor
                            </a>
                        @elseif(Auth::user()->role == 'agricultor')
                            <a href="{{ route('agricultor.dashboard') }}" 
                               class="block px-3 py-3 rounded-lg text-base font-medium text-green-600 hover:bg-green-50 transition-colors duration-200">
                                <i class="fas fa-seedling mr-3"></i>Panel Agricultor
                            </a>
                        @elseif(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.dashboard') }}" 
                               class="block px-3 py-3 rounded-lg text-base font-medium text-purple-600 hover:bg-purple-50 transition-colors duration-200">
                                <i class="fas fa-cog mr-3"></i>Panel Admin
                            </a>
                        @endif
                        
                        <!-- Carrito en móvil -->
                        <a href="{{ route('carrito.index') }}" 
                           class="block px-3 py-3 rounded-lg text-base font-medium text-blue-600 hover:bg-blue-50 transition-colors duration-200">
                            <i class="fas fa-shopping-cart mr-3"></i>Ver Carrito
                            <span id="cart-badge-mobile" class="inline-block ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">
                                @php
                                    $userCarrito = \App\Models\Carrito::where('user_id', auth()->id())->with('items')->first();
                                    echo $userCarrito ? $userCarrito->items->sum('cantidad') : 0;
                                @endphp
                            </span>
                        </a>
                    @endauth
                    
                    @guest
                        <div class="border-t border-gray-100 pt-4 mt-4">
                            <a href="{{ route('login') }}" 
                               class="block px-3 py-3 rounded-lg text-base font-medium text-gray-600 hover:text-green-600 hover:bg-green-50 transition-colors duration-200">
                                <i class="fas fa-sign-in-alt mr-3"></i>Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="block px-3 py-3 rounded-lg text-base font-medium bg-green-600 text-white hover:bg-green-700 transition-colors duration-200 mt-2">
                                    <i class="fas fa-user-plus mr-3"></i>Registrarse
                                </a>
                            @endif
                        </div>
                    @endguest
                </div>
            </div>
        </div>
        
        <!-- Modal del Carrito Optimizado -->
        <div id="cart-modal" class="hidden fixed right-4 top-20 w-80 sm:w-96 bg-white shadow-2xl rounded-2xl z-50 border border-gray-100 fade-in">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                        Mi Carrito
                    </h3>
                    <button id="close-cart-modal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <!-- Lista de productos -->
                <div id="cart-items-list" class="max-h-60 overflow-y-auto space-y-3">
                    <!-- Productos se cargan dinámicamente -->
                </div>
                
                <!-- Total -->
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800">
                        <span>Total:</span>
                        <span class="text-green-600">S/<span id="cart-modal-total">0.00</span></span>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="mt-6 space-y-3">
                    <a href="{{ route('carrito.index') }}" 
                       class="block w-full bg-gradient-green text-white text-center py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-shopping-cart mr-2"></i>Ver Carrito Completo
                    </a>
                    <a href="{{ route('tienda') }}" 
                       class="block w-full bg-gray-100 text-gray-600 text-center py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>Continuar Comprando
                    </a>
                   
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="min-h-screen">
        @if (session('success'))
            <div class="container mx-auto px-4 pt-4">
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm fade-in" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mx-auto px-4 pt-4">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm fade-in" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="text-center">
                <p class="text-gray-400 text-sm">
                    © 2025 <span class="text-green-400 font-semibold">Punto Verde Agroecológico</span> - Amarilis, Huánuco. Todos los derechos reservados
                </p>
            </div>
        </div>
    </footer>
</div>

<!-- JavaScript Optimizado con Cache -->
<script>
    // Variables globales
    let mobileMenuOpen = false;
    
    // OPTIMIZACIÓN: Variables para cache del carrito
    let cartCache = null;
    let cacheTime = null;
    const CACHE_DURATION = 10000; // 10 segundos

    // Toggle mobile menu
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');
        
        mobileMenuOpen = !mobileMenuOpen;
        
        if (mobileMenuOpen) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('mobile-menu-enter');
            mobileMenuIcon.classList.remove('fa-bars');
            mobileMenuIcon.classList.add('fa-times');
        } else {
            mobileMenu.classList.add('hidden');
            mobileMenu.classList.remove('mobile-menu-enter');
            mobileMenuIcon.classList.remove('fa-times');
            mobileMenuIcon.classList.add('fa-bars');
        }
    }

    // Toggle dropdown menus
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const icon = document.getElementById('userMenuIcon');
        
        dropdown.classList.toggle('hidden');
        
        if (icon) {
            if (dropdown.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        }
    }

    // OPTIMIZACIÓN: Función para mostrar productos en modal (separada para reutilizar)
    function mostrarProductosEnModal(response) {
        const itemsList = document.getElementById('cart-items-list');
        
        if (response.items && response.items.length > 0) {
            // OPTIMIZACIÓN: Construir HTML de una vez (más rápido que múltiples innerHTML +=)
            let itemsHTML = '';
            response.items.forEach(function(item) {
                itemsHTML += `
                    <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                        <div class="w-12 h-12 bg-gradient-green rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-leaf text-white"></i>
                        </div>
                        <div class="flex-1 ml-3 min-w-0">
                            <h4 class="font-semibold text-gray-800 truncate">${item.nombre}</h4>
                            <p class="text-sm text-gray-500">Cantidad: ${item.cantidad}</p>
                            <p class="text-sm font-bold text-green-600">S/${item.subtotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;
            });
            itemsList.innerHTML = itemsHTML;
        } else {
            itemsList.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Tu carrito está vacío</p>
                    <a href="{{ route('tienda') }}" class="text-green-600 hover:text-green-700 text-sm font-medium mt-2 inline-block">
                        ¡Explorar productos!
                    </a>
                </div>
            `;
        }

        document.getElementById('cart-modal-total').textContent = response.totalPrice.toFixed(2);
    }

    // OPTIMIZACIÓN: Cargar productos del carrito con cache
    function cargarProductosCarritoConCache() {
        const now = Date.now();
        
        // Si tenemos cache reciente, usarlo
        if (cartCache && cacheTime && (now - cacheTime) < CACHE_DURATION) {
            mostrarProductosEnModal(cartCache);
            return;
        }

        // Mostrar loading inmediatamente
        const itemsList = document.getElementById('cart-items-list');
        itemsList.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-xl text-gray-400"></i>
                <p class="text-gray-500 text-sm mt-1">Cargando...</p>
            </div>
        `;

        $.ajax({
            url: "{{ route('carrito.getDetails') }}",
            method: 'GET',
            timeout: 5000,
            success: function(response) {
                // Guardar en cache
                cartCache = response;
                cacheTime = now;
                
                mostrarProductosEnModal(response);
            },
            error: function() {
                itemsList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-xl text-red-400 mb-2"></i>
                        <p class="text-red-500 text-sm">Error al cargar</p>
                        <button onclick="cargarProductosCarritoConCache()" class="text-green-600 text-sm mt-1 hover:text-green-700">Reintentar</button>
                    </div>
                `;
            }
        });
    }

    // Eventos del modal del carrito
    document.getElementById('cart-button').addEventListener('click', function(e) {
        e.preventDefault();
        const modal = document.getElementById('cart-modal');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            cargarProductosCarritoConCache(); // USAR VERSIÓN CON CACHE
        } else {
            modal.classList.add('hidden');
        }
    });

    document.getElementById('close-cart-modal').addEventListener('click', function() {
        document.getElementById('cart-modal').classList.add('hidden');
    });

    document.getElementById('continue-shopping').addEventListener('click', function() {
        document.getElementById('cart-modal').classList.add('hidden');
    });

    // OPTIMIZACIÓN: Actualizar badge del carrito cuando se agrega un producto
    $(document).ready(function() {
        $('.add-to-cart-form').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let button = form.find('button[type="submit"]');
            let originalText = button.html();

            // Estado de carga
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Agregando...');

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        // OPTIMIZACIÓN: Invalidar cache cuando se agrega producto
                        cartCache = null;
                        cacheTime = null;

                        // Actualizar badge del carrito
                        $('#cart-badge').text(response.totalItems || 0);
                        $('#cart-badge-mobile').text(response.totalItems || 0);

                        // Resetear cantidad del formulario
                        form.find('.quantity-input').val(1);
                        let unitPrice = parseFloat(form.find('.total-price').data('unit-price'));
                        if (unitPrice) {
                            form.find('.total-price').text('S/' + unitPrice.toFixed(2));
                        }

                        // Mensaje de éxito
                        Swal.fire({
                            title: '¡Agregado!',
                            text: 'Producto agregado al carrito',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error al agregar producto';
                    
                    if (xhr.status === 401) {
                        errorMessage = 'Debes iniciar sesión';
                        setTimeout(() => {
                            window.location.href = "{{ route('login') }}";
                        }, 2000);
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        timer: 3000
                    });
                },
                complete: function() {
                    // Restaurar botón
                    button.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    // Cerrar modales al hacer clic afuera
    document.addEventListener('click', function(e) {
        // Cerrar modal del carrito
        const modal = document.getElementById('cart-modal');
        const button = document.getElementById('cart-button');
        
        if (!modal.contains(e.target) && !button.contains(e.target)) {
            modal.classList.add('hidden');
        }
        
        // Cerrar menú de usuario
        if (!e.target.closest('#userMenuButton') && !e.target.closest('#userMenu')) {
            const userMenu = document.getElementById('userMenu');
            const icon = document.getElementById('userMenuIcon');
            if (userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Cerrar menú móvil
        if (!e.target.closest('#mobile-menu-button') && !e.target.closest('#mobile-menu')) {
            if (mobileMenuOpen) {
                toggleMobileMenu();
            }
        }
    });
</script>

@vite(['resources/js/app.js'])
@yield('scripts')
</body>
</html>