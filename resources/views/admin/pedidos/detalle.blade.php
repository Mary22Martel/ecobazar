@extends('layouts.app2')

@section('content')
<div class="space-y-4">
    <h3 class="text-2xl font-semibold text-green-600">Detalles del Pedido #{{ $pedido->id }}</h3>

    <!-- Información del Cliente -->
    <div class="mt-4">
        <h4 class="text-lg font-semibold">Información del Cliente</h4>
        <p><strong>Nombre:</strong> {{ $pedido->user->name }}</p>  <!-- Mostramos el nombre del cliente -->
        <p><strong>Email:</strong> {{ $pedido->user->email }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->telefono }}</p>
        <p><strong>Dirección:</strong> {{ $pedido->direccion }}</p>
        <p><strong>Distrito:</strong> {{ $pedido->distrito }}</p>
    </div>

    <!-- Repartidor Asignado -->
    @if ($pedido->repartidor)
        <div class="mt-4">
            <h4 class="text-lg font-semibold">Repartidor Asignado</h4>
            <p><strong>Nombre:</strong> {{ $pedido->repartidor->name }}</p>
            <p><strong>Email:</strong> {{ $pedido->repartidor->email }}</p>
        </div>
    @else
        <div class="mt-4">
            <h4 class="text-lg font-semibold">Repartidor Asignado</h4>
            <p>No hay repartidor asignado aún.</p>
        </div>
    @endif

    <!-- Productos del Pedido -->
    <div class="mt-4">
        <h4 class="text-lg font-semibold">Productos del Pedido</h4>
        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-left">
                    <th class="py-2 px-4">Producto</th>
                    <th class="py-2 px-4">Cantidad</th>
                    <th class="py-2 px-4">Precio</th>
                    <th class="py-2 px-4">Agricultor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pedido->items as $item)
                    <tr>
                        <td class="py-2 px-4">{{ $item->product->nombre }}</td>
                        <td class="py-2 px-4">{{ $item->cantidad }}</td>
                        <td class="py-2 px-4">S/{{ number_format($item->precio, 2) }}</td>
                        <td class="py-2 px-4">
                            @if ($item->product->usuario)
                                {{ $item->product->usuario->name }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Estado del Pedido -->
    <div class="mt-4">
        <h4 class="text-lg font-semibold">Estado del Pedido: {{ ucfirst($pedido->estado) }}</h4>
    </div>

    <!-- Botón para cerrar el modal -->
    <div class="mt-4 text-right">
        <button wire:click="$emit('closeModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Cerrar</button>
    </div>
</div>
@endsection
