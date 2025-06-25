{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="bg-white shadow-md py-4">
    <div class="container mx-auto flex flex-wrap md:flex-nowrap items-center px-4 md:px-20">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('images/logox.png') }}" alt="Ecobazar Logo" class="h-12 w-auto">
        </a>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="block md:hidden text-gray-500 focus:outline-none ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>

        <!-- Navigation & Right Side -->
        <div id="mobile-menu" class="hidden md:flex flex-col md:flex-row items-center w-full md:w-auto md:flex-1 mt-4 md:mt-0">
            <!-- Menu centrado -->
            <div class="flex-1 flex justify-center">
                <ul class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-8 text-gray-500">
                    <li>
                        <a href="{{ url('/') }}" 
                           class="{{ request()->is('/') ? 'text-green-500 font-bold' : 'text-gray-500 hover:text-green-500' }} transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('nosotros') }}" 
                           class="{{ request()->is('nosotros') ? 'text-green-500 font-bold' : 'text-gray-500 hover:text-green-500' }} transition-colors">
                            Nosotros
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="{{ request()->is('tienda') ? 'text-green-500 font-bold' : 'text-gray-500 hover:text-green-500' }} transition-colors">
                            Tienda
                        </a>
                    </li>
                    @auth
                        @if(Auth::user()->role == 'repartidor')
                            <li>
                                <a href="{{ route('repartidor.dashboard') }}" 
                                   class="text-gray-500 hover:text-green-500 transition-colors">
                                    Repartidor Dashboard
                                </a>
                            </li>
                        @elseif(Auth::user()->role == 'agricultor')
                            <li>
                                <a href="{{ route('agricultor.dashboard') }}" 
                                   class="text-gray-500 hover:text-green-500 transition-colors">
                                    Agricultor Dashboard
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- Right side -->
            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 ml-auto">
                @guest
                    <a href="{{ route('login') }}" 
                       class="text-gray-500 hover:text-green-500 transition-colors {{ request()->is('login') ? 'text-green-500 font-bold' : '' }}">
                        Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="text-gray-500 hover:text-green-500 transition-colors {{ request()->is('register') ? 'text-green-500 font-bold' : '' }}">
                            Registrarse
                        </a>
                    @endif
                @else
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="userMenuButton" class="text-gray-500 hover:text-green-500 flex items-center focus:outline-none transition-colors">
                            {{ Auth::user()->name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-20 border">
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <!-- Cart Button -->
                    @php
                        $cartData = app(\App\Services\CartService::class)->getCartSummary();
                    @endphp
                    <a href="#" class="relative flex items-center text-gray-500 hover:text-green-500 transition-colors" id="cart-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.68 9.74a2 2 0 01-1.99 1.76H6.67a2 2 0 01-1.99-1.76L3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                        <span id="cart-total-items" class="ml-2 text-black font-bold">{{ $cartData['totalItems'] ?? 0 }}</span>
                        <span class="ml-2 text-black font-bold">S/<span id="cart-total-price">{{ number_format($cartData['totalPrice'] ?? 0, 2) }}</span></span>
                        
                        @if(($cartData['totalItems'] ?? 0) > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                {{ $cartData['totalItems'] }}
                            </span>
                        @endif
                    </a>
                @endguest
            </div>
        </div>
    </div>
</nav>