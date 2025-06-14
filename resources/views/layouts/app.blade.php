<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 10px;
        }
        
        /* Backdrop blur for modals */
        .backdrop-blur-custom {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        
        /* Loading states */
        .loading {
            position: relative;
            overflow: hidden;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        /* Lazy loading images */
        img.lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        img.lazy.loaded {
            opacity: 1;
        }
        
        /* Success/Error message animations */
        .alert-enter {
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Focus styles for accessibility */
        .focus-visible:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .nav-link::after {
                background: currentColor;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            nav, footer {
                display: none !important;
            }
            
            .container {
                max-width: none !important;
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
                        @endif
                    @endauth
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Cart Button -->
                    @php
                        use Illuminate\Support\Facades\Auth;
                        use App\Models\Carrito;

                        $carrito = null;
                        $totalItems = 0;
                        $totalPrice = 0.00;

                        if (Auth::check()) {
                            $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();
                            if ($carrito) {
                                $totalItems = $carrito->items->sum('cantidad');
                                $totalPrice = $carrito->items->sum(function ($item) {
                                    return $item->product->precio * $item->cantidad;
                                });
                            }
                        }
                    @endphp
                    
                    <button id="cart-button" 
                            class="relative flex items-center space-x-2 bg-gradient-green text-white px-4 py-2 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <div class="hidden sm:flex flex-col items-start">
                            <span id="cart-total-items" class="text-xs font-medium">{{ $totalItems }} items</span>
                            <span class="text-sm font-bold">S/<span id="cart-total-price">{{ number_format($totalPrice, 2) }}</span></span>
                        </div>
                        @if($totalItems > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ $totalItems > 9 ? '9+' : $totalItems }}
                            </span>
                        @endif
                    </button>

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
                        @endif
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
        
        <!-- Cart Modal Mejorado -->
        <div id="cart-summary" class="fixed hidden right-4 top-20 w-80 sm:w-96 bg-white shadow-2xl rounded-2xl z-50 border border-gray-100 fade-in">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                        Mi Carrito
                    </h3>
                    <button onclick="closeCartModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <!-- Lista de productos en el carrito -->
                <div id="cart-items-list" class="max-h-60 overflow-y-auto custom-scrollbar space-y-3">
                    <!-- Aquí se añadirán los productos dinámicamente -->
                </div>
                
                <!-- Total en el carrito -->
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800">
                        <span>Total:</span>
                        <span class="text-green-600">S/<span id="cart-popup-total-price">0.00</span></span>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="mt-6 space-y-3">
                    <a href="{{ route('carrito.index') }}" 
                       class="block w-full bg-gradient-green text-white text-center py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i>Ver Carrito Completo
                    </a>
                    <button onclick="closeCartModal()" 
                            class="block w-full bg-gray-100 text-gray-600 text-center py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors duration-200">
                        Continuar Comprando
                    </button>
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

    <!-- Footer Mejorado --> 
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                
                <!-- Columna 1 - Logo y descripción -->
                <div class="flex flex-col items-center lg:items-start space-y-6">
                    <div class="text-center lg:text-left">
                        <img src="{{ asset('images/logox.png') }}" alt="Punto Verde Logo" class="w-16 mx-auto lg:mx-0 mb-4">
                        <h3 class="text-xl font-bold text-white mb-2">Punto Verde Agroecológico</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Feria Agrícola Sabatina en Amarilis - Huánuco<br>
                            14 productores agroecológicos de 3 provincias.<br>
                            <span class="text-green-400 font-medium">¡Productos frescos directamente del campo!</span>
                        </p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/islasdepazperu" target="_blank" 
                           class="w-10 h-10 bg-gray-800 hover:bg-blue-600 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="https://www.instagram.com/puntoverde.huanuco/" target="_blank" 
                           class="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Columna 2 - Productos -->
                <div class="text-center lg:text-left">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center justify-center lg:justify-start">
                        <i class="fas fa-leaf text-green-400 mr-2"></i>
                        Nuestros Productos
                    </h3>
                    <ul class="grid grid-cols-2 gap-3">
                        <li><a href="{{ route('tienda') }}" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-seedling text-xs mr-2"></i>Todos
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-carrot text-xs mr-2"></i>Vegetales
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-apple-alt text-xs mr-2"></i>Frutas
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-leaf text-xs mr-2"></i>Verduras
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-seedling text-xs mr-2"></i>Legumbres
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-cheese text-xs mr-2"></i>Quesos
                        </a></li>
                    </ul>
                </div>

                <!-- Columna 3 - Navegación -->
                <div class="text-center lg:text-left">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center justify-center lg:justify-start">
                        <i class="fas fa-compass text-green-400 mr-2"></i>
                        Navegación
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-home text-xs mr-2"></i>Inicio
                        </a></li>
                        <li><a href="{{ route('nosotros') }}" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-users text-xs mr-2"></i>Sobre Nosotros
                        </a></li>
                        <li><a href="{{ route('tienda') }}" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-store text-xs mr-2"></i>Tienda
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 text-sm transition-all duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-envelope text-xs mr-2"></i>Contacto
                        </a></li>
                    </ul>
                </div>

                <!-- Columna 4 - Información de contacto -->
                <div class="text-center lg:text-left">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center justify-center lg:justify-start">
                        <i class="fas fa-info-circle text-green-400 mr-2"></i>
                        Información
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3 justify-center lg:justify-start">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-map-marker-alt text-white text-xs"></i>
                            </div>
                            <div class="text-gray-400 text-sm text-center lg:text-left">
                                <strong class="text-white">Ubicación:</strong><br>
                                Segundo Parque de Paucarbambilla<br>
                                Amarilis, Huánuco, Perú
                            </div>
                        </li>
                        <li class="flex items-start space-x-3 justify-center lg:justify-start">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            <div class="text-gray-400 text-sm text-center lg:text-left">
                                <strong class="text-white">Horarios:</strong><br>
                                Sábados 6:30 AM - 12:00 PM
                            </div>
                        </li>
                        <li class="flex items-start space-x-3 justify-center lg:justify-start">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-envelope text-white text-xs"></i>
                            </div>
                            <div class="text-gray-400 text-sm text-center lg:text-left">
                                <strong class="text-white">Correo:</strong><br>
                                <a href="mailto:ong_idpp@islasdepazperu.org" class="text-gray-400 hover:text-green-400 transition-colors duration-200">
                                    ong_idpp@islasdepazperu.org
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 pt-8 border-t border-gray-700">
                <div class="text-center">
                    <p class="text-gray-400 text-sm">
                        © 2025 <span class="text-green-400 font-semibold">Punto Verde Agroecológico</span> - Amarilis, Huánuco. Todos los derechos reservados<br>
                        <span class="text-xs">Una iniciativa de la Asociación de Productores Agroecológicos</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- JavaScript mejorado -->
<script>
    // Variables globales
    let mobileMenuOpen = false;
    let cartModalOpen = false;

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

    // Close cart modal
    function closeCartModal() {
        const cartSummary = document.getElementById('cart-summary');
        cartSummary.classList.add('hidden');
        cartModalOpen = false;
    }

    // Cart functionality
    document.getElementById('cart-button').addEventListener('click', function(e) {
        e.preventDefault();
        const cartSummary = document.getElementById('cart-summary');
        
        cartModalOpen = !cartModalOpen;
        
        if (cartModalOpen) {
            cartSummary.classList.remove('hidden');
            loadCartItems();
        } else {
            cartSummary.classList.add('hidden');
        }
    });

    // Load cart items
    function loadCartItems() {
        $.ajax({
            type: 'GET',
            url: '{{ route("carrito.getDetails") }}',
            success: function(response) {
                const cartItemsList = document.getElementById('cart-items-list');
                cartItemsList.innerHTML = '';

                if (response.items && response.items.length > 0) {
                    response.items.forEach(function(item) {
                        cartItemsList.innerHTML += `
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                <div class="w-12 h-12 bg-gradient-green rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-leaf text-white"></i>
                                </div>
                                <div class="flex-1 ml-3 min-w-0">
                                    <h4 class="font-semibold text-gray-800 truncate">${item.nombre}</h4>
                                    <p class="text-sm text-gray-500">Cantidad: ${item.cantidad}</p>
                                    <p class="text-sm font-bold text-green-600">S/${item.subtotal.toFixed(2)}</p>
                                </div>
                                <button class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200 transform hover:scale-110" 
                                        onclick="removeItem(${item.id})" title="Eliminar producto">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    cartItemsList.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tu carrito está vacío</p>
                            <a href="{{ route('tienda') }}" class="text-green-600 hover:text-green-700 text-sm font-medium mt-2 inline-block">
                                ¡Explorar productos!
                            </a>
                        </div>
                    `;
                }

                document.getElementById('cart-popup-total-price').textContent = response.totalPrice.toFixed(2);
            },
            error: function(xhr, status, error) {
                console.error('Error loading cart items:', error);
            }
        });
    }

    // Remove item from cart
    function removeItem(itemId) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: "¿Estás seguro de que quieres eliminar este producto del carrito?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    type: 'POST',
                    url: `/carrito/eliminar/${itemId}`,
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                const response = result.value;
                if (response.success) {
                    // Update cart counts
                    document.getElementById('cart-total-items').textContent = response.totalItems + ' items';
                    document.getElementById('cart-total-price').textContent = response.totalPrice.toFixed(2);
                    
                    // Add bounce animation to cart button
                    const cartButton = document.getElementById('cart-button');
                    cartButton.classList.add('cart-bounce');
                    setTimeout(() => cartButton.classList.remove('cart-bounce'), 600);
                    
                    // Reload cart items
                    loadCartItems();
                    
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El producto ha sido eliminado del carrito.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar el producto del carrito.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        // Close user menu
        if (!e.target.closest('#userMenuButton') && !e.target.closest('#userMenu')) {
            const userMenu = document.getElementById('userMenu');
            const icon = document.getElementById('userMenuIcon');
            if (userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Close cart modal
        if (!e.target.closest('#cart-button') && !e.target.closest('#cart-summary')) {
            const cartSummary = document.getElementById('cart-summary');
            if (cartSummary && !cartSummary.classList.contains('hidden')) {
                closeCartModal();
            }
        }
        
        // Close mobile menu
        if (!e.target.closest('#mobile-menu-button') && !e.target.closest('#mobile-menu')) {
            if (mobileMenuOpen) {
                toggleMobileMenu();
            }
        }
    });

    // Enhanced form submission for adding to cart
    $(document).ready(function() {
        $('.add-to-cart-form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let actionUrl = form.attr('action');
            let submitButton = form.find('button[type="submit"]');
            
            // Show loading state
            let originalText = submitButton.html();
            submitButton.html('<i class="fas fa-spinner fa-spin mr-2"></i>Agregando...').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: form.serialize(),
                success: function(response) {
                    if (response.totalItems !== undefined && response.totalPrice !== undefined) {
                        // Update cart display
                        $('#cart-total-items').text(response.totalItems + ' items');
                        $('#cart-total-price').text(response.totalPrice.toFixed(2));

                        // Add bounce animation to cart button
                        const cartButton = document.getElementById('cart-button');
                        cartButton.classList.add('cart-bounce');
                        setTimeout(() => cartButton.classList.remove('cart-bounce'), 600);

                        // Show success message
                        Swal.fire({
                            title: '¡Agregado al carrito!',
                            text: 'El producto se ha agregado exitosamente.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        // Auto-open cart modal
                        setTimeout(() => {
                            document.getElementById('cart-button').click();
                        }, 500);

                    } else {
                        throw new Error('Respuesta inválida del servidor');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Hubo un problema al agregar el producto.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#10b981'
                    });
                },
                complete: function() {
                    // Restore button state
                    submitButton.html(originalText).prop('disabled', false);
                }
            });
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading state to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
                submitButton.disabled = true;
                
                // Re-enable after 5 seconds as failsafe
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 5000);
            }
        });
    });

    // Initialize tooltips (if using Bootstrap tooltips)
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
</script>

<!-- Scripts adicionales -->
@vite(['resources/js/app.js'])

@yield('scripts')