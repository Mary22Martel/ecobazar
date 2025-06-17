<!-- Navigation Component -->
<nav class="bg-white shadow-md border-b border-green-100 sticky top-0 z-40" role="navigation" aria-label="Navegación principal">
    <div class="container mx-auto px-3 sm:px-4 lg:px-6">
        <div class="flex items-center justify-between h-14 sm:h-16">
            
            <!-- Logo Section -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <a href="{{ url('/') }}" class="flex items-center space-x-2 sm:space-x-3 group" aria-label="Punto Verde Inicio">
                    <div class="relative">
                        <img src="{{ asset('images/logox.png') }}" 
                             alt="Punto Verde Logo" 
                             class="h-8 sm:h-10 w-auto transition-transform duration-300 group-hover:scale-105"
                             loading="eager">
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-sm sm:text-base font-bold text-gray-800">Punto Verde</h1>
                        <p class="text-xs text-green-600">Agroecológico</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ url('/') }}" 
                   class="nav-link {{ request()->is('/') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-home mr-1.5 text-xs"></i>Inicio
                </a>
                <a href="{{ route('nosotros') }}" 
                   class="nav-link {{ request()->is('nosotros') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-users mr-1.5 text-xs"></i>Nosotros
                </a>
                <a href="{{ route('tienda') }}" 
                   class="nav-link {{ request()->is('tienda*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-store mr-1.5 text-xs"></i>Tienda
                </a>
            </div>

            <!-- Right Section -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                
                <!-- Cart Button -->
                @php
                    $totalItems = 0;
                    $totalPrice = 0.00;
                    if (Auth::check() && session()->has('cart')) {
                        $cart = session('cart', []);
                        $totalItems = array_sum(array_column($cart, 'quantity'));
                        $totalPrice = array_sum(array_map(function($item) { 
                            return $item['price'] * $item['quantity']; 
                        }, $cart));
                    }
                @endphp
                
                <button id="cart-btn" 
                        class="relative flex items-center space-x-1.5 sm:space-x-2 bg-gradient-to-r from-green-500 to-green-600 text-white px-2.5 sm:px-3 py-2 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105 text-xs sm:text-sm"
                        aria-label="Ver carrito de compras">
                    <i class="fas fa-shopping-cart text-sm sm:text-base"></i>
                    <div class="hidden sm:flex flex-col items-start">
                        <span class="text-xs font-medium">{{ $totalItems }} items</span>
                        <span class="text-xs font-bold">S/{{ number_format($totalPrice, 2) }}</span>
                    </div>
                    @if($totalItems > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-medium animate-pulse">
                            {{ $totalItems > 9 ? '9+' : $totalItems }}
                        </span>
                    @endif
                </button>

                <!-- User Menu / Auth Buttons -->
                @guest
                    <div class="hidden md:flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="text-gray-600 hover:text-green-600 px-2 py-1.5 text-xs font-medium transition-colors duration-200 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-sign-in-alt mr-1"></i>Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200">
                                <i class="fas fa-user-plus mr-1"></i>Registro
                            </a>
                        @endif
                    </div>
                @else
                    <div class="relative">
                        <button id="user-menu-btn" 
                                class="flex items-center space-x-1.5 text-gray-700 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 rounded-lg px-2 py-1.5 transition-all duration-200"
                                aria-expanded="false"
                                aria-haspopup="true">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:block font-medium text-xs truncate max-w-20">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="user-menu-icon"></i>
                        </button>
                        
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-100 z-50 fade-in">
                            <div class="py-1">
                                <div class="px-3 py-2 border-b border-gray-100">
                                    <p class="text-xs text-gray-600">Conectado como</p>
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                </div>
                                @if(Auth::user()->role == 'agricultor')
                                    <a href="{{ route('agricultor.dashboard') }}" 
                                       class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-200">
                                        <i class="fas fa-seedling mr-2 text-xs"></i>Panel Agricultor
                                    </a>
                                @endif
                                <a href="{{ route('logout') }}" 
                                   class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2 text-xs"></i>Cerrar Sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                            </div>
                        </div>
                    </div>
                @endguest

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" 
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                        aria-label="Abrir menú de navegación">
                    <i class="fas fa-bars text-base"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-100 py-2">
            <div class="space-y-1">
                <a href="{{ url('/') }}" 
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is('/') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
                <a href="{{ route('nosotros') }}" 
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is('nosotros') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                    <i class="fas fa-users mr-2"></i>Nosotros
                </a>
                <a href="{{ route('tienda') }}" 
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is('tienda*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }} transition-colors duration-200">
                    <i class="fas fa-store mr-2"></i>Tienda
                </a>
                
                @guest
                    <div class="border-t border-gray-100 pt-2 mt-2 md:hidden">
                        <a href="{{ route('login') }}" 
                           class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:text-green-600 hover:bg-green-50 transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="block px-3 py-2.5 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-colors duration-200 mt-1">
                                <i class="fas fa-user-plus mr-2"></i>Registrarse
                            </a>
                        @endif
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

<script>
    // User menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');
        const userMenuIcon = document.getElementById('user-menu-icon');

        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', function() {
                const isHidden = userMenu.classList.contains('hidden');
                userMenu.classList.toggle('hidden');
                
                if (userMenuIcon) {
                    userMenuIcon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.classList.add('hidden');
                    if (userMenuIcon) userMenuIcon.style.transform = 'rotate(0deg)';
                }
            });
        }
    });
</script>