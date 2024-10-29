<!-- resources/views/order/success.blade.php -->
@extends('layouts.app')

@section('content')
<div class="text-center py-10">
    <h1 class="text-4xl font-bold mb-6">¡Orden Exitosa!</h1>
    <p class="text-lg mb-4">Gracias por tu compra. Hemos recibido tu orden correctamente.</p>
    <a href="{{ route('tienda') }}" class="bg-green-500 text-white px-8 py-3 rounded-lg">Volver a la Tienda</a>
</div>
<!-- Detalles del Pedido -->
<div class="bg-white p-6 shadow-md rounded-lg inline-block text-left mt-8">
    <h3 class="text-2xl font-bold mb-4">Detalles del Pedido</h3>
    @foreach ($orden->items as $item)
    <div class="flex items-center justify-between mb-4">
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
        <span>S/{{ number_format($orden->subtotal, 2) }}</span>
    </div>
    <div class="flex justify-between mt-2">
        <span>Envío:</span>
        <span>S/{{ number_format($orden->envio, 2) }}</span>
    </div>
    <div class="flex justify-between mt-4 border-t pt-4 font-bold text-xl">
        <span>Total:</span>
        <span>S/{{ number_format($orden->total, 2) }}</span>
    </div>
</div>

<!-- Botón para descargar el voucher -->
<div class="mt-6">
    <a href="{{ route('order.voucher', $orden->id) }}" class="bg-blue-500 text-white px-8 py-3 rounded-lg">Descargar Voucher</a>
</div>

<a href="{{ route('tienda') }}" class="bg-green-500 text-white px-8 py-3 rounded-lg mt-4 inline-block">Volver a la Tienda</a>
@endsection
