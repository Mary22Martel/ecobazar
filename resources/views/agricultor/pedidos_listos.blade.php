@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    <!-- Header responsive mejorado -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">✅ MIS PEDIDOS LISTOS</h1>
                <p class="text-green-100 text-base sm:text-lg">{{ $pedidos->count() }} pedidos que ya preparaste</p>
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
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="flex items-center px-3 sm:px-6 py-2 sm:py-3 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all font-semibold text-sm sm:text-base whitespace-nowrap">
                    📦 <span class="ml-1 sm:ml-2">PENDIENTES</span>
                </a>
                <div class="flex items-center px-3 sm:px-6 py-2 sm:py-3 rounded-lg bg-green-500 text-white font-semibold shadow-sm text-sm sm:text-base whitespace-nowrap">
                    ✅ <span class="ml-1 sm:ml-2">LISTOS</span>
                </div>
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
                <div class="text-4xl sm:text-6xl mb-3 sm:mb-4">📋</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">No tienes pedidos listos</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-base sm:text-lg">Cuando marques pedidos como "LISTO", aparecerán aquí para hacer seguimiento</p>
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                       class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-orange-600 hover:to-orange-700 transform hover:scale-105 transition-all shadow-lg">
                        📦 VER PENDIENTES
                    </a>
                    <a href="{{ route('agricultor.dashboard') }}" 
                       class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all shadow-lg">
                        🏠 INICIO
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Lista de pedidos responsive -->
        <div class="space-y-4 sm:space-y-6">
            @foreach($pedidos as $pedido)
                @php
                    $misProductos = $pedido->items->where('product.user_id', Auth::id());
                    $valorTotal = $misProductos->sum(function($item) {
                        return $item->cantidad * $item->precio;
                    });
                @endphp
                
                @if($misProductos->count() > 0)
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                    
                    <!-- Header del pedido responsive -->
                    @if($pedido->estado === 'listo')
                    <div class="bg-gradient-to-r from-amber-400 to-yellow-500 p-4 sm:p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold">PEDIDO #{{ $pedido->id }}</h2>
                                    <p class="text-amber-100 text-sm sm:text-lg">✋ Esperando que armen tu pedido</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl sm:text-3xl font-bold">S/ {{ number_format($valorTotal, 2) }}</div>
                                <div class="text-amber-100 text-sm">Tu ganancia</div>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 bg-white/10 rounded-lg p-3">
                            <p class="text-xs sm:text-sm">🎯 <strong>¡Bien hecho!</strong> Ya preparaste todo. El admin armará el pedido completo y después recibirás tu pago.</p>
                        </div>
                    </div>
                    @elseif($pedido->estado === 'armado')
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 sm:p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold">PEDIDO #{{ $pedido->id }}</h2>
                                    <p class="text-green-100 text-sm sm:text-lg">🎯 ¡Listo para tu pago!</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl sm:text-3xl font-bold">S/ {{ number_format($valorTotal, 2) }}</div>
                                <div class="text-green-100 text-sm">Ya contabilizado</div>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 bg-white/10 rounded-lg p-3">
                            <p class="text-xs sm:text-sm">💰 <strong>¡Excelente!</strong> El pedido está armado. Este dinero se incluirá en tu pago semanal.</p>
                        </div>
                    </div>
                    @elseif($pedido->estado === 'entregado')
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 sm:p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold">PEDIDO #{{ $pedido->id }}</h2>
                                    <p class="text-blue-100 text-sm sm:text-lg">✅ Entregado al cliente</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl sm:text-3xl font-bold">S/ {{ number_format($valorTotal, 2) }}</div>
                                <div class="text-blue-100 text-sm">Completado</div>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 bg-white/10 rounded-lg p-3">
                            <p class="text-xs sm:text-sm">🎉 <strong>¡Misión cumplida!</strong> El cliente ya recibió su pedido. Trabajo excelente.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Contenido del pedido responsive -->
                    <div class="p-4 sm:p-6">
                        <!-- Info del cliente responsive -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border border-blue-100">
                            <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                                <div class="bg-blue-100 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-1 truncate">
                                        Cliente: {{ $pedido->nombre }} {{ $pedido->apellido }}
                                    </h3>
                                    <p class="text-gray-600 flex items-center text-sm sm:text-base">
                                        <span class="mr-2">📱</span> 
                                        <a href="tel:{{ $pedido->telefono }}" class="text-blue-600 underline">{{ $pedido->telefono }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Productos responsive -->
                        <div class="mb-4 sm:mb-6">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center">
                                <span class="mr-2">📦</span> Lo que preparaste:
                            </h3>
                            <div class="space-y-2 sm:space-y-3">
                                @foreach($misProductos as $item)
                                <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200 hover:bg-gray-100 transition-colors">
                                    <div class="flex justify-between items-start sm:items-center">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <h4 class="text-base sm:text-lg font-semibold text-gray-800 truncate">{{ $item->product->nombre }}</h4>
                                            <p class="text-gray-500 text-xs sm:text-sm">{{ $item->product->categoria->nombre ?? 'Producto' }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $item->cantidad }}</div>
                                            <div class="text-gray-600 font-medium text-sm">{{ $item->product->medida->nombre ?? 'und' }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Botón de acción responsive -->
                        <div class="text-center">
                            <a href="{{ route('agricultor.pedido.detalle', $pedido->id) }}" 
                               class="inline-flex items-center justify-center w-full sm:w-auto bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 sm:px-8 py-3 rounded-xl font-semibold hover:from-gray-700 hover:to-gray-800 transform hover:scale-105 transition-all shadow-lg text-sm sm:text-base">
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

        <!-- Guía de estados responsive -->
        <div class="mt-8 sm:mt-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-blue-100">
            <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">💡 ¿Cómo funciona el proceso?</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center text-white text-lg sm:text-2xl font-bold mx-auto mb-3 sm:mb-4">1</div>
                    <h4 class="font-bold text-gray-800 mb-2 text-sm sm:text-base">Preparas tus productos</h4>
                    <p class="text-gray-600 text-xs sm:text-sm">Marcas como "listo" cuando tienes todo preparado</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-full w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center text-white text-lg sm:text-2xl font-bold mx-auto mb-3 sm:mb-4">2</div>
                    <h4 class="font-bold text-gray-800 mb-2 text-sm sm:text-base">Admin arma el pedido</h4>
                    <p class="text-gray-600 text-xs sm:text-sm">Junta todos los productos y lo prepara para entregar</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-full w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center text-white text-lg sm:text-2xl font-bold mx-auto mb-3 sm:mb-4">3</div>
                    <h4 class="font-bold text-gray-800 mb-2 text-sm sm:text-base">Recibes tu pago</h4>
                    <p class="text-gray-600 text-xs sm:text-sm">Cada semana te pagamos por todos los pedidos entregados</p>
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
                    <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                       class="flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 transition-all text-sm sm:text-base whitespace-nowrap">
                        📦 <span class="ml-1 sm:ml-2">PENDIENTES</span>
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