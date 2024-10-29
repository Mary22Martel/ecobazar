@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl">
    <h1 class="text-4xl font-bold text-green-600 mb-8 text-center">Pedidos Pendientes</h1>

    @if($pedidos->isEmpty())
    <p>No tienes pedidos pendientes en este momento.</p>
@else
        <div class="overflow-x-auto mt-8">
            <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg mx-auto">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left">
                        <th class="py-4 px-6">Número de Orden</th>
                        <th class="py-4 px-6">Cliente</th>
                        <th class="py-4 px-6">Productos</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pedidos as $pedido)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">{{ $pedido->id }}</td>
                            <td class="py-4 px-6">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                            <td class="py-4 px-6">
                                <ul>
                                    @foreach($pedido->items as $item)
                                        @if($item->product->user_id == Auth::id()) {{-- Solo mostrar productos del agricultor actual --}}
                                            <li>{{ $item->product->nombre }} - Cantidad: {{ $item->cantidad }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6">{{ $pedido->estado ?? 'Pendiente' }}</td>
                            <td class="py-4 px-6">
                                <a href="{{ route('agricultor.pedido.detalle', $pedido->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
