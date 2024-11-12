@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10 px-4 lg:px-0 bg-gray-50 rounded-lg shadow-md">
  

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario de Información de Compra -->
        <div class="col-span-2 bg-white p-8 shadow-lg rounded-lg ml-8">
        <h2 class="text-5xl font-bold text-center mb-5 text-green-600 ">Datos personales</h2>
        <p class="text-center mb-5">Solicitamos únicamente la información esencial para la finalización de la compra.</p>
       <h3 class="text-xl font-bold my-6 text-gray-700">Información de compra</h3>  
            <form action="{{ route('order.store') }}" method="POST" id="checkout-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="text" name="nombre" placeholder="Nombres" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" required>
                    <input type="text" name="apellido" placeholder="Apellidos" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" required>
                    <input type="text" name="empresa" placeholder="Nombre de empresa (opcional)" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300">
                    <input type="email" name="email" placeholder="Correo Electrónico" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" required>
                    <input type="text" name="telefono" placeholder="Teléfono" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" required>
                </div>

                <h3 class="text-xl font-bold my-6 text-gray-700">Opciones de Delivery</h3>
                <div class="flex flex-col space-y-4 mb-6">
                    <label class="flex items-center text-gray-600">
                        <input type="radio" name="delivery" value="puesto" class="mr-2 delivery-option focus:ring-green-500" required> Recoger en Puesto
                    </label>
                    <label class="flex items-center text-gray-600">
                        <input type="radio" name="delivery" value="delivery" class="mr-2 delivery-option focus:ring-green-500" required> Delivery
                    </label>
                </div>

                <div id="delivery-fields" class="space-y-4 mb-6 hidden">
                    <input type="text" name="direccion" placeholder="Nombre de la calle y número de casa" class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
                    <input type="text" name="direccion_opcional" placeholder="Dpto., piso, unidad, bloque (opcional)" class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
                    <select name="distrito" class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
    <option value="">Seleccione una zona</option>
    @foreach($zones as $zone)
        <option value="{{ $zone->id }}" data-cost="{{ $zone->delivery_cost }}">{{ $zone->name }} - S/{{ $zone->delivery_cost }}</option>
    @endforeach
</select>

                </div>

                <h3 class="text-xl font-bold my-6 text-gray-700">Opción de Pago</h3>
                <div class="flex flex-col space-y-4 mb-6">
                    <label class="flex items-center text-gray-600">
                        <input type="radio" name="pago" value="puesto" class="mr-2 focus:ring-green-500" required> Pagar en Puesto
                    </label>
                    <label class="flex items-center text-gray-600">
                        <input type="radio" name="pago" value="sistema" class="mr-2 focus:ring-green-500" required> Pagar en el Sistema
                    </label>
                </div>

                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">Realizar pedido</button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="bg-white p-8 shadow-lg rounded-lg mx-6">
            <h3 class="text-2xl font-bold mb-6 text-center text-green-700">Resumen de tu compra</h3>
            @foreach ($carrito->items as $item)
            <div class="flex items-center justify-between mb-4 carrito-item" data-id="{{ $item->product->id }}" data-title="{{ $item->product->nombre }}" data-price="{{ $item->product->precio }}" data-quantity="{{ $item->cantidad }}">
                <div class="flex items-center">
                    <div>
                        <p class="font-bold text-gray-800">{{ $item->product->nombre }} x{{ $item->cantidad }}</p>
                        <p class="text-gray-600">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="flex justify-between mt-4 border-t pt-4">
                <span class="text-gray-700">Subtotal:</span>
                <span id="subtotal" class="text-gray-800">S/{{ number_format($carrito->total(), 2) }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span class="text-gray-700">Envío:</span>
                <span id="envio" class="text-gray-800">S/0.00</span>
            </div>
            <div class="flex justify-between mt-4 border-t pt-4 font-bold text-xl">
                <span class="text-gray-800">Total:</span>
                <span id="total" class="text-green-500">S/{{ number_format($carrito->total(), 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryFields = document.getElementById('delivery-fields');
    const deliveryOptions = document.querySelectorAll('input[name="delivery"]');
    const zoneSelect = document.querySelector('select[name="distrito"]');
    const envioSpan = document.getElementById('envio');
    const totalSpan = document.getElementById('total');
    const subtotalSpan = document.getElementById('subtotal');
    const checkoutForm = document.getElementById('checkout-form');

    // Inicialmente ocultar los campos de dirección y quitar el "required" del campo "distrito"
    deliveryFields.style.display = 'none';
    zoneSelect.required = false;

    // Actualizar el costo de envío basado en la zona seleccionada
    zoneSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const envioCost = parseFloat(selectedOption.getAttribute('data-cost') || 0);
        const subtotal = parseFloat(subtotalSpan.innerText.replace('S/', ''));
        envioSpan.innerText = `S/${envioCost.toFixed(2)}`;
        totalSpan.innerText = `S/${(subtotal + envioCost).toFixed(2)}`;
    });

    // Manejar cambios en las opciones de entrega (mostrar u ocultar campos de dirección)
    deliveryOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value === 'delivery') {
                deliveryFields.style.display = 'block';
                deliveryFields.querySelectorAll('input').forEach(input => input.required = true);
                zoneSelect.required = true;
            } else {
                deliveryFields.style.display = 'none';
                deliveryFields.querySelectorAll('input').forEach(input => input.required = false);
                zoneSelect.required = false;
            }
        });
    });

    // Enviar el formulario con datos en formato JSON
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
        direccion: formData.get('delivery') === 'delivery' ? formData.get('direccion') : '', // Solo enviar si es delivery
        distrito: formData.get('delivery') === 'delivery' ? formData.get('distrito') : '',   // Solo enviar si es delivery
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Redirigir según el método de pago
            if (data.init_point) {
                // Redirigir a la URL de Mercado Pago
                window.location.href = data.init_point;
            } else if (data.redirect_url) {
                // Redirigir a la página de éxito
                window.location.href = data.redirect_url;
            } else {
                console.error("Error: No se proporcionó una URL de redirección.");
                alert("Hubo un problema al procesar tu orden. Inténtalo de nuevo.");
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
