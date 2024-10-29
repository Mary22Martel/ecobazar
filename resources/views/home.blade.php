@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-screen mt-0">
        <!-- Carrusel de imágenes (automático en la sección hero) -->
        <div class="absolute inset-0">
            <div id="carousel" class="relative w-full h-full">
                <!-- Imagen 1 -->
                <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                    <img src="{{ asset('images/A3.webp') }}" alt="Background Image 1" class="w-full h-full object-cover">
                </div>
                <!-- Imagen 2 -->
                <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/A2.webp') }}" alt="Background Image 2" class="w-full h-full object-cover">
                </div>
                <!-- Imagen 3 -->
                <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/A1.webp') }}" alt="Background Image 3" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Overlay oscuro -->
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <!-- Contenido sobre la imagen -->
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <h1 class="text-white text-5xl md:text-7xl font-bold mb-4">Una parte vital de la Comunidad</h1>
            <p class="text-white text-lg font-bold md:text-3xl mb-8">
                Compre en nuestro mercado de agricultores en<br>
                línea comida local de calidad.
            </p>
            <div class="flex space-x-8 justify-center">
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-ligth py-3 px-6 rounded-full inline-flex items-center space-x-2">
                    Mirar Video
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-5.197-3.482A1 1 0 008 8.481v7.038a1 1 0 001.555.832l5.197-3.482a1 1 0 000-1.664z" />
                    </svg>
                </a>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-ligth py-3 px-6 rounded-full inline-flex items-center space-x-2">
                    Comprar Ahora
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Botón Vender -->
    <div class="text-center mt-5">
        <a href="{{ route('agricultor.register') }}" class="inline-block mb-4 px-6 py-5 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-xl">
            Vender en Ecobazar
        </a>
    </div>

    <!-- Carrusel de la Sección Izquierda -->
    <section class="py-16 bg-white px-20">
        <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
            <!-- Carrusel de imágenes del agricultor (sección izquierda) -->
            <div class="lg:w-1/2 mb-8 lg:mb-0 relative">
                <div id="carousel-left" class="relative w-full h-full" style="min-height: 400px;"> <!-- Aseguramos un tamaño mínimo -->
                    <!-- Imagen 1 -->
                    <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                        <img src="{{ asset('images/M1.jpg') }}" alt="Farmer Image 1" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                    <!-- Imagen 2 -->
                    <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                        <img src="{{ asset('images/M2.jpg') }}" alt="Farmer Image 2" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                    <!-- Imagen 3 -->
                    <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                        <img src="{{ asset('images/M3.jpg') }}" alt="Farmer Image 3" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                    <!-- Imagen 4 -->
                    <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                        <img src="{{ asset('images/M4.jpg') }}" alt="Farmer Image 4" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                </div>

                <!-- Flechas de navegación -->
                <button id="prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="next" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Contenido textual -->
            <div class="lg:w-1/2 lg:pl-12 lg:mb-0 text-left lg:text-left">
                <h3 class="text-6xl font-bold text-green-600 mb-0">Feria Organizada por <span class="text-green">Islas de Paz</span></h3><br>
                <p class="text-gray-600 mb-6">
                    Estamos haciendo que sea más fácil elegir los excelentes alimentos que se producen en nuestros propios centros alimentarios.
                </p>

                <!-- Lista de beneficios -->
                <ul class="text-gray-600 space-y-4 mb-8">
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-500 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Obtenga los alimentos locales más frescos y nutritivos disponibles durante todo el año
                    </li>
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-500 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Apoye los intereses de los pequeños productores de alimentos de su comunidad
                    </li>
                </ul>

                <!-- Texto adicional -->
                <p class="text-gray-600 mb-6">
                    Ecobazar aumenta el acceso de la comunidad a productos elaborados y cultivados localmente a través de este sistema alimentario reestructurado, que a su vez, sirve a nuestro planeta y a nuestro sentido de identidad y soberanía basado en el lugar.
                </p>
                <p class="text-gray-600 mb-8">
                    ¡Estamos emocionados de que se una a nuestra misión y ponga su dinero donde está su corazón!
                </p>

                <!-- Botón de acción -->
                <a href="#" class="inline-flex items-center px-6 py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700">
                    Comprar Ahora
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <!-- Cards Derecha -->
    <section class="py-16 bg-white px-20">
        <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
            <!-- Contenido textual -->
            <div class="lg:w-1/2 lg:pr-16">
                <h2 class="text-green-600 text-6xl font-bold mb-4">Vender en Ecobazar</h2>
                <br>
                <p class="text-gray-600 mb-6">
                    Ecobazar es un mercado colaborativo que reúne a varios agricultores y productores en una tienda local en línea. Así es como funciona:
                </p>

                <!-- Lista de pasos -->
                <ul class="text-gray-600 space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-500 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Los compradores realizan pedidos según su disponibilidad cada semana y pagan por adelantado.
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-500 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Un Market Hub Manager dedicado recibe sus productos por cliente y almacena pedidos de los clientes.
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 text-green-500 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Los clientes recogen sus pedidos en un lugar de recogida designado.
                    </li>
                </ul>

                <!-- Texto adicional -->
                <p class="text-gray-600 mb-8">
                    Este modelo prepago de cosecha bajo pedido reduce el desperdicio al garantizar que sus clientes obtengan sus productos más frescos mientras usted hace un uso eficiente de su mano de obra, producto y tiempo.
                </p>

                <!-- Botón de acción -->
                <a href="#" class="inline-flex items-center px-6 py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700">
                    Empezar Ahora
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Carrusel de imágenes a la derecha -->
            <div class="lg:w-1/2 mt-8 lg:mt-0 relative">
                <div id="carousel-right" class="relative w-full h-full" style="min-height: 500px;">
                    <!-- Imagen 1 -->
                    <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                        <img src="{{ asset('images/E1.jpeg') }}" alt="Farmer Image 1" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                    <!-- Imagen 2 -->
                    <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                        <img src="{{ asset('images/E2.jpeg') }}" alt="Farmer Image 2" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                    <!-- Imagen 3 -->
                    <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                        <img src="{{ asset('images/E3.jpeg') }}" alt="Farmer Image 3" class="w-full h-full object-cover rounded-lg shadow-lg">
                    </div>
                </div>

                <!-- Flechas de navegación -->
                <button id="prev-right" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="next-right" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

