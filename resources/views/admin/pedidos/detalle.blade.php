@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl">
    <h1 class="text-4xl font-bold text-green-600 mb-8 text-center">Detalle del Pedido #{{ $pedido->id }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Información del Cliente</h2>
        <p><strong>Nombre:</strong> {{ $pedido->nombre }} {{ $pedido->apellido }}</p>
        <p><strong>Email:</strong> {{ $pedido->email }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->telefono }}</p>
        <p><strong>Dirección:</strong> {{ $pedido->direccion }}</p>
        <p><strong>Distrito:</strong> {{ $pedido->distrito }}</p>

        @if ($pedido->repartidor)
            <h2 class="text-2xl font-bold mt-6 mb-4">Repartidor Asignado</h2>
            <p><strong>Nombre:</strong> {{ $pedido->repartidor->name }}</p>
            <p><strong>Email:</strong> {{ $pedido->repartidor->email }}</p>
        @else
            <h2 class="text-2xl font-bold mt-6 mb-4">Repartidor Asignado</h2>
            <p>No hay repartidor asignado aún.</p>
        @endif

        <h2 class="text-2xl font-bold mt-6 mb-4">Productos del Pedido</h2>
        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg mx-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-left">
                    <th class="py-4 px-6">Producto</th>
                    <th class="py-4 px-6">Cantidad</th>
                    <th class="py-4 px-6">Precio</th>
                    <th class="py-4 px-6">Agricultor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($pedido->items as $item)
                <tr>
                    <td class="py-4 px-6">{{ $item->product->nombre }}</td>
                    <td class="py-4 px-6">{{ $item->cantidad }}</td>
                    <td class="py-4 px-6">S/{{ number_format($item->precio, 2) }}</td>
                    <td class="py-4 px-6">
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

        <h2 class="text-2xl font-bold mt-6 mb-4">Estado del Pedido: {{ ucfirst($pedido->estado) }}</h2>

        <!-- Aquí añadimos el formulario para actualizar el estado del pedido -->
        <form action="{{ route('admin.pedido.actualizar_estado', $pedido->id) }}" method="POST" class="mt-6">
            @csrf
            <div class="flex items-center space-x-4">
                <select name="estado" class="border rounded-lg p-2">
                    <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="listo" {{ $pedido->estado == 'listo' ? 'selected' : '' }}>Listo</option>
                    <option value="enviando" {{ $pedido->estado == 'enviando' ? 'selected' : '' }}>Enviando</option>
                    <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                </select>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Actualizar Estado</button>
            </div>
        </form>
        <!-- Fin del formulario para actualizar el estado del pedido -->
    </div>
</div>
@endsection
