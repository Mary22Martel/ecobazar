@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-6 sm:mt-12 px-4 sm:px-6 lg:px-8 max-w-7xl">
    <!-- Bienvenida -->
    <div class="mb-8 sm:mb-16">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-xl rounded-2xl sm:rounded-3xl p-6 sm:p-10 flex flex-col sm:flex-row items-center justify-between transition-transform duration-300 hover:shadow-2xl">
            <div class="flex-1 mb-6 sm:mb-0 sm:mr-8">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 leading-tight">
                    ¡Bienvenido, {{ Auth::user()->name ?? 'Repartidor' }}!
                </h2>
                <p class="text-sm sm:text-base md:text-lg opacity-90 leading-relaxed">
                    Gracias por ser parte de nuestra comunidad. Gestiona tus entregas, revisa rutas y descubre herramientas diseñadas para optimizar tu trabajo.
                </p>
            </div>
            <div class="w-full sm:w-1/3 mt-6 sm:mt-0">
                <img src="{{ asset('images/rep.svg') }}" alt="Repartidor" 
                     class="w-full h-48 sm:h-64 object-contain object-center transition-transform duration-500 hover:scale-105">
            </div>
        </div>
    </div>

    <!-- Tarjetas de Funcionalidades -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Tarjeta Entregas -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 h-full flex flex-col">
            <div class="mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Gestionar Entregas</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Visualiza y administra todas tus entregas pendientes de forma organizada y eficiente.</p>
            </div>
            <a href="{{ route('repartidor.pedidos_pendientes') }}" 
               class="mt-auto bg-blue-500 hover:bg-blue-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Ver Entregas</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- Tarjeta Rutas -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 h-full flex flex-col">
            <div class="mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Optimizar Rutas</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Consulta las rutas inteligentes diseñadas para maximizar tu eficiencia en entregas.</p>
            </div>
            <a href="{{ route('repartidor.pedidos_pendientes') }}" 
               class="mt-auto bg-purple-500 hover:bg-purple-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Ver Rutas</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </a>
        </div>

        <!-- Tarjeta Funcionalidades -->
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 h-full flex flex-col">
            <div class="mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Nuevas Funciones</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Descubre las próximas actualizaciones y características en desarrollo.</p>
            </div>
            <a href="{{ route('progreso') }}" 
               class="mt-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg text-sm font-medium transition-colors duration-300 flex items-center justify-center space-x-2">
                <span>Explorar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection