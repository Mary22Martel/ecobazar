@extends('layouts.app2')

@section('content')
<div class="container mx-auto my-10 px-4">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Asignar Repartidor a Pedido</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($pedidos as $pedido)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6">
                    <h5 class="text-2xl font-semibold text-gray-800 mb-2">Pedido #{{ $pedido->id }}</h5>
                    <p class="text-gray-600"><strong>Cliente:</strong> {{ $pedido->nombre }} {{ $pedido->apellido }}</p>
                    <p class="text-gray-600"><strong>Dirección:</strong> {{ $pedido->direccion }}</p>
                    <p class="text-gray-600 mb-4"><strong>Distrito:</strong> {{ $pedido->distrito }}</p>

                    <form action="{{ route('admin.repartidor.asignar_repartidor', ['id' => $pedido->id]) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="repartidor_{{ $pedido->id }}" class="block text-sm font-medium text-gray-700 mb-1">Asignar Repartidor:</label>
                            <select name="repartidor_id" id="repartidor_{{ $pedido->id }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Seleccione un repartidor</option>
                                @foreach ($repartidores as $repartidor)
                                    <option value="{{ $repartidor->id }}">{{ $repartidor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-300">
                            Asignar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
