@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl px-4 grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Bienvenida -->
    <div class="lg:col-span-3 flex flex-col items-center mb-12">
        <div class="w-full bg-gradient-to-r from-green-500 to-green-700 text-white shadow-xl rounded-3xl p-10 flex items-center justify-between transform hover:scale-105 transition-all">
            <div class="flex-1">
                <h2 class="text-5xl font-extrabold mb-4">Bienvenido, {{ Auth::user()->name ?? 'Agricultor' }}</h2>
                <p class="text-white leading-relaxed text-lg">Gracias por ser parte de nuestra comunidad. Desde aquí puedes gestionar todos tus productos, revisar los pedidos pendientes y explorar nuevas funcionalidades que hemos preparado para ti.</p>
            </div>
            <div class="hidden md:block w-1/3">
                <img src="{{ asset('images/agrix.svg') }}" alt="Imagen de Bienvenida" class="w-full rounded-2xl shadow-2xl transform scale-110">
            </div>
        </div>
    </div>

    <!-- Tarjetas de Funcionalidades -->
    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Gestionar Productos -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-2xl rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Gestionar Productos</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Aquí puedes ver y gestionar todos tus productos fácilmente.</p>
            </div>
            <a href="{{ route('productos.index') }}" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition-all duration-300 ease-in-out text-center transform hover:-translate-y-1">
                Ver Mis Productos
            </a>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-2xl rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Pedidos Pendientes</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Aquí puedes ver y gestionar todos los pedidos que tienes pendientes.</p>
            </div>
            <a href="{{ route('agricultor.pedidos_pendientes') }}" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition-all duration-300 ease-in-out text-center transform hover:-translate-y-1">
                Ver Pedidos Pendientes
            </a>
        </div>

        <!-- Otras Funcionalidades -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm hover:shadow-2xl rounded-lg p-6 transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 ease-in-out flex flex-col justify-between h-full">
            <div>
                <h4 class="text-xl font-semibold mb-2 text-gray-800">Otras Funcionalidades</h4>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">Aquí puedes explorar otras funciones que se agregaran en el futuro.</p>
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
