@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-8 max-w-4xl px-4">
    <!-- Navegación superior -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mb-6 shadow">
        <div class="flex justify-between items-center">
            <a href="{{ route('agricultor.dashboard') }}" 
               class="flex items-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                ← Volver al inicio
            </a>
            <div class="flex space-x-2">
                <span class="bg-orange-500 text-white px-3 py-2 rounded text-sm font-bold">
                    📦 PENDIENTES
                </span>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-green-500 hover:text-white transition-colors">
                    ✅ LISTOS
                </a>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-blue-500 hover:text-white transition-colors">
                    💰 PAGOS
                </a>
            </div>
        </div>
    </div>

    <!-- Título simple y claro -->
    <div class="bg-orange-500 text-white p-6 rounded-lg mb-6 text-center">
        <h1 class="text-2xl font-bold">📦 MIS PEDIDOS POR ARMAR</h1>
        <p class="text-lg mt-2">{{ $pedidos->whereIn('estado', ['pendiente', 'pagado'])->count() }} pedidos esperando</p>
    </div>

    @if($pedidos->isEmpty())
        <div class="bg-green-50 border-2 border-green-200 p-8 rounded-lg text-center">
            <h2 class="text-xl font-bold text-green-800">🎉 ¡No tienes pedidos pendientes!</h2>
            <p class="text-green-700 mt-2">Descansa o revisa si hay nuevos pedidos más tarde</p>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="inline-block mt-4 bg-green-500 text-white px-6 py-3 rounded-lg text-lg font-bold hover:bg-green-600">
                🏠 Volver al inicio
            </a>
        </div>
    @else
        <!-- Lista simple de pedidos -->
        @foreach($pedidos as $pedido)
            @php
                $misProductos = $pedido->items->where('product.user_id', Auth::id());
            @endphp
            
            @if($misProductos->count() > 0 && in_array($pedido->estado, ['pendiente', 'pagado']))
            <div class="bg-white border-2 border-gray-300 rounded-lg p-6 mb-6 shadow hover:shadow-lg transition-shadow">
                <!-- Número del pedido grande -->
                <div class="text-center mb-4">
                    <h2 class="text-3xl font-bold text-orange-600">PEDIDO #{{ $pedido->id }}</h2>
                </div>

                <!-- Etiqueta para el pedido -->
                <div class="bg-yellow-50 border-2 border-yellow-300 p-4 rounded-lg mb-4">
                    <h3 class="text-lg font-bold text-center mb-3">🏷️ ETIQUETA PARA ESTE PEDIDO</h3>
                    <div class="bg-white border-2 border-dashed border-gray-400 p-4 rounded text-center">
                        <div class="text-2xl font-bold text-blue-600 mb-2">
                            {{ strtoupper($pedido->nombre . ' ' . $pedido->apellido) }}
                        </div>
                        <div class="text-lg font-bold text-gray-800">
                            📱 {{ $pedido->telefono }}
                        </div>
                        <div class="text-sm text-gray-600 mt-2">
                            Pedido #{{ $pedido->id }}
                        </div>
                    </div>
                    <p class="text-center text-sm text-yellow-800 mt-2">
                        ☝️ Copia esta información en una etiqueta
                    </p>
                </div>

                <!-- Productos a preparar con unidades CORREGIDO -->
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">🥕 QUÉ PREPARAR:</h3>
                    @foreach($misProductos as $item)
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg mb-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-xl font-bold text-gray-800">{{ $item->product->nombre }}</span>
                                <div class="text-sm text-gray-600">
                                    @if($item->product->categoria)
                                        {{ $item->product->categoria->nombre ?? 'Producto agrícola' }}
                                    @else
                                        Producto agrícola
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold text-green-600">{{ $item->cantidad }}</span>
                                <div class="text-lg font-semibold text-gray-700">
                                    @if($item->product->medida)
                                        {{ $item->product->medida->nombre }}
                                    @else
                                        unidades
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Botones de acción -->
                <div class="grid grid-cols-1 gap-3">
                    @if($pedido->estado === 'pagado')
                    <form action="{{ route('agricultor.confirmar_pedido_listo', $pedido->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('¿Ya tienes todo listo para {{ $pedido->nombre }} {{ $pedido->apellido }}?\n\n✅ Al confirmar se marcará como LISTO')"
                                class="w-full bg-green-500 text-white text-xl font-bold py-4 rounded-lg hover:bg-green-600 transition-colors">
                            ✅ YA ESTÁ LISTO
                        </button>
                    </form>
                    @elseif($pedido->estado === 'pendiente')
                    <div class="w-full bg-yellow-50 border-2 border-yellow-300 text-yellow-800 text-xl font-bold py-4 rounded-lg text-center">
                        ⏳ ESPERANDO PAGO DEL CLIENTE
                    </div>
                    @else
                    <div class="w-full bg-green-50 border-2 border-green-300 text-green-800 text-xl font-bold py-4 rounded-lg text-center">
                        ✅ PEDIDO LISTO
                    </div>
                    @endif

                    <!-- Botón para ver más detalles -->
                    <a href="{{ route('agricultor.pedido.detalle', $pedido->id) }}" 
                       class="w-full bg-gray-500 text-white text-lg font-bold py-3 rounded-lg hover:bg-gray-600 text-center transition-colors">
                        👁️ VER DETALLES
                    </a>
                </div>
            </div>
            @endif
        @endforeach

        <!-- Instrucciones simples -->
        <div class="bg-blue-50 border-2 border-blue-200 p-6 rounded-lg mt-8">
            <h3 class="text-lg font-bold text-blue-800 mb-3">📋 QUÉ HACER:</h3>
            <div class="space-y-2 text-blue-800">
                <p><strong>1.</strong> Prepara cada producto con la cantidad exacta</p>
                <p><strong>2.</strong> Pega la etiqueta en cada pedido</p>
                <p><strong>3.</strong> Presiona "YA ESTÁ LISTO" cuando termines</p>
                <p><strong>4.</strong> Lleva todo a la feria el día acordado</p>
            </div>
        </div>

        <!-- Navegación inferior -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mt-6 text-center">
            <div class="flex justify-center space-x-4">
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors">
                    🏠 INICIO
                </a>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition-colors">
                    ✅ VER LISTOS
                </a>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                    💰 MIS PAGOS
                </a>
            </div>
        </div>
    @endif
</div>
@endsection