<!-- JavaScript para carrusel -->
<script>
    // Variables para controlar el carrusel de la izquierda
    const carouselItems = document.querySelectorAll('#carousel-left .carousel-item');
    let currentIndex = 0;
    let totalItems = carouselItems.length;

    // Función para mostrar la imagen en el índice actual
    function showImage(index) {
        carouselItems.forEach((item, i) => {
            item.classList.remove('opacity-100');
            item.classList.add('opacity-0');
            if (i === index) {
                item.classList.remove('opacity-0');
                item.classList.add('opacity-100');
            }
        });
    }

    // Función para avanzar a la siguiente imagen manualmente
    document.getElementById('next').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % totalItems;
        showImage(currentIndex);
    });

    // Función para retroceder a la imagen anterior manualmente
    document.getElementById('prev').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        showImage(currentIndex);
    });

    // Carrusel automático en la sección Hero
    const heroCarouselItems = document.querySelectorAll('.carousel-item-hero');
    let heroIndex = 0;

    function showNextHeroImage() {
        heroCarouselItems[heroIndex].classList.remove('opacity-100');
        heroCarouselItems[heroIndex].classList.add('opacity-0');
        heroIndex = (heroIndex + 1) % heroCarouselItems.length;
        heroCarouselItems[heroIndex].classList.remove('opacity-0');
        heroCarouselItems[heroIndex].classList.add('opacity-100');
    }

    // Cambiar la imagen del carrusel Hero cada 5 segundos
    setInterval(showNextHeroImage, 5000);

    // Variables para controlar el carrusel de la derecha
    const rightCarouselItems = document.querySelectorAll('#carousel-right .carousel-item-right');
    let rightCurrentIndex = 0;
    let totalRightItems = rightCarouselItems.length;

    // Función para mostrar la imagen en el índice actual
    function showRightImage(index) {
        rightCarouselItems.forEach((item, i) => {
            item.classList.remove('opacity-100');
            item.classList.add('opacity-0');
            if (i === index) {
                item.classList.remove('opacity-0');
                item.classList.add('opacity-100');
            }
        });
    }

    // Función para avanzar a la siguiente imagen manualmente
    document.getElementById('next-right').addEventListener('click', () => {
        rightCurrentIndex = (rightCurrentIndex + 1) % totalRightItems;
        showRightImage(rightCurrentIndex);
    });

    // Función para retroceder a la imagen anterior manualmente
    document.getElementById('prev-right').addEventListener('click', () => {
        rightCurrentIndex = (rightCurrentIndex - 1 + totalRightItems) % totalRightItems;
        showRightImage(rightCurrentIndex);
    });
</script>

@endsection
