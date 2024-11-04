@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-3xl font-bold mb-6">Información de Compra</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario de Información de Compra -->
        <div class="col-span-2 bg-white p-6 shadow-md rounded-lg">
            <form action="{{ route('order.store') }}" method="POST" id="checkout-form">
                @csrf
                <!-- Información del Cliente -->
                <h3 class="text-xl font-bold mb-4">Información de Compra</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="nombre" placeholder="Nombres" class="border rounded px-4 py-2" required>
                    <input type="text" name="apellido" placeholder="Apellidos" class="border rounded px-4 py-2" required>
                    <input type="text" name="empresa" placeholder="Nombre de empresa (opcional)" class="border rounded px-4 py-2">
                    <input type="email" name="email" placeholder="Correo Electrónico" class="border rounded px-4 py-2" required>
                    <input type="text" name="telefono" placeholder="Teléfono" class="border rounded px-4 py-2" required>
                </div>

                <!-- Opciones de Delivery -->
                <h3 class="text-xl font-bold my-4">Opciones de Delivery</h3>
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="delivery" value="puesto" class="mr-2" required> Recoger en Puesto
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="delivery" value="delivery" class="mr-2" required> Delivery
                    </label>
                </div>
                <div id="delivery-fields">
                    <input type="text" name="direccion" placeholder="Nombre de la calle y número de casa" class="border rounded w-full px-4 py-2 mb-2" required>
                    <input type="text" name="direccion_opcional" placeholder="Dpto., piso, unidad, bloque (opcional)" class="border rounded w-full px-4 py-2 mb-2">
                    <input type="text" name="distrito" placeholder="Distrito" class="border rounded w-full px-4 py-2 mb-2" required>
                </div>

                <!-- Opción de Pago -->
                <h3 class="text-xl font-bold my-4">Opción de Pago</h3>
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="pago" value="puesto" class="mr-2" required> Pagar en Puesto
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="pago" value="sistema" class="mr-2" required> Pagar en el Sistema
                    </label>
                </div>

                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-600 transition duration-200">Realizar pedido</button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="bg-white p-6 shadow-md rounded-lg">
            <h3 class="text-2xl font-bold mb-4">Resumen de tu compra</h3>
            @foreach ($carrito->items as $item)
            <div class="flex items-center justify-between mb-4 carrito-item" data-id="{{ $item->product->id }}" data-title="{{ $item->product->nombre }}" data-price="{{ $item->product->precio }}" data-quantity="{{ $item->cantidad }}">
                <div class="flex items-center">
                    <div>
                        <p class="font-bold">{{ $item->product->nombre }} x{{ $item->cantidad }}</p>
                        <p class="text-gray-600">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="flex justify-between mt-4 border-t pt-4">
                <span>Subtotal:</span>
                <span>S/{{ number_format($carrito->total(), 2) }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span>Envío:</span>
                <span>S/8.00</span>
            </div>
            <div class="flex justify-between mt-4 border-t pt-4 font-bold text-xl">
                <span>Total:</span>
                <span>S/{{ number_format($carrito->total() + 8.00, 2) }}</span>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');

    checkoutForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Evita el envío estándar del formulario

        // Obtener datos del formulario
        const formData = new FormData(checkoutForm);
        const orderData = {
            nombre: formData.get('nombre'),
            apellido: formData.get('apellido'),
            empresa: formData.get('empresa'),
            email: formData.get('email'),
            telefono: formData.get('telefono'),
            delivery: formData.get('delivery'),
            direccion: formData.get('direccion'),
            distrito: formData.get('distrito'),
            pago: formData.get('pago')
        };

        // Obtener los datos del carrito desde los elementos HTML
        const carritoItems = document.querySelectorAll('.carrito-item');
        orderData.carrito = []; // Crear un array para los items

        carritoItems.forEach(item => {
            orderData.carrito.push({
                id: item.dataset.id,
                title: item.dataset.title,
                quantity: parseInt(item.dataset.quantity),
                unit_price: parseFloat(item.dataset.price)
            });
        });

        // Hacer una petición POST a OrderController@store
        fetch("{{ route('order.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Si data contiene init_point, significa que el pago es "sistema" y tenemos una URL de pago
            if (data.init_point) {
    // Redirigir a la URL de Mercado Pago
    window.location.href = data.init_point;
} else {
    // Si no es un pago por sistema, redirigir a la página de éxito
    window.location.href = data.redirect_url;
}

        })
        .catch(error => {
            console.error('Error al crear la orden:', error);
            alert('Hubo un problema al procesar tu orden. Inténtalo de nuevo.');
        });
    });
});

</script>
@endsection
