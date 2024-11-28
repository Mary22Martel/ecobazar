@extends('layouts.app2')

@section('content')
<div class="container mx-auto p-4 lg:p-6">
    <h1 class="text-5xl lg:text-5xl font-bold mb-6 text-center text-green-500">Pedidos Pendientes</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-400 text-green-500 p-4 rounded-md mb-6 text-center shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-400 text-red-800 p-4 rounded-md mb-6 text-center shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($pedidos->isEmpty())
        <p class="text-center text-lg text-gray-600">No tienes pedidos pendientes en este momento.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wide">
                        <th class="py-3 px-4 text-left">ID del Pedido</th>
                        <th class="py-3 px-4 text-left">Cliente</th>
                        <th class="py-3 px-4 text-left">Dirección</th>
                        <th class="py-3 px-4 text-left">Zona</th>
                        <th class="py-3 px-4 text-left">Total</th>
                        <th class="py-3 px-4 text-left">Ver Detalles</th>
                        <th class="py-3 px-4 text-left">Acciones</th>
                        <th class="py-3 px-4 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $pedido)
                        <tr class="border-b hover:bg-green-50">
                            <!-- Información del Pedido -->
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->id }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->direccion ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $pedido->distrito ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">S/{{ number_format($pedido->total, 2) }}</td>
                            
                            <!-- Ver Detalles -->
                            <td class="py-3 px-4 text-sm">
                                <a href="{{ route('repartidor.pedido.detalle', $pedido->id) }}" 
                                   class="text-green-700 underline hover:text-green-900 transition">
                                    Ver Detalles
                                </a>
                            </td>

                            <!-- Acciones -->
                            <td class="py-2 px-3 text-sm">
                                <div class="flex flex-col space-y-2">
                                    @if(trim(strtolower($pedido->estado)) === 'pagado')
                                        <form action="{{ route('repartidor.pedido.proceso', $pedido->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-yellow-100 text-yellow-700 py-2 px-4 rounded-md hover:bg-yellow-200 transition shadow">
                                                En Proceso
                                            </button>
                                        </form>
                                        <button class="bg-gray-100 text-gray-400 py-2 px-4 rounded-md cursor-not-allowed shadow" disabled>
                                            Entregado
                                        </button>
                                    @elseif(trim(strtolower($pedido->estado)) === 'en proceso')
                                        <button class="bg-yellow-100 text-yellow-700 py-2 px-4 rounded-md cursor-not-allowed shadow">
                                            En Proceso
                                        </button>
                                        <form action="{{ route('repartidor.pedido.entregado', $pedido->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-100 text-green-700 py-2 px-4 rounded-md hover:bg-green-200 transition shadow">
                                                Entregado
                                            </button>
                                        </form>
                                    @elseif(trim(strtolower($pedido->estado)) === 'entregado')
                                        <button class="bg-gray-100 text-gray-400 py-2 px-4 rounded-md cursor-not-allowed shadow">
                                            En Proceso
                                        </button>
                                        <button class="bg-gray-100 text-gray-400 py-2 px-4 rounded-md cursor-not-allowed shadow">
                                            Entregado
                                        </button>
                                    @elseif(trim(strtolower($pedido->estado)) === 'cancelado')
                                        <button class="bg-red-100 text-red-700 py-2 px-4 rounded-md cursor-not-allowed shadow">
                                            Pedido Cancelado
                                        </button>
                                    @else
                                        <span class="text-red-700 italic">Estado desconocido</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Estado -->
                            <td class="py-3 px-4 text-sm">
                                @if(trim(strtolower($pedido->estado)) === 'pagado')
                                    <span class="inline-block bg-gray-200 text-gray-600 px-3 py-1 rounded-md font-semibold shadow">
                                        Pendiente
                                    </span>
                                @elseif(trim(strtolower($pedido->estado)) === 'en proceso')
                                    <span class="inline-block bg-yellow-200 text-yellow-700 px-3 py-1 rounded-md font-semibold shadow">
                                        En Proceso
                                    </span>
                                @elseif(trim(strtolower($pedido->estado)) === 'entregado')
                                    <span class="inline-block bg-green-200 text-green-700 px-3 py-1 rounded-md font-semibold shadow">
                                        Entregado
                                    </span>
                                @elseif(trim(strtolower($pedido->estado)) === 'cancelado')
                                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-md font-semibold shadow">
                                        Cancelado
                                    </span>
                                @else
                                    <span class="inline-block bg-red-200 text-red-700 px-3 py-1 rounded-md font-semibold shadow">
                                        Desconocido
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
