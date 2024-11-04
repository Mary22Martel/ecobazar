@extends('layouts.app2')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold text-center mb-8">Dashboard del Repartidor</h1>

    <!-- Tarjeta para acceder a los pedidos pendientes -->
    <div class="flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card para Pedidos Pendientes -->
            <div class="bg-white shadow-md hover:shadow-lg rounded-lg p-6 text-center transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Pedidos Pendientes</h2>
                <p class="text-gray-600 mb-6">Ver los pedidos que están listos para ser entregados.</p>
                <a href="{{ route('repartidor.pedidos_pendientes') }}" class="block bg-green-500 text-white font-semibold px-5 py-3 rounded-lg hover:bg-green-600 transition-colors duration-300">
                    Ver Pedidos Pendientes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
