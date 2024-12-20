<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.5/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <!-- Incluye jQuery y SweetAlert2 al inicio, antes de tus scripts -->
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
                    <li>
                        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-green-500 font-bold' : 'text-gray-500' }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('nosotros') }}" class="{{ request()->is('nosotros') ? 'text-green-500 font-bold' : 'text-gray-500' }}">Nosotros</a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" class="{{ request()->is('tienda') ? 'text-green-500 font-bold' : 'text-gray-500' }}">Tienda</a>
                    </li>
                    <!-- <li>
                        <a href="#" class="{{ request()->is('canastas') ? 'text-green-500 font-bold' : 'text-gray-500' }}">Canastas</a>
                    </li> -->
               
                    @auth
                        @if(Auth::user()->role == 'repartidor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('repartidor.dashboard') }}">{{ __('Repartidor Dashboard') }}</a>
                            </li>
                        @elseif(Auth::user()->role == 'agricultor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('agricultor.dashboard') }}">{{ __('Agricultor Dashboard ') }}</a>
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


            <!-- Botón de carrito -->
            <a href="#" class="relative flex items-center text-gray-500" id="cart-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-600">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.68 9.74a2 2 0 01-1.99 1.76H6.67a2 2 0 01-1.99-1.76L3 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                <span id="cart-total-items" class="ml-2 text-black font-bold">{{ $totalItems }}</span>
            <span class="ml-2 text-black font-bold">S/<span id="cart-total-price">{{ number_format($totalPrice, 2) }}</span></span>

            </a>

            <!-- Modal del carrito -->
            <div id="cart-summary" class="fixed hidden right-4 top-16 w-80 bg-white shadow-lg rounded-lg z-50">
                <div class="p-4">
                    <h3 class="text-lg font-bold mb-4">Carrito de Compras</h3>
                    
                    <!-- Lista de productos en el carrito -->
                    <div id="cart-items-list">
                        <!-- Aquí se añadirán los productos dinámicamente -->
                    </div>
                    
                    <!-- Total en el carrito -->
                    <div class="border-t pt-2 mt-4">
                        <span class="font-bold">Total: S/<span id="cart-popup-total-price">0.00</span></span>
                    </div>
                    
                    <!-- Enlace para ver el carrito completo -->
                    <div class="mt-4 text-right">
                        <a href="{{ route('carrito.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                            Ver carrito de compras
                        </a>
                    </div>
                </div>
            </div>

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

        <!-- Footer --> 
        <footer class="bg-gray-100 text-gray-700 py-20 px-8">
    <div class="container mx-auto max-w-screen-xl px-4">
        <!-- Grid para las columnas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 text-center md:text-left">
            <!-- Logo y descripción -->
            <div class="flex flex-col items-center md:items-start space-y-8">
                <a href="#" class="flex justify-center md:justify-start items-center">
                    <img src="{{ asset('images/Logo.png') }}" alt="Ecobazar Logo" class="w-44">
                </a>
                <p class="text-gray-500 leading-relaxed text-center md:text-left">
                    Somos tu mercado en línea para productos frescos y de calidad provenientes de ferias agrícolas locales. 
                    ¡Compra directamente de los agricultores!
                </p>
                <div class="flex justify-center md:justify-start space-x-6">
                    <a href="#" class="text-2xl text-gray-700 hover:text-green-500 transition transform hover:scale-110">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-2xl text-gray-700 hover:text-green-500 transition transform hover:scale-110">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-2xl text-gray-700 hover:text-green-500 transition transform hover:scale-110">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-2xl text-gray-700 hover:text-green-500 transition transform hover:scale-110">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- Categorías -->
            <div class="flex flex-col items-center md:items-start space-y-8">
                <h3 class="text-lg font-semibold text-gray-800">Categorías</h3>
                <ul class="grid grid-cols-2 gap-y-4">
                    <li><a href="{{ route('tienda') }}" class="text-gray-500 hover:text-green-500 transition hover:underline">Todo</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Vegetales</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Fruta</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Verduras de hojas</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Legumbres</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Queso</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Tubérculos</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Granos</a></li>
                </ul>
            </div>

            <!-- Enlaces útiles -->
            <div class="flex flex-col items-center md:items-start space-y-8">
                <h3 class="text-lg font-semibold text-gray-800">Enlaces Útiles</h3>
                <ul class="space-y-4">
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Inicio</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Sobre Nosotros</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Contacto</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Términos y Condiciones</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">Blog</a></li>
                    <li><a href="#" class="text-gray-500 hover:text-green-500 transition hover:underline">FAQ</a></li>
                </ul>
            </div>

            <!-- Información de contacto -->
            <div class="flex flex-col items-center md:items-start space-y-8">
                <h3 class="text-lg font-semibold text-gray-800">Contáctanos</h3>
                <ul class="space-y-4">
                    <li class="flex justify-center md:justify-start items-center space-x-3">
                        <i class="fas fa-phone-alt text-green-500"></i>
                        <span class="text-gray-500">+51 999 999 999</span>
                    </li>
                    <li class="flex justify-center md:justify-start items-center space-x-3">
                        <i class="fas fa-envelope text-green-500"></i>
                        <span class="text-gray-500">contacto@ecobazar.com</span>
                    </li>
                    <li class="flex justify-center md:justify-start items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-green-500"></i>
                        <span class="text-gray-500">Calle Ejemplo 123, Ciudad, País</span>
                    </li>
                </ul>
                <div class="mt-4">
                    <a href="#" class="text-gray-500 hover:text-green-500 underline">Ver ubicación en el mapa</a>
                </div>
            </div>
        </div>

        <!-- Línea divisoria y créditos -->
        <div class="mt-16 border-t border-gray-200 pt-8">
            <div class="flex flex-col items-center space-y-4">
                <p class="text-sm text-gray-500 text-center">Ecobazar eCommerce © 2024. Todos los derechos reservados</p>
                <img src="{{ asset('images/tarjetas.png') }}" alt="Métodos de pago" class="w-36">
            </div>
        </div>
    </div>
</footer>












    </div>

    <!-- Toggle Dropdown Script -->
    <script>
        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');
            if (!userMenuButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Mostrar/Ocultar el resumen del carrito
        document.getElementById('cart-button').addEventListener('click', function(event) {
            event.preventDefault();  // Prevenir el comportamiento por defecto
            const cartSummary = document.getElementById('cart-summary');
            cartSummary.classList.toggle('hidden');  // Mostrar/ocultar el resumen del carrito
        });


        // Ocultar el dropdown si se hace clic fuera
        document.addEventListener('click', function(event) {
            const cartButton = document.getElementById('cart-button');
            const cartSummary = document.getElementById('cart-summary');
            if (!cartButton.contains(event.target) && !cartSummary.contains(event.target)) {
                cartSummary.classList.add('hidden');
            }
        });
    </script>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Script para manejar el AJAX de agregar al carrito -->
    <script>
   $(document).ready(function() {
    // Maneja la acción de agregar al carrito
    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault();  // Prevenir la recarga de la página

        let form = $(this);  // Formulario específico que se envió
        let actionUrl = form.attr('action');  // URL del formulario

        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: form.serialize(),  // Enviar los datos del formulario
            success: function(response) {
                // Verificar que la respuesta del servidor tenga los datos esperados
                if (response.totalItems !== undefined && response.totalPrice !== undefined) {
                    // Actualizar el ícono del carrito con los nuevos valores
                    $('#cart-total-items').text(response.totalItems);  // Número de productos en el carrito
                    $('#cart-total-price').text(response.totalPrice.toFixed(2));  // Precio total

                    // Limpiar el contenido anterior del modal del carrito
                    $('#cart-items-list').empty();

                    // Recorrer los productos agregados y mostrarlos en el modal
                    response.items.forEach(function(item) {
                        $('#cart-items-list').append(`
                            <div class="flex items-center mb-4">
                                <div class="flex-1 ml-4">
                                    <h4 class="font-bold">${item.nombre}</h4>
                                    <p class="text-gray-500">Cantidad: ${item.cantidad}</p>
                                    <p class="text-green-500">S/${item.subtotal.toFixed(2)}</p>
                                </div>
                                <button class="text-red-500 hover:text-red-700" onclick="removeItem(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `);
                    });

                    // Actualizar el total en el modal del carrito
                    $('#cart-popup-total-price').text(response.totalPrice.toFixed(2));

                    // Mostrar el mensaje de éxito con SweetAlert
                    Swal.fire({
                        title: 'Producto añadido al carrito!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Mostrar el modal del carrito
                    $('#cart-summary').removeClass('hidden');
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'No se pudo agregar el producto. Intenta nuevamente.',
                        icon: 'error',
                        showConfirmButton: true,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema al agregar el producto.',
                    icon: 'error',
                    showConfirmButton: true,
                });
            }
        });
    });
});


        // Mostrar/Ocultar el resumen del carrito al hacer clic en el ícono del carrito o el monto
        $('#cart-button, #cart-total-price').on('click', function(event) {
            event.preventDefault();  // Prevenir el comportamiento predeterminado del enlace
            $('#cart-summary').toggleClass('hidden');  // Mostrar u ocultar el modal del carrito

            // Realizar una llamada AJAX para actualizar los detalles del carrito
            $.ajax({
                type: 'GET',
                url: '{{ route("carrito.getDetails") }}',
                success: function(response) {
                    // Limpiar el contenido anterior del modal del carrito
                    $('#cart-items-list').empty();

                    // Recorrer los productos y agregarlos al modal
                    response.items.forEach(function(item) {
                        $('#cart-items-list').append(`
                            <div class="flex items-center mb-4">
                                <div class="flex-1">
                                    <h4 class="font-bold">${item.nombre}</h4>
                                    <p class="text-gray-500">Cantidad: ${item.cantidad}</p>
                                    <p class="text-green-500">S/${item.subtotal.toFixed(2)}</p>
                                </div>
                                <button class="text-red-500 hover:text-red-700" onclick="removeItem(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `);
                    });

                    // Actualizar el total en el modal
                    $('#cart-popup-total-price').text(response.totalPrice.toFixed(2));
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Hubo un problema al cargar los detalles del carrito.',
                        icon: 'error',
                        showConfirmButton: true,
                    });
                }
            });
        });

        // Ocultar el modal si se hace clic fuera de él
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#cart-button, #cart-summary').length) {
                $('#cart-summary').addClass('hidden');  // Ocultar el modal
            }
        });
    

    // Función para eliminar un producto del carrito
    function removeItem(itemId) {
        $.ajax({
            type: 'POST',
            url: `/carrito/eliminar/${itemId}`,
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Producto eliminado del carrito!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#cart-button, #cart-total-price').click();  // Actualizar el carrito
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'No se pudo eliminar el producto del carrito. Intenta nuevamente.',
                        icon: 'error',
                        showConfirmButton: true,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema al eliminar el producto.',
                    icon: 'error',
                    showConfirmButton: true,
                });
            }
        });
    }
</script>




    @yield('scripts')
</body>
</html>
