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
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 md:px-8">
        <h1 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-bold mb-4">Una parte vital de la Comunidad</h1>
        <p class="text-white text-base sm:text-lg md:text-xl lg:text-3xl font-bold mb-8">
            Compre en nuestro mercado de agricultores en<br class="hidden sm:block">
            línea comida local de calidad.
        </p>
        <div class="flex space-x-4 md:space-x-8 justify-center">
            <a href="tienda" class="bg-green-600 hover:bg-green-700 text-white font-light py-2 px-4 md:py-3 md:px-6 rounded-full inline-flex items-center space-x-2 text-sm md:text-base">
                Comprar Ahora
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-5 md:h-5 ml-1 md:ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Botón Vender -->
<div class="text-center mt-5 px-4">
    <a href="{{ route('agricultor.register') }}" class="inline-block mb-4 px-4 py-3 sm:px-6 sm:py-5 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-lg sm:text-xl">
        Vender en Ecobazar
    </a>
</div>

<!-- Carrusel de la Sección Izquierda -->
<section class="py-8 md:py-16 bg-white px-4 md:px-8 lg:px-20">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
        <!-- Carrusel de imágenes del agricultor (sección izquierda) -->
        <div class="w-full lg:w-1/2 mb-8 lg:mb-0 relative">
            <div id="carousel-left" class="relative w-full h-64 sm:h-96 md:h-96 lg:h-full" style="min-height: 300px;">
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
            <button id="prev" class="absolute left-2 md:left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-1 md:p-2 rounded-full hover:bg-green-700 z-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="next" class="absolute right-2 md:right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-1 md:p-2 rounded-full hover:bg-green-700 z-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Contenido textual -->
        <div class="w-full lg:w-1/2 lg:pl-12 lg:mb-0 text-center lg:text-left">
            <h3 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-green-600 mb-2">Feria Organizada por <span class="text-green">Islas de Paz</span></h3><br>
            <p class="text-gray-600 mb-6 text-sm md:text-base">
                Estamos haciendo que sea más fácil elegir los excelentes alimentos que se producen en nuestros propios centros alimentarios.
            </p>

            <!-- Lista de beneficios -->
            <ul class="text-gray-600 space-y-4 mb-8 text-sm md:text-base">
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6 text-green-500 mr-2 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Obtenga los alimentos locales más frescos y nutritivos disponibles durante todo el año</span>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6 text-green-500 mr-2 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Apoye los intereses de los pequeños productores de alimentos de su comunidad</span>
                </li>
            </ul>

            <!-- Texto adicional -->
            <p class="text-gray-600 mb-6 text-sm md:text-base">
                Ecobazar aumenta el acceso de la comunidad a productos elaborados y cultivados localmente a través de este sistema alimentario reestructurado, que a su vez, sirve a nuestro planeta y a nuestro sentido de identidad y soberanía basado en el lugar.
            </p>
            <p class="text-gray-600 mb-8 text-sm md:text-base">
                ¡Estamos emocionados de que se una a nuestra misión y ponga su dinero donde está su corazón!
            </p>

            <!-- Botón de acción -->
            <a href="#" class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-sm md:text-base">
                Comprar Ahora
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-5 md:h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Cards Derecha -->
<section class="py-8 md:py-16 bg-white px-4 md:px-8 lg:px-20">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
        <!-- Contenido textual -->
        <div class="w-full lg:w-1/2 lg:pr-16 text-center lg:text-left order-2 lg:order-1 mt-8 lg:mt-0">
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl text-green-600 font-bold mb-2">Vender en Ecobazar</h2>
            <br>
            <p class="text-gray-600 mb-6 text-sm md:text-base">
                Ecobazar es un mercado colaborativo que reúne a varios agricultores y productores en una tienda local en línea. Así es como funciona:
            </p>

            <!-- Lista de pasos -->
            <ul class="text-gray-600 space-y-4 mb-8 text-left text-sm md:text-base">
                <li class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6 text-green-500 mr-2 mt-0.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Los compradores realizan pedidos según su disponibilidad cada semana y pagan por adelantado.</span>
                </li>
                <li class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6 text-green-500 mr-2 mt-0.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Un Market Hub Manager dedicado recibe sus productos por cliente y almacena pedidos de los clientes.</span>
                </li>
                <li class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6 text-green-500 mr-2 mt-0.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Los clientes recogen sus pedidos en un lugar de recogida designado.</span>
                </li>
            </ul>

            <!-- Texto adicional -->
            <p class="text-gray-600 mb-8 text-sm md:text-base">
                Este modelo prepago de cosecha bajo pedido reduce el desperdicio al garantizar que sus clientes obtengan sus productos más frescos mientras usted hace un uso eficiente de su mano de obra, producto y tiempo.
            </p>

            <!-- Botón de acción -->
            <a href="#" class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-sm md:text-base">
                Empezar Ahora
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-5 md:h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Carrusel de imágenes a la derecha -->
        <div class="w-full lg:w-1/2 order-1 lg:order-2">
            <div id="carousel-right" class="relative w-full h-64 sm:h-96 md:h-96 lg:h-full" style="min-height: 300px;">
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
            <button id="prev-right" class="absolute left-2 md:left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-1 md:p-2 rounded-full hover:bg-green-700 z-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="next-right" class="absolute right-2 md:right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-1 md:p-2 rounded-full hover:bg-green-700 z-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</section>

