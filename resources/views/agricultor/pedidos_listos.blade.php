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
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-orange-500 hover:text-white transition-colors">
                    📦 PENDIENTES
                </a>
                <span class="bg-green-500 text-white px-3 py-2 rounded text-sm font-bold">
                    ✅ LISTOS
                </span>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-blue-500 hover:text-white transition-colors">
                    💰 PAGOS
                </a>
            </div>
        </div>
    </div>

    <!-- Título simple -->
    <div class="bg-green-500 text-white p-6 rounded-lg mb-6 text-center">
        <h1 class="text-2xl font-bold">✅ MIS PEDIDOS LISTOS</h1>
        <p class="text-lg mt-2">{{ $pedidos->count() }} pedidos preparados</p>
    </div>

    @if($pedidos->isEmpty())
        <div class="bg-gray-50 border-2 border-gray-200 p-8 rounded-lg text-center">
            <h2 class="text-xl font-bold text-gray-800">📋 No tienes pedidos listos</h2>
            <p class="text-gray-700 mt-2">Cuando marques pedidos como "LISTO", aparecerán aquí</p>
            <div class="flex justify-center space-x-4 mt-6">
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="bg-orange-500 text-white px-6 py-3 rounded-lg text-lg font-bold hover:bg-orange-600">
                    📦 VER PENDIENTES
                </a>
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg text-lg font-bold hover:bg-gray-600">
                    🏠 INICIO
                </a>
            </div>
        </div>
    @else
        <!-- Lista simple de pedidos listos -->
        @foreach($pedidos as $pedido)
            @php
                $misProductos = $pedido->items->where('product.user_id', Auth::id());
                $valorTotal = $misProductos->sum(function($item) {
                    return $item->cantidad * $item->precio;
                });
            @endphp
            
            @if($misProductos->count() > 0)
            <div class="bg-white border-2 border-gray-300 rounded-lg p-6 mb-6 shadow hover:shadow-lg transition-shadow">
                <!-- Estado del pedido -->
                <div class="text-center mb-4">
                    @if($pedido->estado === 'armado')
                    <div class="bg-green-50 border-2 border-green-300 p-3 rounded-lg">
                        <h2 class="text-2xl font-bold text-green-800">🎯 PEDIDO #{{ $pedido->id }} - LISTO PARA PAGO</h2>
                        <p class="text-green-700 font-bold">S/ {{ number_format($valorTotal, 2) }} - Ya recibirás tu pago</p>
                        <div class="text-sm text-green-600 mt-1">
                            Pedido armado - Se incluirá en tu pago semanal
                        </div>
                    </div>
                    @elseif($pedido->estado === 'entregado')
                    <div class="bg-blue-50 border-2 border-blue-300 p-3 rounded-lg">
                        <h2 class="text-2xl font-bold text-blue-800">✅ PEDIDO #{{ $pedido->id }} - ENTREGADO</h2>
                        <p class="text-blue-700 font-bold">S/ {{ number_format($valorTotal, 2) }} - Pagado</p>
                        <div class="text-sm text-blue-600 mt-1">
                            Pedido completado y entregado al cliente
                        </div>
                    </div>
                    @else
                    <div class="bg-orange-50 border-2 border-orange-300 p-3 rounded-lg">
                        <h2 class="text-2xl font-bold text-orange-800">📦 PEDIDO #{{ $pedido->id }} - EN PROCESO</h2>
                        <p class="text-orange-700 font-bold">S/ {{ number_format($valorTotal, 2) }} - En proceso</p>
                    </div>
                    @endif
                </div>

                <!-- Información del cliente -->
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4">
                    <h3 class="text-lg font-bold text-blue-800 mb-2">👤 CLIENTE:</h3>
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-900">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                        <div class="text-lg text-blue-800">📱 {{ $pedido->telefono }}</div>
                    </div>
                </div>

                <!-- Productos preparados con unidades -->
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">📦 LO QUE PREPARASTE:</h3>
                    @foreach($misProductos as $item)
                    <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg mb-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-lg font-bold text-gray-800">{{ $item->product->nombre }}</span>
                                <div class="text-sm text-gray-600">{{ $item->product->categoria->nombre ?? 'Producto' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-green-600">{{ $item->cantidad }}</span>
                                <div class="text-lg font-semibold text-gray-700">
                                    {{ $item->product->medida->nombre ?? 'und' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Botones de acción -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <a href="{{ route('agricultor.pedido.detalle', $pedido->id) }}" 
                       class="bg-gray-500 text-white text-lg font-bold py-3 rounded-lg hover:bg-gray-600 text-center transition-colors">
                        👁️ VER DETALLES
                    </a>
                </div>
            </div>
            @endif
        @endforeach

        <!-- Información sobre pagos -->
        <div class="bg-yellow-50 border-2 border-yellow-200 p-6 rounded-lg mt-8">
            <h3 class="text-lg font-bold text-yellow-800 mb-3">💰 SOBRE TUS PAGOS:</h3>
            <div class="space-y-2 text-yellow-800">
                <p><strong>🎯 Verde "PAGADO":</strong> Ya recibirás dinero por este pedido</p>
                <p><strong>⏳ Naranja "ESPERANDO":</strong> Falta que lo armen en la feria</p>
                <p><strong>📅 Pagos:</strong> Se hacen cada semana</p>
            </div>
        </div>

        <!-- Navegación inferior -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mt-6 text-center">
            <div class="flex justify-center space-x-4">
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors">
                    🏠 INICIO
                </a>
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="bg-orange-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition-colors">
                    📦 VER PENDIENTES
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