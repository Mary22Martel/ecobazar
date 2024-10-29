@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Columnas izquierda para la imagen principal y miniaturas -->
        <div class="md:col-span-5">
            <!-- Imagen principal del producto -->
            <div class="mb-4 ml-4">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-96 object-cover rounded-lg">
            </div>

            <!-- Galería de miniaturas (si es que tienes más imágenes) -->
            <div class="grid grid-cols-4 gap-2 ml-4">
                <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-20 object-cover rounded-lg" alt="{{ $producto->nombre }}">
                <!-- Añadir más imágenes aquí si es necesario -->
            </div>
        </div>

        <!-- Columna derecha para los detalles del producto -->
        <div class="md:col-span-7 space-y-4">
            <!-- Título del producto -->
            <h2 class="text-3xl font-bold">{{ $producto->nombre }}</h2>

            <!-- Precio y oferta (si existe un descuento) -->
            <div class="flex items-center space-x-4">
                <p class="text-3xl text-green-600 font-semibold">
                    S/{{ number_format($producto->precio, 2) }}
                </p>
                @if($producto->descuento)
                    <span class="text-red-500 bg-red-100 px-2 py-1 rounded-lg text-sm font-bold">{{ $producto->descuento }}% OFF</span>
                    <p class="text-gray-500 line-through">S/{{ number_format($producto->precio * (1 + $producto->descuento / 100), 2) }}</p>
                @endif
            </div>

            <!-- Descripción del producto -->
            <p class="text-gray-600">{{ $producto->descripcion }}</p>

            <!-- Opciones de cantidad disponibles -->
            <div class="flex items-center space-x-4">
                <p class="font-semibold">Disponibles: {{ $producto->cantidad_disponible }}</p>
                
                <!-- Selector de cantidad -->
                <div class="flex items-center">
                    <button id="decrease" class="bg-gray-300 text-gray-700 px-2 py-1 rounded-l-lg">-</button>
                    <input id="quantity" name="cantidad" type="number" value="1" min="1" max="{{ $producto->cantidad_disponible }}" class="text-center w-12 border-t border-b px-2 py-1">
                    <button id="increase" class="bg-gray-300 text-gray-700 px-2 py-1 rounded-r-lg">+</button>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex space-x-4 mt-6">
                <a href="{{ route('tienda') }}" class="inline-block bg-transparent text-green-600 border border-green-600 px-4 py-2 rounded-lg hover:bg-green-100">
                    Volver a la tienda
                </a>

                <!-- Formulario para agregar al carrito -->
                <form class="add-to-cart-form mt-2" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                @csrf
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg w-full hover:bg-green-600">
                    Agregar al carrito
                </button>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const decreaseButton = document.getElementById('decrease');
        const increaseButton = document.getElementById('increase');
        const quantityInput = document.getElementById('quantity');
        const cantidadInput = document.getElementById('cantidadInput'); // input oculto para el formulario

        // Convertir el valor máximo del input en un número a través de JavaScript
        const maxQuantity = parseInt("{{ $producto->cantidad_disponible }}");

        // Evento para disminuir la cantidad
        decreaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                cantidadInput.value = quantityInput.value; // actualizar input oculto
            }
        });

        // Evento para aumentar la cantidad
        increaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
                cantidadInput.value = quantityInput.value; // actualizar input oculto
            }
        });

        // Asegurar que la cantidad en el input oculto esté sincronizada
        quantityInput.addEventListener('input', function() {
            cantidadInput.value = quantityInput.value;
        });
    });
    function updateProductStock(productId) {
    $.ajax({
        url: '/producto/' + productId, // Ruta para obtener los datos del producto
        method: 'GET',
        success: function(response) {
            // Actualizar la cantidad disponible en la vista
            $('#producto-' + productId + ' .cantidad-disponible').text('Disponibles: ' + response.cantidad_disponible);
        }
    });
}

</script>
@endsection
