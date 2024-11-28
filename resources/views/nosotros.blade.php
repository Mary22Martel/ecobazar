@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl px-4">
    <!-- Encabezado -->
    <div class="text-center mb-12">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-green-600 mb-4">Nuestra Misión</h1>
        <p class="text-gray-600 text-lg sm:text-xl leading-relaxed">
            En nuestra plataforma, trabajamos para conectar agricultores de Huánuco con clientes conscientes, ofreciendo productos de alta calidad a precios justos. Queremos que conozcas la realidad de los agricultores y formes parte de este cambio positivo.
        </p>
    </div>

    <!-- Sección de objetivos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-gray-50 border border-gray-200 shadow-lg rounded-2xl p-6">
            <h2 class="text-2xl font-semibold text-green-600 mb-4">Apoyamos a los Agricultores</h2>
            <p class="text-gray-600 leading-relaxed">
                Nos enfocamos en mejorar las ventas de los agricultores locales, ayudándolos a llegar a más hogares con productos frescos y de calidad. Queremos que los clientes entiendan los retos y el esfuerzo detrás de cada cosecha.
            </p>
        </div>
        <div class="bg-gray-50 border border-gray-200 shadow-lg rounded-2xl p-6">
            <h2 class="text-2xl font-semibold text-green-600 mb-4">Conectar Mercados</h2>
            <p class="text-gray-600 leading-relaxed">
                Nuestra misión es asociar mercados de diferentes regiones para que los clientes puedan disfrutar de una mayor variedad de productos, facilitando la compra desde cualquier lugar y llevando lo mejor a sus hogares.
            </p>
        </div>
    </div>

    <!-- Imagen destacada -->
    <div class="text-center mb-12">
        <img src="{{ asset('images/M1.jpg') }}" alt="Agricultores y mercado" 
            class="w-full max-w-3xl mx-auto rounded-3xl shadow-xl">
    </div>

    <!-- Valores y calidad -->
    <div class="bg-gradient-to-r from-green-100 to-green-50 p-12 rounded-3xl shadow-lg">
        <h2 class="text-3xl font-extrabold text-center text-green-600 mb-8">Calidad, Precio Justo y Compromiso</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Productos de Calidad</h3>
                <p class="text-gray-600 leading-relaxed">
                    Cada producto proviene de agricultores comprometidos con ofrecer lo mejor, garantizando frescura y calidad en cada compra.
                </p>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Precios Justos</h3>
                <p class="text-gray-600 leading-relaxed">
                    Creemos que todos, tanto agricultores como consumidores, deben beneficiarse. Por eso, aseguramos precios justos para todos.
                </p>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Compromiso Social</h3>
                <p class="text-gray-600 leading-relaxed">
                    Nuestro objetivo es transformar la forma en que los productos llegan del campo a la mesa, creando un impacto positivo en las comunidades rurales.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
