@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl px-4 grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Bienvenida -->
    <div class="lg:col-span-3 flex flex-col items-center mb-12">
        <div class="w-full bg-gradient-to-r from-blue-500 to-blue-500 text-white shadow-xl rounded-3xl p-10 flex items-center justify-between transform hover:scale-105 transition-all duration-500 ease-in-out">
            <div class="flex-1">
                <h2 class="text-5xl font-extrabold mb-4">Bienvenido, {{ Auth::user()->name ?? 'Repartidor' }}</h2>
                <p class="text-white leading-relaxed text-lg">Gracias por ser parte de nuestra comunidad de repartidores. Desde aquí puedes gestionar tus entregas, revisar tus rutas y explorar funcionalidades que hemos preparado para ayudarte en tu labor.</p>
            </div>
            <div class="hidden md:block w-1/3">
                <img src="{{ asset('images/rep.svg') }}" alt="Imagen de Bienvenida" class="w-full rounded-2xl shadow-2xl transform scale-110">
            </div>
        </div>
    </div>

    <!-- Tarjetas de Funcionalidades -->
    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Gestionar Entregas -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-lg rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Gestionar Entregas</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Aquí puedes ver y gestionar todas tus entregas de forma eficiente.</p>
            </div>
            <a href="{{ route('repartidor.pedidos_pendientes') }}" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out text-center transform hover:-translate-y-1">
                Ver Mis Entregas
            </a>
        </div>

        <!-- Revisar Rutas -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-lg rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Revisar Rutas</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Consulta las rutas asignadas y optimiza tu tiempo de entrega.</p>
            </div>
            <a href="{{ route('repartidor.pedidos_pendientes') }}" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-all duration-300 ease-in-out text-center transform hover:-translate-y-1">
                Ver Rutas
            </a>
        </div>

        <!-- Otras Funcionalidades -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-lg rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Otras Funcionalidades</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Explora otras funciones que se agregarán en el futuro para mejorar tu experiencia.</p>
            </div>
            <a href="{{ route('progreso') }}" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition-all duration-300 ease-in-out text-center transform hover:-translate-y-1">
                Ver Otras Funciones
            </a>

        </div>
    </div>
</div>
<br>
<br>
@endsection
