@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-7xl">
    <h1 class="text-6xl font-extrabold text-center text-green-500 mb-12">Dashboard del Agricultor</h1>

    <div class="flex justify-center">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl">
    <div class="bg-white shadow-md hover:shadow-lg transition-shadow rounded-lg p-8">
            <h4 class="text-2xl font-bold mb-4 text-gray-700">Gestionar Productos</h4>
            <p class="text-gray-600 mb-6">Aquí puedes ver y gestionar todos tus productos fácilmente.</p>
            <div class="flex flex-col space-y-4">
                <a href="{{ route('productos.index') }}" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out text-center">
                    Ver Mis Productos
                </a>
                <a href="{{ route('productos.create') }}" class="bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition duration-300 ease-in-out text-center">
                    Agregar Nuevo Producto
                </a>
            </div>
        </div>

        <!-- Aquí puedes agregar más tarjetas en el futuro -->
        <!-- Ejemplo de otra tarjeta para otras funcionalidades -->
        <div class="bg-white shadow-md hover:shadow-lg transition-shadow rounded-lg p-8">
            <h4 class="text-2xl font-bold mb-4 text-gray-700">Otras Funcionalidades</h4>
            <p class="text-gray-600 mb-6">Aquí puedes explorar otras funciones que se agregaran en el futuro.</p>
            <a href="#" class="bg-yellow-500 text-white py-3 px-6 rounded-lg hover:bg-yellow-600 transition duration-300 ease-in-out text-center">
                Ver Otras Funciones
            </a>
        </div>

         <!-- Nueva tarjeta para gestionar pedidos -->
         <div class="bg-white shadow-md hover:shadow-lg transition-shadow rounded-lg p-8">
                <h4 class="text-2xl font-bold mb-4 text-gray-700">Pedidos Pendientes</h4>
                <p class="text-gray-600 mb-6">Aquí puedes ver y gestionar todos los pedidos que tienes pendientes.</p>
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('agricultor.pedidos_pendientes') }}" class="bg-purple-500 text-white py-3 px-6 rounded-lg hover:bg-purple-600 transition duration-300 ease-in-out text-center">
                        Ver Pedidos Pendientes
                    </a>
                </div>
            </div>

    </div>
</div>
</div>
<br>
<br>
@endsection
