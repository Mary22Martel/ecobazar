<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app2.name', 'Admin Dashboard') }}</title>

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.5/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- jQuery y SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @vite('resources/css/app.css')
</head>
  
<body class="bg-gray-50">
    <div id="app">
        <!-- Navbar Responsive -->
        <nav class="bg-white shadow-md py-4" x-data="{ isOpen: false, userMenuOpen: false }">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('images/Logo.png') }}" alt="Ecobazar Logo" class="w-32 md:w-52 transition-all duration-300">
                    </a>

                    <!-- Menú móvil -->
                    <div class="flex lg:hidden">
                        <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Links de navegación - Desktop -->
                    <div class="hidden lg:flex items-center space-x-8">
                        @auth
                            @if(Auth::user()->role == 'repartidor')
                                <a href="{{ route('repartidor.dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">
                                    Panel Repartidor
                                </a>
                            @elseif(Auth::user()->role == 'agricultor')
                                <a href="{{ route('agricultor.dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">
                                    Panel Agricultor
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Right Side Navbar -->
                    <div class="hidden lg:flex items-center space-x-6">
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 {{ request()->is('login') ? 'text-green-600 font-semibold' : '' }}">
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-gray-600 hover:text-green-600 {{ request()->is('register') ? 'text-green-600 font-semibold' : '' }}">
                                    Registrarse
                                </a>
                            @endif
                        @else
                            <div class="relative">
                                <button @click="userMenuOpen = !userMenuOpen" class="text-gray-600 hover:text-green-600 flex items-center focus:outline-none">
                                    {{ Auth::user()->name }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('logout') }}" 
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>

                <!-- Menú móvil - Contenido -->
                <div class="lg:hidden" x-show="isOpen" @click.away="isOpen = false">
                    <div class="pt-4 pb-2 space-y-4">
                        @auth
                            @if(Auth::user()->role == 'repartidor')
                                <a href="{{ route('repartidor.dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                    Panel Repartidor
                                </a>
                            @elseif(Auth::user()->role == 'agricultor')
                                <a href="{{ route('agricultor.dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                    Panel Agricultor
                                </a>
                            @endif
                            
                            <div class="border-t border-gray-200 pt-2">
                                <a href="{{ route('logout') }}" 
                                   class="block px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar Sesión
                                </a>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                    Registrarse
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-0">
            @if (session('success'))
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
