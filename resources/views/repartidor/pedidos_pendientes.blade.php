@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold mb-6 text-center text-green-600">Pedidos Pendientes</h1>

    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-6 text-center">
            {{ session('success') }}
        </div>
    @endif

    @if($pedidos->isEmpty())
        <p class="text-center text-lg text-gray-600">No tienes pedidos pendientes en este momento.</p>
    @else
        <table class="min-w-full bg-white border border-gray-200 shadow rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-4 px-6 text-left">ID del Pedido</th>
                    <th class="py-4 px-6 text-left">Cliente</th>
                    <th class="py-4 px-6 text-left">Dirección</th>
                    <th class="py-4 px-6 text-left">Total</th>
                    <th class="py-4 px-6 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $pedido)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $pedido->id }}</td>
                        <td class="py-4 px-6">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                        <td class="py-4 px-6">{{ $pedido->direccion ?? 'N/A' }}</td>
                        <td class="py-4 px-6">S/{{ number_format($pedido->total, 2) }}</td>
                        <td class="py-4 px-6">
                            @if($pedido->estado !== 'entregado')
                                <form action="{{ route('repartidor.pedido.entregado', $pedido->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-all">
                                        Marcar como Entregado
                                    </button>
                                </form>
                            @else
                                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                                    Pedido Entregado
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
