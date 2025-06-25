@extends('layouts.app')

@section('content')
<div class="container mx-auto py-16 px-4 lg:px-0">
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
        <h1 class="text-4xl font-bold text-red-600 mb-6">¡Oops! Algo salió mal</h1>
        <p class="text-lg text-gray-700 mb-8">
            Lo sentimos, tu pedido no pudo procesarse correctamente.
            Por favor, verifica tu información o intenta nuevamente.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('carrito.index') }}" class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                Volver al carrito
            </a>
            <a href="{{ route('home') }}" class="text-gray-600 hover:underline py-3">
                Volver al inicio
            </a>
        </div>
    </div>
</div>
@endsection
