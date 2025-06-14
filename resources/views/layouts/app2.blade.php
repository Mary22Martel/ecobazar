<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Punto Verde Agroecológico') }}</title>

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.5/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
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
        
        /* Efectos hover mejorados */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::before {
            width: 100%;
        }
        
        /* Gradientes personalizados */
        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .bg-gradient-emerald {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }
        
        /* Sombras personalizadas */
        .shadow-green {
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.15);
        }
        
        .shadow-green-lg {
            box-shadow: 0 10px 25px -3px rgba(16, 185, 129, 0.2);
        }
        
        /* Backdrop blur para móvil */
        .backdrop-blur-mobile {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
    </style>

    @vite('resources/css/app.css')
</head>
  
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div id="app">
        <!-- Navbar Responsive Mejorado -->
        <nav class="bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-100 sticky top-0 z-50" 
             x-data="{ isOpen: false, userMenuOpen: false }">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 lg:h-20">
                    
                    <!-- Logo mejorado -->
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                            <div class="relative">
                                <img src="{{ asset('images/logox.png') }}" 
                                     alt="Punto Verde Logo" 
                                     class="h-10 w-auto sm:h-12 transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 rounded-full bg-green-400 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            </div>
                            <div class="hidden sm:block">
                                <h1 class="text-lg lg:text-xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    Punto Verde
                                </h1>
                                <p class="text-xs text-gray-500 -mt-1">Agroecológico</p>
                            </div>
                        </a>
                    </div>

                    <!-- Botón menú móvil mejorado -->
                    <div class="flex lg:hidden">
                        <button @click="isOpen = !isOpen" 
                                class="relative inline-flex items-center justify-center p-2 rounded-xl text-gray-600 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                            <span class="sr-only">Abrir menú principal</span>
                            <svg class="h-6 w-6 transition-transform duration-200"
                                 :class="{ 'rotate-90': isOpen }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Links de navegación - Desktop -->
                    <div class="hidden lg:flex items-center space-x-1">
                        @auth
                            @if(Auth::user()->role == 'repartidor')
                                <a href="{{ route('repartidor.dashboard') }}" 
                                   class="nav-link px-4 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <i class="fas fa-truck mr-2"></i>
                                    Panel Repartidor
                                </a>
                            @elseif(Auth::user()->role == 'agricultor')
                                <a href="{{ route('agricultor.dashboard') }}" 
                                   class="nav-link px-4 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <i class="fas fa-seedling mr-2"></i>
                                    Panel Agricultor
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Right Side Navbar mejorado -->
                    <div class="hidden lg:flex items-center space-x-4">
                        @guest
                            <a href="{{ route('login') }}" 
                               class="nav-link px-4 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium {{ request()->is('login') ? 'text-green-600 bg-green-50' : '' }}">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="bg-gradient-green text-white px-6 py-2 rounded-lg hover:shadow-green transition-all duration-200 font-medium transform hover:scale-105 {{ request()->is('register') ? 'shadow-green-lg' : '' }}">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Registrarse
                                </a>
                            @endif
                        @else
                            <!-- Usuario autenticado -->
                            <div class="relative" x-data="{ userMenuOpen: false }">
                                <button @click="userMenuOpen = !userMenuOpen" 
                                        class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gradient-green rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </div>
                                        <div class="hidden sm:block text-left">
                                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                        </div>
                                    </div>
                                    <svg class="h-4 w-4 transition-transform duration-200" 
                                         :class="{ 'rotate-180': userMenuOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu mejorado -->
                                <div x-show="userMenuOpen" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-1 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-1 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     @click.away="userMenuOpen = false" 
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                    
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                    </div>
                                    
                                    <a href="{{ route('logout') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>

                <!-- Menú móvil mejorado -->
                <div class="lg:hidden overflow-hidden" 
                     x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-1 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-1 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4">
                    
                    <div class="pb-4 pt-2 space-y-2 bg-white border-t border-gray-100">
                        @auth
                            @if(Auth::user()->role == 'repartidor')
                                <a href="{{ route('repartidor.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-all duration-200 font-medium rounded-lg mx-2">
                                    <i class="fas fa-truck mr-3 w-5"></i>
                                    Panel Repartidor
                                </a>
                            @elseif(Auth::user()->role == 'agricultor')
                                <a href="{{ route('agricultor.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-all duration-200 font-medium rounded-lg mx-2">
                                    <i class="fas fa-seedling mr-3 w-5"></i>
                                    Panel Agricultor
                                </a>
                            @endif
                            
                            <!-- Perfil del usuario en móvil -->
                            <div class="border-t border-gray-100 pt-2 mt-2">
                                <div class="flex items-center px-4 py-3 mx-2">
                                    <div class="w-10 h-10 bg-gradient-green rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                    </div>
                                </div>
                                
                                <a href="{{ route('logout') }}" 
                                   class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-all duration-200 font-medium rounded-lg mx-2"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        @else
                            <a href="{{ route('login') }}" 
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-all duration-200 font-medium rounded-lg mx-2">
                                <i class="fas fa-sign-in-alt mr-3 w-5"></i>
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="flex items-center px-4 py-3 mx-2 bg-gradient-green text-white rounded-lg font-medium transition-all duration-200 hover:shadow-green">
                                    <i class="fas fa-user-plus mr-3 w-5"></i>
                                    Registrarse
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content con mejor espaciado -->
        <main class="min-h-screen pb-8">
            <!-- Alertas mejoradas -->
            @if (session('success'))
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm fade-in" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('error'))
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm fade-in" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('warning'))
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm fade-in" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-3"></i>
                            <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('info'))
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg shadow-sm fade-in" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                            <p class="text-blue-800 font-medium">{{ session('info') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Content -->
            @yield('content')
        </main>

        <!-- Footer mejorado (opcional) -->
        <footer class="bg-white border-t border-gray-100 mt-auto">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm">
                        © {{ date('Y') }} Punto Verde Agroecológico. Todos los derechos reservados.
                    </p>
                    <div class="flex items-center mt-2 sm:mt-0">
                        <span class="text-gray-400 text-sm mr-2">Desarrollado con</span>
                        <i class="fas fa-heart text-green-500"></i>
                        <span class="text-gray-400 text-sm ml-2">para nuestros agricultores</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>