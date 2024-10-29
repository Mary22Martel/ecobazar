@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl">
    <h1 class="text-4xl font-bold text-green-600 mb-8 text-center">Pedidos Listos</h1>
    @if($pedidos->isEmpty())
        <p>No tienes pedidos listos en este momento.</p>
    @else
        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg mx-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-left">
                    <th class="py-4 px-6">ID del Pedido</th>
                    <th class="py-4 px-6">Productos</th>
                    <th class="py-4 px-6">Cliente</th>
                    <th class="py-4 px-6">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pedidos as $pedido)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $pedido->id }}</td>
                        <td class="py-4 px-6">
                            @foreach($pedido->items as $item)
                                <div>{{ $item->product->nombre }} - {{ $item->cantidad }}</div>
                            @endforeach
                        </td>
                        <td class="py-4 px-6">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                        <td class="py-4 px-6">{{ ucfirst($pedido->estado) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
