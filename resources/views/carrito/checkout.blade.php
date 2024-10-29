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
        const paymentOptions = document.querySelectorAll('input[name="pago"]');
        const checkoutForm = document.getElementById('checkout-form');

        let selectedPaymentOption = null;

        paymentOptions.forEach(option => {
            option.addEventListener('change', function() {
                selectedPaymentOption = this.value;
            });
        });

        checkoutForm.addEventListener('submit', function(event) {
            if (selectedPaymentOption === 'sistema') {
                event.preventDefault(); // Evitar el envío del formulario por defecto

                // Obtener datos del carrito desde los elementos HTML con clase "carrito-item"
                const carritoItems = document.querySelectorAll('.carrito-item');
                const products = [];

                carritoItems.forEach(item => {
                    products.push({
                        id: item.dataset.id,
                        title: item.dataset.title,
                        description: 'Producto del carrito',
                        currency_id: 'PEN',
                        quantity: parseInt(item.dataset.quantity),
                        unit_price: parseFloat(item.dataset.price)
                    });
                });

                // Datos del comprador y del pedido
                const formData = new FormData(checkoutForm);
                const orderData = {
                    product: products,
                    name: formData.get('nombre'),
                    surname: formData.get('apellido'),
                    email: formData.get('email'),
                    phone: formData.get('telefono'),
                    total: parseFloat("{{ $carrito->total() + 8.00 }}")
                };

                fetch("{{ url('/create-preference') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => response.json())
                .then(preference => {
                    if (preference.error) {
                        throw new Error(preference.error);
                    }
                    // Redirigir al pago de Mercado Pago
                    window.location.href = preference.init_point;
                })
                .catch(error => {
                    console.error('Error al crear la preferencia:', error);
                });
            }
        });
    });
</script>
@endsection
