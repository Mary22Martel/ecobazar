@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative h-screen mt-0">
    <!-- Carrusel de imágenes (automático en la sección hero) -->
    <div class="absolute inset-0">
        <div id="carousel" class="relative w-full h-full">
            <!-- Imagen 1 -->
            <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                <img src="{{ asset('images/A3.webp') }}" alt="Punto Verde Agroecológico" class="w-full h-full object-cover">
            </div>
            <!-- Imagen 2 -->
            <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="{{ asset('images/A2.webp') }}" alt="Productores Agroecológicos" class="w-full h-full object-cover">
            </div>
            <!-- Imagen 3 -->
            <div class="carousel-item-hero absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="{{ asset('images/A1.webp') }}" alt="Feria Sabatina Amarilis" class="w-full h-full object-cover">
            </div>
        </div>
    </div>

    <!-- Overlay oscuro -->
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>

    <!-- Contenido sobre la imagen -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 md:px-8">
        <h1 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-bold mb-4">Punto Verde Agroecológico</h1>
        <p class="text-white text-base sm:text-lg md:text-xl lg:text-2xl font-medium mb-4">
            Feria Agrícola Sabatina en Amarilis<br class="hidden sm:block">
            <span class="text-green-400">14 productores de 3 provincias de Huánuco</span>
        </p>
        <p class="text-white text-sm sm:text-base md:text-lg mb-8 max-w-4xl">
            "La agroecología no es solo una técnica, es una forma de vida en armonía con la tierra.<br>
            En comunidad, es compartir la vida con la tierra y entre nosotros."
        </p>
        <div class="flex space-x-4 md:space-x-8 justify-center">
            <a href="tienda" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 md:py-4 md:px-8 rounded-full inline-flex items-center space-x-2 text-base md:text-lg transition-all duration-300 transform hover:scale-105">
                Comprar Productos Frescos
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Información de Ubicación y Horarios -->
<section class="py-8 bg-green-50">
    <div class="container mx-auto px-4 md:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="flex flex-col items-center">
                    <div class="bg-green-100 p-4 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Ubicación</h3>
                    <p class="text-gray-600">Segundo Parque de Paucarbambilla<br>Amarilis, Huánuco, Perú</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-green-100 p-4 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Horarios</h3>
                    <p class="text-gray-600">Sábados<br>6:30 AM - 12:00 PM</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-green-100 p-4 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Productores</h3>
                    <p class="text-gray-600">14 productores agroecológicos<br>de 3 provincias</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Sobre Nuestra Feria -->
<section class="py-8 md:py-16 bg-white px-4 md:px-8 lg:px-20">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
        <!-- Carrusel de imágenes del mercado (sección izquierda) -->
        <div class="w-full lg:w-1/2 mb-8 lg:mb-0 relative">
            <div id="carousel-left" class="relative w-full h-64 sm:h-96 md:h-96 lg:h-full" style="min-height: 400px;">
                <!-- Imagen 1 -->
                <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                    <img src="{{ asset('images/M1.jpg') }}" alt="Productores en la feria" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
                <!-- Imagen 2 -->
                <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/M2.jpg') }}" alt="Productos frescos" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
                <!-- Imagen 3 -->
                <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/M3.jpg') }}" alt="Comunidad comprando" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
                <!-- Imagen 4 -->
                <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/M4.jpg') }}" alt="Agricultura sostenible" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
            </div>

            <!-- Flechas de navegación -->
            <button id="prev" class="absolute left-2 md:left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 md:p-3 rounded-full hover:bg-green-700 z-20 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="next" class="absolute right-2 md:right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 md:p-3 rounded-full hover:bg-green-700 z-20 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Contenido textual -->
        <div class="w-full lg:w-1/2 lg:pl-12 lg:mb-0 text-center lg:text-left">
            <h3 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-green-600 mb-6">
                Asociación de Productores <span class="text-green-800">Agroecológicos</span>
            </h3>
            <p class="text-gray-700 mb-6 text-base md:text-lg leading-relaxed">
                Somos una comunidad unida por la pasión de cultivar alimentos saludables y sostener nuestra tierra. 
                Cada sábado nos reunimos para compartir los frutos de nuestro trabajo agroecológico.
            </p>

            <!-- Lista de beneficios -->
            <ul class="text-gray-700 space-y-4 mb-8 text-base md:text-lg">
                <li class="flex items-start">
                    <span class="text-2xl mr-3 mt-1">🌱</span>
                    <span>Productos 100% agroecológicos sin químicos dañinos para tu salud</span>
                </li>
                <li class="flex items-start">
                    <span class="text-2xl mr-3 mt-1">🤝</span>
                    <span>Apoyas directamente a familias productoras de tu región</span>
                </li>
                <li class="flex items-start">
                    <span class="text-2xl mr-3 mt-1">🌍</span>
                    <span>Contribuyes al cuidado del medio ambiente y la biodiversidad</span>
                </li>
                <li class="flex items-start">
                    <span class="text-2xl mr-3 mt-1">💚</span>
                    <span>Fortaleces la economía local y el comercio justo</span>
                </li>
            </ul>

            <!-- Productos disponibles -->
            <div class="mb-8">
                <h4 class="text-xl font-semibold text-gray-800 mb-4">Productos que encontrarás:</h4>
                <div class="text-3xl leading-relaxed">
                    🍊🍋🥑🥦🥬🥒🍆🌽🥕🥔🥐🫑🍲🍜☕
                </div>
            </div>

            <!-- Botón de acción -->
            <a href="tienda" class="inline-flex items-center px-6 py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-base md:text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                Explorar Productos
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Sección Impacto Social -->
<section class="py-8 md:py-16 bg-green-50 px-4 md:px-8 lg:px-20">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between">
        <!-- Contenido textual -->
        <div class="w-full lg:w-1/2 lg:pr-16 text-center lg:text-left order-2 lg:order-1 mt-8 lg:mt-0">
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl text-green-600 font-bold mb-6">
                Impacto en Nuestra Comunidad
            </h2>
            <p class="text-gray-700 mb-6 text-base md:text-lg leading-relaxed">
                Cada compra que realizas genera un impacto positivo que va más allá de alimentarte bien. 
                Estás siendo parte de un movimiento que transforma vidas y cuida nuestro planeta.
            </p>

            <!-- Lista de impactos -->
            <ul class="text-gray-700 space-y-6 mb-8 text-left text-base md:text-lg">
                <li class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mt-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <strong>Salud familiar:</strong> Productos libres de pesticidas que nutren tu cuerpo y el de tu familia.
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mt-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <strong>Desarrollo económico:</strong> Generas ingresos justos para 14 familias productoras de la región.
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mt-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                        </svg>
                    </div>
                    <div>
                        <strong>Cuidado ambiental:</strong> Promovemos prácticas que regeneran el suelo y protegen la biodiversidad.
                    </div>
                </li>
            </ul>

            <!-- Mensaje inspiracional -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                <p class="text-gray-700 italic text-lg leading-relaxed">
                    "Cuando compras en nuestra feria, no solo adquieres alimentos frescos y nutritivos, 
                    te conviertes en parte de una familia que cree en la armonía entre el ser humano y la naturaleza."
                </p>
            </div>

            <!-- Botón de acción -->
            <a href="tienda" class="inline-flex items-center px-6 py-4 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 text-base md:text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                Únete a Nuestra Comunidad
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Carrusel de imágenes a la derecha -->
        <div class="w-full lg:w-1/2 order-1 lg:order-2">
            <div id="carousel-right" class="relative w-full h-64 sm:h-96 md:h-96 lg:h-full" style="min-height: 400px;">
                <!-- Imagen 1 -->
                <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                    <img src="{{ asset('images/imagen1.jpg') }}" alt="Impacto social" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
                <!-- Imagen 2 -->
                <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/E2.jpeg') }}" alt="Comunidad agroecológica" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
                <!-- Imagen 3 -->
                <div class="carousel-item-right absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                    <img src="{{ asset('images/E3.jpeg') }}" alt="Agricultura sostenible" class="w-full h-full object-cover rounded-lg shadow-lg">
                </div>
            </div>

            <!-- Flechas de navegación -->
            <button id="prev-right" class="absolute left-2 md:left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 md:p-3 rounded-full hover:bg-green-700 z-20 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="next-right" class="absolute right-2 md:right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 md:p-3 rounded-full hover:bg-green-700 z-20 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
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