<!-- Script para los carruseles -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carrusel hero (automático)
        const heroItems = document.querySelectorAll('.carousel-item-hero');
        let currentHeroIndex = 0;
        
        function nextHeroSlide() {
            heroItems[currentHeroIndex].classList.remove('opacity-100');
            heroItems[currentHeroIndex].classList.add('opacity-0');
            currentHeroIndex = (currentHeroIndex + 1) % heroItems.length;
            heroItems[currentHeroIndex].classList.remove('opacity-0');
            heroItems[currentHeroIndex].classList.add('opacity-100');
        }
        
        // Cambio automático cada 5 segundos
        setInterval(nextHeroSlide, 5000);
        
        // Carrusel izquierdo (manual)
        const items = document.querySelectorAll('.carousel-item');
        let currentIndex = 0;
        
        document.getElementById('prev').addEventListener('click', function() {
            items[currentIndex].classList.remove('opacity-100');
            items[currentIndex].classList.add('opacity-0');
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            items[currentIndex].classList.remove('opacity-0');
            items[currentIndex].classList.add('opacity-100');
        });
        
        document.getElementById('next').addEventListener('click', function() {
            items[currentIndex].classList.remove('opacity-100');
            items[currentIndex].classList.add('opacity-0');
            currentIndex = (currentIndex + 1) % items.length;
            items[currentIndex].classList.remove('opacity-0');
            items[currentIndex].classList.add('opacity-100');
        });
        
        // Carrusel derecho (manual)
        const rightItems = document.querySelectorAll('.carousel-item-right');
        let currentRightIndex = 0;
        
        document.getElementById('prev-right').addEventListener('click', function() {
            rightItems[currentRightIndex].classList.remove('opacity-100');
            rightItems[currentRightIndex].classList.add('opacity-0');
            currentRightIndex = (currentRightIndex - 1 + rightItems.length) % rightItems.length;
            rightItems[currentRightIndex].classList.remove('opacity-0');
            rightItems[currentRightIndex].classList.add('opacity-100');
        });
        
        document.getElementById('next-right').addEventListener('click', function() {
            rightItems[currentRightIndex].classList.remove('opacity-100');
            rightItems[currentRightIndex].classList.add('opacity-0');
            currentRightIndex = (currentRightIndex + 1) % rightItems.length;
            rightItems[currentRightIndex].classList.remove('opacity-0');
            rightItems[currentRightIndex].classList.add('opacity-100');
        });
    });
</script>

@endsection
