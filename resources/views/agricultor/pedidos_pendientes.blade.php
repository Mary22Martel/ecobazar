@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    <!-- Header responsive mejorado -->
    <div class="bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-xl sm:text-2xl font-bold mb-1">📦 MIS PEDIDOS POR ARMAR</h1>
                <p class="text-orange-100 text-sm sm:text-base">{{ $pedidos->whereIn('estado', ['pendiente', 'pagado'])->count() }} pedidos esperando</p>
            </div>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="bg-white/20 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white/30 transition-all text-sm sm:text-base text-center">
                ← Volver al inicio
            </a>
        </div>
    </div>

    <!-- Navegación de pestañas responsive -->
    <div class="mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-2 overflow-x-auto">
            <div class="flex space-x-1 min-w-max sm:min-w-0 sm:justify-center">
                <div class="flex items-center px-3 sm:px-6 py-2 sm:py-3 rounded-lg bg-orange-500 text-white font-semibold shadow-sm text-sm sm:text-base whitespace-nowrap">
                    📦 <span class="ml-1 sm:ml-2">PENDIENTES</span>
                </div>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="flex items-center px-3 sm:px-6 py-2 sm:py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-600 transition-all font-semibold text-sm sm:text-base whitespace-nowrap">
                    ✅ <span class="ml-1 sm:ml-2">LISTOS</span>
                </a>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="flex items-center px-3 sm:px-6 py-2 sm:py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all font-semibold text-sm sm:text-base whitespace-nowrap">
                    💰 <span class="ml-1 sm:ml-2">PAGOS</span>
                </a>
            </div>
        </div>
    </div>

    @if($pedidos->isEmpty())
        <!-- Estado vacío responsive -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-6 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="text-4xl sm:text-6xl mb-3 sm:mb-4">🎉</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">¡No tienes pedidos pendientes!</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base">Descansa o revisa si hay nuevos pedidos más tarde</p>
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg">
                    🏠 Volver al inicio
                </a>
            </div>
        </div>
    @else
        <!-- Lista de pedidos responsive -->
        <div class="space-y-4 sm:space-y-6">
            @foreach($pedidos as $pedido)
                @php
                    $misProductos = $pedido->items->where('product.user_id', Auth::id());
                @endphp
                
                @if($misProductos->count() > 0 && in_array($pedido->estado, ['pendiente', 'pagado']))
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                    
                    <!-- Header del pedido responsive -->
                    <div class="bg-gradient-to-r from-orange-400 to-amber-500 p-4 sm:p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold">PEDIDO #{{ $pedido->id }}</h2>
                                    <p class="text-orange-100 text-sm sm:text-base">
                                        @if($pedido->estado === 'pagado')
                                            ✅ Listo para preparar
                                        @else
                                            ⏳ Esperando pago
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del pedido responsive -->
                    <div class="p-4 sm:p-6">
                        <!-- Etiqueta del cliente -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border border-blue-100">
                            <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-2 text-center">🏷️ ETIQUETA PARA ESTE PEDIDO</h3>
                            <div class="bg-white border-2 border-dashed border-gray-300 p-3 sm:p-4 rounded-lg text-center">
                                <div class="text-lg sm:text-xl font-bold text-blue-600 mb-1">
                                    {{ strtoupper($pedido->nombre . ' ' . $pedido->apellido) }}
                                </div>
                                <!-- <div class="text-sm sm:text-base font-semibold text-gray-800 mb-1">
                                    📱 {{ $pedido->telefono }}
                                </div> -->
                                <div class="text-xs sm:text-sm text-gray-600">
                                    Pedido #{{ $pedido->id }}
                                </div>
                            </div>
                            <p class="text-center text-xs sm:text-sm text-blue-700 mt-2">
                                ☝️ Copia esta información en una etiqueta
                            </p>
                        </div>

                        <!-- Productos a preparar -->
                        <div class="mb-4 sm:mb-6">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🥕</span> Lo que debes preparar:
                            </h3>
                            <div class="space-y-2 sm:space-y-3">
                                @foreach($misProductos as $item)
                                <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <h4 class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $item->product->nombre }}</h4>
                                            <p class="text-gray-500 text-xs sm:text-sm">
                                                @if($item->product->categoria)
                                                    {{ $item->product->categoria->nombre ?? 'Producto agrícola' }}
                                                @else
                                                    Producto agrícola
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $item->cantidad }}</div>
                                            <div class="text-gray-600 font-medium text-xs sm:text-sm">
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
                        </div>

                        <!-- Botones de acción responsive -->
                        <div class="space-y-3">
                            @if($pedido->estado === 'pagado')
                            <form action="{{ route('agricultor.confirmar_pedido_listo', $pedido->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('¿Ya tienes todo listo para {{ $pedido->nombre }} {{ $pedido->apellido }}?\n\n✅ Al confirmar se marcará como LISTO')"
                                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white text-base sm:text-lg font-bold py-3 sm:py-4 rounded-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg">
                                    ✅ YA ESTÁ LISTO
                                </button>
                            </form>
                            @elseif($pedido->estado === 'pendiente')
                            <div class="w-full bg-gradient-to-r from-yellow-100 to-amber-100 border-2 border-yellow-300 text-yellow-800 text-xs sm:text-lg font-bold py-3 sm:py-4 rounded-lg text-center">
                                ⏳ ESPERANDO PAGO DEL CLIENTE
                            </div>
                            @endif

                            <!-- Botón para ver más detalles -->
                            <a href="{{ route('agricultor.pedido.detalle', $pedido->id) }}" 
                               class="w-full bg-gradient-to-r from-gray-600 to-gray-700 text-white text-sm sm:text-base font-semibold py-3 rounded-lg hover:from-gray-700 hover:to-gray-800 text-center transition-all flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Ver todos los detalles
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Instrucciones simples -->
        <div class="mt-8 sm:mt-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4 text-center">📋 QUÉ HACER:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">📦</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">1. Prepara</p>
                    <p class="text-xs text-gray-600">Cada producto con la cantidad exacta</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">🏷️</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">2. Etiqueta</p>
                    <p class="text-xs text-gray-600">Pega la etiqueta en cada pedido</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">✅</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">3. Confirma</p>
                    <p class="text-xs text-gray-600">Presiona "YA ESTÁ LISTO"</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">🚚</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">4. Lleva</p>
                    <p class="text-xs text-gray-600">Todo a la feria el sábado</p>
                </div>
            </div>
        </div>

        <!-- Navegación inferior responsive -->
        <div class="mt-6 sm:mt-8">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-2 overflow-x-auto">
                <div class="flex space-x-2 min-w-max sm:min-w-0 sm:justify-center">
                    <a href="{{ route('agricultor.dashboard') }}" 
                       class="flex items-center bg-gradient-to-r from-gray-500 to-gray-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold hover:from-gray-600 hover:to-gray-700 transition-all text-sm sm:text-base whitespace-nowrap">
                        🏠 <span class="ml-1 sm:ml-2">INICIO</span>
                    </a>
                    <a href="{{ route('agricultor.pedidos_listos') }}" 
                       class="flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all text-sm sm:text-base whitespace-nowrap">
                        ✅ <span class="ml-1 sm:ml-2">LISTOS</span>
                    </a>
                    <a href="{{ route('agricultor.pagos') }}" 
                       class="flex items-center bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all text-sm sm:text-base whitespace-nowrap">
                        💰 <span class="ml-1 sm:ml-2">PAGOS</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection