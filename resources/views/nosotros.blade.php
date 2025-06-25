@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 lg:mt-12 px-4 sm:px-6 lg:px-8 max-w-7xl">
    <!-- Encabezado -->
    <div class="text-center mb-10 lg:mb-16 space-y-6">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-green-800">
                Nuestra Misión
            </span>
        </h1>
        
        <p class="mx-auto text-gray-600 text-base sm:text-lg md:text-xl max-w-4xl leading-relaxed md:leading-loose">
            En nuestra plataforma, trabajamos para conectar agricultores de Huánuco con clientes conscientes, ofreciendo productos de alta calidad a precios justos.
        </p>
    </div>

    <!-- Sección de objetivos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mb-14 lg:mb-20">
        <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-900">Apoyamos a los Agricultores</h2>
            </div>
            <p class="text-gray-600 text-base lg:text-lg leading-relaxed">
                Mejoramos las ventas de agricultores locales, llevando productos frescos y de calidad a más hogares. Conectamos directamente con los consumidores para un comercio justo y transparente.
            </p>
        </div>

        <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 ease-in-out transform hover:-translate-y-1">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-900">Conectar Mercados</h2>
            </div>
            <p class="text-gray-600 text-base lg:text-lg leading-relaxed">
                Unimos mercados regionales para ofrecer variedad de productos, facilitando la compra desde cualquier lugar con entrega directa a domicilio.
            </p>
        </div>
    </div>

    <!-- Imagen destacada -->
    <div class="mb-14 lg:mb-20">
        <div class="relative overflow-hidden rounded-2xl lg:rounded-3xl aspect-w-16 aspect-h-9">
            <img src="{{ asset('images/M1.jpg') }}" alt="Agricultores trabajando" 
                 class="object-cover w-full h-full transition-transform duration-500 hover:scale-105">
        </div>
    </div>

    <!-- Valores y calidad -->
    <div class="bg-white border border-gray-100 rounded-2xl lg:rounded-3xl p-6 lg:p-12 shadow-xl mb-4">
        <h2 class="text-2xl lg:text-3xl font-bold text-center text-gray-900 mb-10 lg:mb-14">
            Nuestros Pilares Fundamentales
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="text-center px-4 py-6 lg:px-6 lg:py-8 hover:bg-gray-50 rounded-xl transition-colors">
                <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Calidad Garantizada</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    Productos frescos directamente del campo, seleccionados bajo los más altos estándares de calidad.
                </p>
            </div>

            <div class="text-center px-4 py-6 lg:px-6 lg:py-8 hover:bg-gray-50 rounded-xl transition-colors">
                <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Precios Justos</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    Equilibrio perfecto entre un precio justo para agricultores y accesible para consumidores.
                </p>
            </div>

            <div class="text-center px-4 py-6 lg:px-6 lg:py-8 hover:bg-gray-50 rounded-xl transition-colors">
                <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Impacto Social</h3>
                <p class="text-gray-600 text-base leading-relaxed">
                    Transformamos la economía local apoyando directamente a las comunidades agrícolas.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection