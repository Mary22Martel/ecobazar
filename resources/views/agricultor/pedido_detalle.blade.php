@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-8 max-w-4xl px-4">
    <!-- Navegación superior -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mb-6 shadow">
        <div class="flex justify-between items-center">
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="flex items-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                ← Volver a pendientes
            </a>
            <div class="flex space-x-2">
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-gray-500 hover:text-white transition-colors">
                    🏠 INICIO
                </a>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-bold hover:bg-green-500 hover:text-white transition-colors">
                    ✅ LISTOS
                </a>
            </div>
        </div>
    </div>

    <!-- Header del pedido -->
    <div class="bg-blue-600 text-white p-6 rounded-lg mb-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-2">📋 Pedido #{{ $pedido->id }}</h1>
            <span class="inline-flex px-4 py-2 text-lg font-bold rounded-full
                @if($pedido->estado === 'pendiente') bg-orange-100 text-orange-800
                @elseif($pedido->estado === 'pagado') bg-blue-100 text-blue-800
                @elseif($pedido->estado === 'armado') bg-green-100 text-green-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($pedido->estado ?? 'Pendiente') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información del Cliente -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">👤 INFORMACIÓN DEL CLIENTE</h2>
            
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                    <label class="text-sm font-semibold text-gray-600">Nombre completo</label>
                    <div class="text-lg font-bold text-gray-800">
                        {{ $pedido->nombre }} {{ $pedido->apellido }}
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                    <label class="text-sm font-semibold text-gray-600">Teléfono</label>
                    <div class="text-lg font-bold text-gray-800">
                        {{ $pedido->telefono }}
                    </div>
                </div>

                @if($pedido->delivery === 'delivery' && $pedido->direccion)
                <div class="bg-green-50 border border-green-200 p-3 rounded-lg">
                    <label class="text-sm font-semibold text-gray-600">🚚 Dirección de entrega</label>
                    <div class="text-gray-800">{{ $pedido->direccion }}, {{ $pedido->distrito }}</div>
                </div>
                @else
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                    <label class="text-sm font-semibold text-gray-600">🏪 Tipo de entrega</label>
                    <div class="text-gray-800">Retiro en puesto de la feria</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Etiqueta del pedido -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4">🏷️ ETIQUETA DEL PEDIDO</h3>
            <div class="bg-white border-2 border-dashed border-gray-400 p-4 rounded-lg text-center">
                <div class="text-xs text-gray-500 mb-2">COPIAR ESTA INFORMACIÓN</div>
                <div class="text-lg font-bold text-gray-800 mb-2">PEDIDO #{{ $pedido->id }}</div>
                <div class="text-xl font-bold text-blue-600 mb-2">
                    {{ strtoupper($pedido->nombre . ' ' . $pedido->apellido) }}
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    📱 {{ $pedido->telefono }}
                </div>
                <div class="text-xs text-gray-500 mb-2">
                    {{ $productosAgricultor->count() }} productos | {{ $productosAgricultor->sum('cantidad') }} unidades
                </div>
                <div class="text-xs text-gray-400">
                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-2 text-center">
                ☝️ Copia esta información en tu etiqueta
            </div>
        </div>
    </div>

    <!-- Productos del pedido -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">🥕 PRODUCTOS A PREPARAR</h2>
        
        <div class="space-y-4">
            @foreach($productosAgricultor as $item)
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $item->product->nombre }}</h3>
                        <p class="text-gray-600">{{ $item->product->categoria->nombre ?? 'Producto agrícola' }}</p>
                        @if($item->product->descripcion)
                        <p class="text-sm text-gray-500 mt-1">{{ $item->product->descripcion }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600">{{ $item->cantidad }}</div>
                        <div class="text-lg font-semibold text-gray-700">
                            {{ $item->product->medida->nombre ?? 'unidades' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">S/ {{ number_format($item->precio, 2) }} c/u</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Resumen -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-2">📊 Resumen de tus productos</h3>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $productosAgricultor->count() }}</div>
                    <div class="text-sm text-blue-700">Productos diferentes</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $productosAgricultor->sum('cantidad') }}</div>
                    <div class="text-sm text-green-700">Cantidad total</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">
                        S/ {{ number_format($productosAgricultor->sum(function($item) { return $item->cantidad * $item->precio; }), 2) }}
                    </div>
                    <div class="text-sm text-purple-700">Valor total</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow mt-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">⚡ ACCIONES</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($pedido->estado !== 'armado')
            <form action="{{ route('agricultor.confirmar_pedido_listo', $pedido->id) }}" method="POST" class="md:col-span-2">
                @csrf
                <button type="submit" 
                        onclick="return confirm('¿Confirmas que tienes todos los productos listos y etiquetados para {{ $pedido->nombre }} {{ $pedido->apellido }}?\n\n✅ Al confirmar se marcará como LISTO')"
                        class="w-full px-4 py-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-bold text-xl">
                    ✅ CONFIRMAR PEDIDO LISTO
                </button>
            </form>
            @else
            <div class="text-center md:col-span-2">
                <span class="inline-flex items-center px-4 py-4 bg-green-50 border-2 border-green-300 text-green-800 rounded-lg font-bold text-xl w-full justify-center">
                    ✅ PEDIDO CONFIRMADO COMO LISTO
                </span>
            </div>
            @endif

        </div>
    </div>

    <!-- Navegación inferior -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mt-6 text-center">
        <div class="flex justify-center space-x-4">
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="bg-orange-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition-colors">
                📦 PENDIENTES
            </a>
            <a href="{{ route('agricultor.pedidos_listos') }}" 
               class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition-colors">
                ✅ LISTOS
            </a>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors">
                🏠 INICIO
            </a>
        </div>
    </div>
</div>
@endsection