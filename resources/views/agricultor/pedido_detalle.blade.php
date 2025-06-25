@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    <!-- Header responsive -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-xl sm:text-2xl font-bold mb-1">📋 Pedido #{{ $pedido->id }}</h1>
                <div class="flex items-center">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($pedido->estado === 'pendiente') bg-orange-100 text-orange-800
                        @elseif($pedido->estado === 'pagado') bg-blue-100 text-blue-800
                        @elseif($pedido->estado === 'listo') bg-amber-100 text-amber-800
                        @elseif($pedido->estado === 'armado') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @if($pedido->estado === 'pendiente') ⏳ Esperando pago
                        @elseif($pedido->estado === 'pagado') 💰 Pagado
                        @elseif($pedido->estado === 'listo') ✋ Listo
                        @elseif($pedido->estado === 'armado') ✅ Armado
                        @else {{ ucfirst($pedido->estado ?? 'Pendiente') }}
                        @endif
                    </span>
                </div>
            </div>
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="bg-white/20 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white/30 transition-all text-sm sm:text-base text-center">
                ← Volver a pendientes
            </a>
        </div>
    </div>

    <!-- Grid responsive para información principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
        <!-- Información del Cliente -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-4 sm:p-6 border-b border-gray-100">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <span class="mr-2">👤</span> Información del Cliente
                </h2>
            </div>
            
            <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 p-3 rounded-lg">
                    <label class="text-xs sm:text-sm font-semibold text-gray-600 block mb-1">Nombre completo</label>
                    <div class="text-sm sm:text-base font-bold text-gray-800">
                        {{ $pedido->nombre }} {{ $pedido->apellido }}
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-3 rounded-lg">
                    <label class="text-xs sm:text-sm font-semibold text-gray-600 block mb-1">Teléfono</label>
                    <div class="text-sm sm:text-base font-bold text-gray-800">
                        <a href="tel:{{ $pedido->telefono }}" class="text-blue-600 hover:underline">
                            📱 {{ $pedido->telefono }}
                        </a>
                    </div>
                </div>

                @if($pedido->delivery === 'delivery' && $pedido->direccion)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 p-3 rounded-lg">
                    <label class="text-xs sm:text-sm font-semibold text-gray-600 block mb-1">🚚 Dirección de entrega</label>
                    <div class="text-sm sm:text-base text-gray-800">{{ $pedido->direccion }}, {{ $pedido->distrito }}</div>
                </div>
                @else
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 p-3 rounded-lg">
                    <label class="text-xs sm:text-sm font-semibold text-gray-600 block mb-1">🏪 Tipo de entrega</label>
                    <div class="text-sm sm:text-base text-gray-800">Retiro en puesto de la feria</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Etiqueta del pedido -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 p-4 sm:p-6 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <span class="mr-2">🏷️</span> Etiqueta del Pedido
                </h3>
            </div>
            
            <div class="p-4 sm:p-6">
                <div class="bg-white border-2 border-dashed border-gray-300 p-3 sm:p-4 rounded-lg text-center">
                    <div class="text-xs text-gray-500 mb-2">COPIAR ESTA INFORMACIÓN</div>
                    <div class="text-sm sm:text-base font-bold text-gray-800 mb-2">PEDIDO #{{ $pedido->id }}</div>
                    <div class="text-base sm:text-lg font-bold text-blue-600 mb-2">
                        {{ strtoupper($pedido->nombre . ' ' . $pedido->apellido) }}
                    </div>
                    <div class="text-xs sm:text-sm text-gray-600 mb-2">
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
    </div>

    <!-- Productos del pedido -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 sm:p-6 border-b border-gray-100">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                <span class="mr-2">🥕</span> Productos a Preparar
            </h2>
        </div>
        
        <div class="p-4 sm:p-6">
            <div class="space-y-3 sm:space-y-4">
                @foreach($productosAgricultor as $item)
                <div class="bg-gray-50 border border-gray-200 p-3 sm:p-4 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
                        <div class="flex-1">
                            <h3 class="text-sm sm:text-base font-bold text-gray-800">{{ $item->product->nombre }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $item->product->categoria->nombre ?? 'Producto agrícola' }}</p>
                            @if($item->product->descripcion)
                            <p class="text-xs text-gray-500 mt-1">{{ $item->product->descripcion }}</p>
                            @endif
                        </div>
                        <div class="text-left sm:text-right flex-shrink-0">
                            <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $item->cantidad }}</div>
                            <div class="text-sm sm:text-base font-semibold text-gray-700">
                                {{ $item->product->medida->nombre ?? 'unidades' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">S/ {{ number_format($item->precio, 2) }} c/u</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Resumen responsivo -->
            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-3 text-sm sm:text-base">📊 Resumen de tus productos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div class="bg-white rounded-lg p-3 border border-blue-100">
                        <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $productosAgricultor->count() }}</div>
                        <div class="text-xs sm:text-sm text-blue-700">Productos diferentes</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-green-100">
                        <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $productosAgricultor->sum('cantidad') }}</div>
                        <div class="text-xs sm:text-sm text-green-700">Cantidad total</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-purple-100">
                        <div class="text-xl sm:text-2xl font-bold text-purple-600">
                            S/ {{ number_format($productosAgricultor->sum(function($item) { return $item->cantidad * $item->precio; }), 2) }}
                        </div>
                        <div class="text-xs sm:text-sm text-purple-700">Valor total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 sm:p-6 border-b border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                <span class="mr-2">⚡</span> Acciones
            </h3>
        </div>
        
        <div class="p-4 sm:p-6">
            @if($pedido->estado === 'pagado')
            <form action="{{ route('agricultor.confirmar_pedido_listo', $pedido->id) }}" method="POST">
                @csrf
                <button type="submit" 
                        onclick="return confirm('¿Confirmas que tienes todos los productos listos y etiquetados para {{ $pedido->nombre }} {{ $pedido->apellido }}?\n\n✅ Al confirmar se marcará como LISTO')"
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all font-bold text-base sm:text-lg py-3 sm:py-4 shadow-lg">
                    ✅ CONFIRMAR PEDIDO LISTO
                </button>
            </form>
            @elseif($pedido->estado === 'pendiente')
            <div class="w-full bg-gradient-to-r from-yellow-100 to-amber-100 border-2 border-yellow-300 text-yellow-800 rounded-lg font-bold text-xs sm:text-lg py-3 sm:py-4 text-center">
                ⏳ ESPERANDO PAGO DEL CLIENTE
            </div>
            @elseif($pedido->estado === 'listo')
            <div class="w-full bg-gradient-to-r from-amber-100 to-yellow-100 border-2 border-amber-300 text-amber-800 rounded-lg font-bold text-xs sm:text-lg py-3 sm:py-4 text-center">
                ✋ PEDIDO MARCADO COMO LISTO
            </div>
            @elseif($pedido->estado === 'armado')
            <div class="w-full bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-300 text-green-800 rounded-lg font-bold text-xs sm:text-lg py-3 sm:py-4 text-center">
                ✅ PEDIDO CONFIRMADO Y ARMADO
            </div>
            @endif
        </div>
    </div>

    <!-- Navegación inferior responsive -->
    <div class="bg-white rounded-xl shadow-lg p-2 overflow-x-auto">
        <div class="flex space-x-2 min-w-max sm:min-w-0 sm:justify-center">
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all text-sm sm:text-base whitespace-nowrap">
                📦 <span class="ml-1 sm:ml-2">PENDIENTES</span>
            </a>
            <a href="{{ route('agricultor.pedidos_listos') }}" 
               class="flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition-all text-sm sm:text-base whitespace-nowrap">
                ✅ <span class="ml-1 sm:ml-2">LISTOS</span>
            </a>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="flex items-center bg-gradient-to-r from-gray-500 to-gray-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:from-gray-600 hover:to-gray-700 transition-all text-sm sm:text-base whitespace-nowrap">
                🏠 <span class="ml-1 sm:ml-2">INICIO</span>
            </a>
        </div>
    </div>
</div>
@endsection