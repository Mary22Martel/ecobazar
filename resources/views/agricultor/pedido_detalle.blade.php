@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-4xl">
    <h1 class="text-4xl font-bold text-green-600 mb-8 text-center">Detalle del Pedido #{{ $pedido->id }}</h1>

    <div class="bg-white p-8 shadow-md rounded-lg">
        <h2 class="text-xl font-bold mb-4">Información del Cliente</h2>
        <p><strong>Nombre:</strong> {{ $pedido->nombre }} {{ $pedido->apellido }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->telefono }}</p>
        <p><strong>Email:</strong> {{ $pedido->email }}</p>
        <p><strong>Tipo de Delivery:</strong> {{ $pedido->delivery }}</p>
        <p><strong>Dirección:</strong> {{ $pedido->direccion }}</p>

        <h2 class="text-xl font-bold mt-8 mb-4">Productos del Pedido</h2>
        <ul>
            @foreach($productosAgricultor as $item)
                <li>{{ $item->product->nombre }} - Cantidad: {{ $item->cantidad }}</li>
            @endforeach
        </ul>

        <div class="flex justify-end mt-8">
        <form action="{{ route('agricultor.confirmar_pedido_listo', $pedido->id) }}" method="POST">
    @csrf
    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
        Confirmar Pedido Listo
    </button>
</form>

        </div>
    </div>
</div>
@endsection
