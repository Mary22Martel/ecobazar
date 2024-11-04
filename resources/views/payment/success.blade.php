@extends('layouts.app2')

@section('content')
<div class="text-center py-10">
    <h1 class="text-4xl font-bold mb-6">¡Orden Exitosa!</h1>
    <p class="text-lg mb-4">Gracias por tu compra. Hemos recibido tu orden correctamente.</p>
    <a href="{{ route('tienda') }}" class="bg-green-500 text-white px-8 py-3 rounded-lg">Volver a la Tienda</a>
</div>

<a href="{{ route('tienda') }}" class="bg-green-500 text-white px-8 py-3 rounded-lg mt-4 inline-block">Volver a la Tienda</a>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verifica si estamos en la página de confirmación de éxito
        if (window.location.href.includes('/order/success')) {
            // Hacer una llamada AJAX para refrescar el carrito al cargar la página de éxito
            fetch('{{ route("carrito.getDetails") }}')
                .then(response => response.json())
                .then(data => {
                    // Actualizar la cantidad de productos en el carrito en la cabecera
                    document.getElementById('cart-total-items').innerText = data.totalItems;
                    document.getElementById('cart-total-price').innerText = data.totalPrice.toFixed(2);

                    // Vaciar el resumen del carrito en el modal del carrito
                    const cartItemsList = document.getElementById('cart-items-list');
                    if (cartItemsList) {
                        cartItemsList.innerHTML = ''; // Vacía el contenido del modal del carrito
                    }
                    document.getElementById('cart-popup-total-price').innerText = '0.00';
                })
                .catch(error => console.error('Error al actualizar el carrito:', error));
        }
    });
</script>

@endsection
