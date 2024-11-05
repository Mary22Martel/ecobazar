<!-- resources/views/order/success.blade.php -->
@extends('layouts.app2')
@section('content')
<div class="flex flex-col items-center min-h-screen bg-gray-100 px-4 py-10">
    <!-- Success Message -->
    <div class="bg-white p-8 shadow-lg rounded-lg text-center max-w-lg w-full">
        <h1 class="text-4xl font-bold mb-6 text-green-600">¡Orden Exitosa!</h1>
        <p class="text-lg mb-3">Gracias por tu compra. Hemos recibido tu orden correctamente.</p>
    </div>

    <!-- Order Details -->
    <div class="bg-white p-6 shadow-lg rounded-lg mt-8 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4">Detalles del Pedido</h3>
        @foreach ($orden->items as $item)
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="font-bold">{{ $item->product->nombre }} x{{ $item->cantidad }}</p>
                <p class="text-gray-600">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</p>
            </div>
        </div>
        @endforeach
        <div class="flex justify-between mt-4 border-t pt-4">
            <span class="text-lg">Subtotal:</span>
            <span class="text-lg">S/{{ number_format($orden->subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between mt-2">
            <span class="text-lg">Envío:</span>
            <span class="text-lg">S/{{ number_format($orden->envio, 2) }}</span>
        </div>
        <div class="flex justify-between mt-4 border-t pt-4 font-bold text-xl">
            <span>Total:</span>
            <span>S/{{ number_format($orden->total, 2) }}</span>
        </div>
    </div>

    <!-- Voucher Download and Back Button -->
    <div class="flex flex-col sm:flex-row sm:space-x-4 mt-6 w-full max-w-md">
        <a href="{{ route('order.voucher', $orden->id) }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold text-center mb-4 sm:mb-0 hover:bg-blue-600 transition duration-200 w-full">Descargar Voucher</a>
        <a href="{{ route('tienda') }}" class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-600 transition duration-200 w-full">Volver a la Tienda</a>
    </div>
</div>
@endsection
