@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-6xl mx-auto py-8">
        <!-- Title with Cart Icon -->
        <h2 class="text-4xl sm:text-5xl font-bold mb-10 text-green-600 text-center flex items-center justify-center">
            🛒 Carrito de Compras
        </h2>

        @if($carrito && $carrito->items->count())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items Table -->
            <div class="col-span-2 bg-white p-6 shadow-lg rounded-lg overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="border-b">
                            <th class="py-4 text-left">Producto</th>
                            <th class="py-4 text-center">Precio</th>
                            <th class="py-4 text-center">Cantidad</th>
                            <th class="py-4 text-right">Subtotal</th>
                            <th class="py-4 text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carrito->items as $item)
                        <tr class="border-b" data-item-id="{{ $item->id }}">
                            <td class="py-6 flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $item->product->imagen) }}" alt="{{ $item->product->nombre }}" class="w-16 h-16 object-cover rounded">
                                <div>
                                    <p class="font-semibold">{{ $item->product->nombre }}</p>
                                </div>
                            </td>
                            <td class="py-6 text-center">S/{{ number_format($item->product->precio, 2) }}</td>
                            <td class="py-6 text-center">
                                <div class="flex items-center justify-center">
                                    <button type="button" class="text-gray-600 px-2 update-quantity" data-action="decrement">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" class="w-12 border rounded px-2 py-1 text-center item-quantity" readonly>
                                    <button type="button" class="text-gray-600 px-2 update-quantity" data-action="increment">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-6 text-right font-bold item-subtotal">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</td>
                            <td class="py-6 text-center">
                                <form action="{{ route('carrito.remove', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cart Summary -->
            <div class="bg-white p-6 shadow-lg rounded-lg">
                <h3 class="text-2xl font-bold mb-6 text-gray-700">Resumen de tu compra</h3>
                <div class="flex justify-between mb-4">
                    <span class="text-lg">Subtotal:</span>
                    <span class="text-lg font-bold" id="cart-subtotal">S/{{ number_format($carrito->total(), 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-4 mb-6">
                    <span class="text-xl font-bold">Total:</span>
                    <span class="text-xl font-bold text-green-500" id="cart-total">S/{{ number_format($carrito->total(), 2) }}</span>
                </div>
                <!-- Checkout Button -->
                <a href="{{ route('checkout') }}" class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-600 transition duration-200 text-center inline-block">Proceder a comprar</a>
                <!-- Return to Store Button -->
                <a href="{{ route('tienda') }}" class="block text-center mt-4 text-gray-500 hover:underline items-center justify-center">
                    <span class="mr-2">←</span> Ver más productos
                </a>
            </div>
        </div>
        @else
        <p class="text-center text-xl mt-20">No hay productos en tu carrito.</p>
        <div class="flex justify-center mt-8">
            <a href="{{ route('tienda') }}" class="bg-green-500 text-white px-8 py-3 rounded-lg flex items-center">
                <span class="mr-2">←</span> Volver a la tienda
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const updateButtons = document.querySelectorAll('.update-quantity');

    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const row = this.closest('tr');
            const quantityInput = row.querySelector('.item-quantity');
            let quantity = parseInt(quantityInput.value);
            const itemId = row.getAttribute('data-item-id');

            // Increment or decrement quantity
            if (action === 'increment') {
                quantity++;
            } else if (action === 'decrement' && quantity > 1) {
                quantity--;
            }

            // Update the input value
            quantityInput.value = quantity;

            // Make AJAX request to update the quantity in the backend
            fetch(`/carrito/actualizar/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cantidad: quantity })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    // Update subtotal for the item
                    row.querySelector('.item-subtotal').textContent = `S/${data.itemSubtotal.toFixed(2)}`;

                    // Update cart subtotal and total
                    document.getElementById('cart-subtotal').textContent = `S/${data.cartSubtotal.toFixed(2)}`;
                    document.getElementById('cart-total').textContent = `S/${data.cartTotal.toFixed(2)}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al actualizar el carrito. Intenta nuevamente.');
            });
        });
    });
});

</script>
@endsection
