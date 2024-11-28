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
  
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="bg-white shadow-md py-4">
            <div class="container mx-auto flex justify-between items-center px-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center">
                    <img src="{{ asset('images/Logo.png') }}" alt="Ecobazar Logo" class="w-52">
                </a>
                
                <!-- Links de navegación -->
                <ul class="flex space-x-8 text-gray-500">
                    
                    @auth
                        @if(Auth::user()->role == 'repartidor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('repartidor.dashboard') }}">{{ __('Panel administrativo repartidor') }}</a>
                            </li>
                        @elseif(Auth::user()->role == 'agricultor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('agricultor.dashboard') }}">{{ __('Panel administrativo agricultor') }}</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <!-- Right Side Navbar -->
                <div class="flex items-center space-x-6">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-500 {{ request()->is('login') ? 'text-green-500 font-bold' : '' }}">Iniciar Sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-gray-500 {{ request()->is('register') ? 'text-green-500 font-bold' : '' }}">Registrarse</a>
                        @endif
                    @else
                        <div class="relative">
                            <button id="userMenuButton" class="text-gray-500 flex items-center focus:outline-none" onclick="toggleDropdown('userMenu')">
                                {{ Auth::user()->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar Sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-0">
            @if (session('success'))
                <div class="container mx-auto">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        // Función para alternar el menú desplegable
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener('click', function(event) {
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');

            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
