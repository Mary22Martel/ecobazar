@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-6 sm:mt-12 px-4 sm:px-6 lg:px-8 max-w-7xl">
    <!-- Sección de Bienvenida -->
    <div class="mb-8 sm:mb-16">
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg rounded-2xl sm:rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row items-center justify-between transition-all duration-300 hover:shadow-xl">
            <div class="flex-1 mb-8 lg:mb-0 lg:mr-10">
                <h1 class="text-3xl sm:text-4xl xl:text-5xl font-bold mb-4 leading-tight">
                    ¡Bienvenido, {{ Auth::user()->name ?? 'Agricultor' }}!
                </h1>
                <p class="text-sm sm:text-base lg:text-lg opacity-95 leading-relaxed">
                    Gracias por ser parte de nuestra comunidad. Gestiona tus productos, revisa pedidos pendientes y descubre herramientas diseñadas para potenciar tu trabajo agrícola.
                </p>
            </div>
            <div class="w-full lg:w-1/3 mt-8 lg:mt-0">
                <img src="{{ asset('images/agrix.svg') }}" alt="Agricultor" 
                     class="w-full h-48 sm:h-56 object-contain object-center transition-transform duration-500 hover:scale-105">
            </div>
        </div>
    </div>

    <!-- Tarjetas de Funcionalidades -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
        <!-- Tarjeta Productos -->
        <div class="bg-white border border-green-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col group">
            <div class="mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2M14 4h2a2 2 0 12-4 0h2m0-2v2"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Gestionar Productos</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Administra y actualiza tu catálogo de productos agrícolas de forma sencilla y organizada.</p>
            </div>
            <a href="{{ route('productos.index') }}" 
               class="mt-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Administrar Productos</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- Tarjeta Pedidos -->
        <div class="bg-white border border-green-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col group">
            <div class="mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2m-4 0v2m0 0h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Pedidos Pendientes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Revisa y gestiona todos los pedidos actuales de tus productos.</p>
            </div>
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="mt-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Ver Pedidos</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </a>
        </div>

        <!-- Tarjeta Nuevas Funciones -->
        <div class="bg-white border border-green-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col group">
            <div class="mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Nuevas Funciones</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Descubre las próximas actualizaciones y mejoras en desarrollo.</p>
            </div>
            <a href="{{ route('progreso') }}" 
               class="mt-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Explorar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </a>
        </div>

        <!-- Tarjeta Pagos al Productor -->
        <div class="bg-white border border-green-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col group">
            <div class="mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10v6m0 4v2m-1-1h2"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Pagos al Productor</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Visualiza el monto a pagar por las ventas de tus productos.</p>
            </div>
            <a href="{{ route('agricultor.pagos') }}" 
               class="mt-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Ver Pagos</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
