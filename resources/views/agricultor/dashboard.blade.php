@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header principal con animación -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg animate-fade-in">
        <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-bounce-slow">
                <span class="text-2xl sm:text-3xl">🌱</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">¡Hola {{ Auth::user()->name }}!</h1>
            <p class="text-green-100 text-base sm:text-lg">Tu espacio de trabajo en Punto Verde</p>
        </div>
    </div>

    <!-- Navegación rápida simplificada -->
    <div class="mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-2 overflow-x-auto">
            <div class="flex space-x-1 min-w-max sm:min-w-0 sm:justify-center">
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    📦 <span class="ml-1 sm:ml-2">PENDIENTES</span>
                </a>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    ✅ <span class="ml-1 sm:ml-2">LISTOS</span>
                </a>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    💰 <span class="ml-1 sm:ml-2">PAGOS</span>
                </a>
                <a href="{{ route('productos.index') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    🥕 <span class="ml-1 sm:ml-2">PRODUCTOS</span>
                </a>
            </div>
        </div>
    </div>

    @php
        // Calcular estadísticas
        $pendientesQuery = \App\Models\Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })->whereIn('estado', ['pendiente', 'pagado']);
        $pendientes = $pendientesQuery->count();

        $listosQuery = \App\Models\Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })->whereIn('estado', ['listo', 'armado', 'entregado']);
        $listos = $listosQuery->count();

        $totalProductos = \App\Models\Product::where('user_id', Auth::id())->count();
    @endphp

    <!-- Acciones principales con efectos -->
    <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        
        <!-- Pedidos por armar - Con urgencia animada -->
        <a href="{{ route('agricultor.pedidos_pendientes') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-left {{ $pendientes > 0 ? 'border-red-400 ring-2 ring-red-100 animate-pulse-border' : 'border-gray-300' }}">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-inner {{ $pendientes > 0 ? 'animate-bounce-slow' : '' }}">
                        <span class="text-2xl sm:text-3xl">📦</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS POR ARMAR</h2>
                    @if($pendientes > 0)
                        <div class="flex items-center space-x-2">
                            <div class="bg-red-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center animate-pulse shadow-lg">
                                <span class="text-sm sm:text-base font-bold">{{ $pendientes }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-red-600 font-semibold">{{ $pendientes == 1 ? 'pedido esperando' : 'pedidos esperando' }}</p>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">¡Necesita tu atención ahora!</p>
                    @else
                        <p class="text-sm sm:text-base text-green-600 font-semibold">✅ Todo al día</p>
                        <p class="text-xs sm:text-sm text-gray-400">No hay pedidos pendientes</p>
                    @endif
                </div>
                @if($pendientes > 0)
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                    </div>
                @endif
            </div>
        </a>

        <!-- Pedidos listos -->
        <a href="{{ route('agricultor.pedidos_listos') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-green-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-right">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center shadow-inner">
                        <span class="text-2xl sm:text-3xl">✅</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS LISTOS</h2>
                    <p class="text-sm sm:text-base text-green-600 font-semibold">{{ $listos }} {{ $listos == 1 ? 'pedido preparado' : 'pedidos preparados' }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Esperando armado o entregados</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Dashboard de estadísticas con animación -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2 animate-bounce-slow">📊</span> Mi resumen de hoy
        </h3>
        
        <div class="grid grid-cols-3 gap-2 sm:gap-4">
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 p-3 sm:p-4 rounded-xl hover:border-green-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group {{ $pendientes > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200' }}">
                <div class="text-2xl sm:text-3xl font-bold text-gray-700 mb-1 group-hover:text-green-600 transition-colors {{ $pendientes > 0 ? 'text-red-500 animate-bounce-slow' : '' }}">{{ $pendientes }}</div>
                <div class="text-xs sm:text-sm text-gray-600 font-medium group-hover:text-green-700 transition-colors">Por armar</div>
                @if($pendientes > 0)
                    <div class="w-2 h-2 bg-red-500 rounded-full mx-auto mt-1 animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('agricultor.pedidos_listos') }}" 
               class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 p-3 sm:p-4 rounded-xl hover:border-green-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-gray-700 mb-1 group-hover:text-green-600 transition-colors">{{ $listos }}</div>
                <div class="text-xs sm:text-sm text-gray-600 font-medium group-hover:text-green-700 transition-colors">Listos</div>
            </a>
            
            <a href="{{ route('productos.index') }}" 
               class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 p-3 sm:p-4 rounded-xl hover:border-green-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-gray-700 mb-1 group-hover:text-green-600 transition-colors">{{ $totalProductos }}</div>
                <div class="text-xs sm:text-sm text-gray-600 font-medium group-hover:text-green-700 transition-colors">Productos</div>
            </a>
        </div>
    </div>

    <!-- Acciones secundarias simplificadas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 sm:mb-8">
        
        <!-- Mis pagos -->
        <a href="{{ route('agricultor.pagos') }}" 
           class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-xl p-4 sm:p-6 hover:border-green-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-left">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-green-100 group-hover:to-green-200 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">💰</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">MIS PAGOS</h3>
            <p class="text-sm text-gray-600 group-hover:text-green-700 transition-colors">Ver dinero ganado esta semana</p>
        </a>

        <!-- Mis productos -->
        <a href="{{ route('productos.index') }}" 
           class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-xl p-4 sm:p-6 hover:border-green-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-right">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-green-100 group-hover:to-green-200 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">🥕</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">MIS PRODUCTOS</h3>
            <p class="text-sm text-gray-600 group-hover:text-green-700 transition-colors">Administrar mi catálogo</p>
        </a>
    </div>

    <!-- Guía rápida mejorada con efectos -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-green-800 mb-4 flex items-center">
            <span class="mr-2 animate-bounce-slow">💡</span> ¿Cómo usar tu panel?
        </h3>
        
        <div class="space-y-3">
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">1</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Revisa "PEDIDOS POR ARMAR"</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Si hay números rojos, ¡tienes trabajo que hacer!</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.2s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">2</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Prepara los productos exactos</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Revisa las cantidades y marca como "listo" cuando termines</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.3s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">3</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Lleva todo a la feria</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Los sábados, entrega tus productos preparados</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.4s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">4</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Revisa tus pagos</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Cada semana te pagamos por todo lo que vendiste</p>
                </div>
            </div>
        </div>
    </div>

    @if($pendientes > 0)
    <!-- Mensaje de urgencia animado -->
    <div class="mt-4 sm:mt-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-400 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl animate-bounce">🚨</span>
            </div>
            <div class="ml-3">
                <h4 class="text-base sm:text-lg font-bold text-red-800">¡Atención!</h4>
                <p class="text-sm sm:text-base text-red-700">
                    Tienes <strong>{{ $pendientes }}</strong> {{ $pendientes == 1 ? 'pedido que necesita' : 'pedidos que necesitan' }} tu atención.
                    <a href="{{ route('agricultor.pedidos_pendientes') }}" class="underline font-semibold hover:text-red-900 animate-pulse">
                        Ver ahora →
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes slideInLeft {
    from { 
        opacity: 0; 
        transform: translateX(-30px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

@keyframes slideInRight {
    from { 
        opacity: 0; 
        transform: translateX(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

@keyframes bounceSlow {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
    20%, 40%, 60%, 80% { transform: translateX(2px); }
}

@keyframes pulseBorder {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3); 
    }
    50% { 
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); 
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

.animate-slide-in-left {
    animation: slideInLeft 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slideInRight 0.6s ease-out;
}

.animate-bounce-slow {
    animation: bounceSlow 2s infinite;
}

.animate-shake {
    animation: shake 0.5s ease-in-out infinite;
}

.animate-pulse-border {
    animation: pulseBorder 2s infinite;
}
</style>

@endsection