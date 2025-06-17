@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header principal con animación -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg animate-fade-in">
        <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-bounce-slow">
                <span class="text-2xl sm:text-3xl">⚙️</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">¡Hola Administrador!</h1>
            <p class="text-blue-100 text-base sm:text-lg">Panel de gestión Punto Verde</p>
        </div>
    </div>

    <!-- Navegación rápida simplificada -->
    <div class="mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-2 overflow-x-auto">
            <div class="flex space-x-1 min-w-max sm:min-w-0 sm:justify-center">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    📦 <span class="ml-1 sm:ml-2">PEDIDOS</span>
                </a>
                  <a href="{{ route('admin.pagos.agricultores') }}" 
                    class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                        💰 <span class="ml-1 sm:ml-2">PAGOS</span>
                  </a>
                <a href="{{ route('admin.reportes.semanales') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    📈 <span class="ml-1 sm:ml-2">REPORTES</span>
                </a>
                <a href="{{ route('admin.configuracion.zonas') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    🗺️ <span class="ml-1 sm:ml-2">CONFIG</span>
                </a>
                <a href="{{ route('admin.usuarios.index') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    👥 <span class="ml-1 sm:ml-2">USUARIOS</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Acciones principales con efectos -->
    <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        
        <!-- Pedidos por armar - Con urgencia animada -->
        <a href="{{ route('admin.pedidos.index') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-left {{ $pedidosListos > 0 ? 'border-orange-400 ring-2 ring-orange-100 animate-pulse-border' : 'border-gray-300' }}">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center shadow-inner {{ $pedidosListos > 0 ? 'animate-bounce-slow' : '' }}">
                        <span class="text-2xl sm:text-3xl">📋</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS POR ARMAR</h2>
                    @if($pedidosListos > 0)
                        <div class="flex items-center space-x-2">
                            <div class="bg-orange-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center animate-pulse shadow-lg">
                                <span class="text-sm sm:text-base font-bold">{{ $pedidosListos }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-orange-600 font-semibold">{{ $pedidosListos == 1 ? 'pedido listo' : 'pedidos listos' }}</p>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">¡Necesita ser armado ahora!</p>
                    @else
                        <p class="text-sm sm:text-base text-green-600 font-semibold">✅ Todo armado</p>
                        <p class="text-xs sm:text-sm text-gray-400">No hay pedidos por armar</p>
                    @endif
                </div>
                @if($pedidosListos > 0)
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full animate-ping"></div>
                    </div>
                @endif
            </div>
        </a>

        <!-- Pedidos urgentes -->
        <a href="{{ route('admin.pedidos.index') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-right {{ $pedidosUrgentes > 0 ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-300' }}">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center shadow-inner">
                        <span class="text-2xl sm:text-3xl">🚨</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS URGENTES</h2>
                    @if($pedidosUrgentes > 0)
                        <div class="flex items-center space-x-2">
                            <div class="bg-red-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center shadow-lg">
                                <span class="text-sm sm:text-base font-bold">{{ $pedidosUrgentes }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-red-600 font-semibold">más de 1 día</p>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Pedidos con retraso</p>
                    @else
                        <p class="text-sm sm:text-base text-green-600 font-semibold">✅ Todo al día</p>
                        <p class="text-xs sm:text-sm text-gray-400">Sin pedidos atrasados</p>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <!-- Dashboard de estadísticas con animación -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2 animate-bounce-slow">📊</span> Resumen de hoy
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 p-3 sm:p-4 rounded-xl hover:border-blue-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group border-blue-200">
                <div class="text-2xl sm:text-3xl font-bold text-blue-700 mb-1 group-hover:text-blue-600 transition-colors">{{ $pedidosHoy }}</div>
                <div class="text-xs sm:text-sm text-blue-600 font-medium group-hover:text-blue-700 transition-colors">Pedidos hoy</div>
            </a>
            
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 p-3 sm:p-4 rounded-xl hover:border-orange-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-orange-700 mb-1 group-hover:text-orange-600 transition-colors {{ $pedidosPendientes > 0 ? 'animate-bounce-slow' : '' }}">{{ $pedidosPendientes }}</div>
                <div class="text-xs sm:text-sm text-orange-600 font-medium group-hover:text-orange-700 transition-colors">Pendientes</div>
                @if($pedidosPendientes > 0)
                    <div class="w-2 h-2 bg-orange-500 rounded-full mx-auto mt-1 animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 p-3 sm:p-4 rounded-xl hover:border-green-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-green-700 mb-1 group-hover:text-green-600 transition-colors">{{ $pedidosArmados }}</div>
                <div class="text-xs sm:text-sm text-green-600 font-medium group-hover:text-green-700 transition-colors">Armados</div>
            </a>
            
            <a href="{{ route('admin.reportes.semanales') }}" 
               class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 p-3 sm:p-4 rounded-xl hover:border-purple-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-purple-700 mb-1 group-hover:text-purple-600 transition-colors">{{ $agricultoresActivos }}</div>
                <div class="text-xs sm:text-sm text-purple-600 font-medium group-hover:text-purple-700 transition-colors">Activos</div>
            </a>
        </div>
    </div>

    <!-- Configuraciones del Sistema -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2">⚙️</span> Configuraciones del Sistema
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
            <a href="{{ route('admin.configuracion.zonas') }}" 
               class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 p-3 sm:p-4 rounded-xl hover:border-indigo-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-indigo-700 mb-1 group-hover:text-indigo-600 transition-colors">{{ $totalZonas }}</div>
                <div class="text-xs sm:text-sm text-indigo-600 font-medium group-hover:text-indigo-700 transition-colors">Zonas</div>
            </a>
            
            <a href="{{ route('admin.configuracion.categorias') }}" 
               class="bg-gradient-to-br from-pink-50 to-pink-100 border-2 border-pink-200 p-3 sm:p-4 rounded-xl hover:border-pink-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-pink-700 mb-1 group-hover:text-pink-600 transition-colors">{{ $totalCategorias }}</div>
                <div class="text-xs sm:text-sm text-pink-600 font-medium group-hover:text-pink-700 transition-colors">Categorías</div>
            </a>
            
            <a href="{{ route('admin.configuracion.medidas') }}" 
               class="bg-gradient-to-br from-teal-50 to-teal-100 border-2 border-teal-200 p-3 sm:p-4 rounded-xl hover:border-teal-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-teal-700 mb-1 group-hover:text-teal-600 transition-colors">{{ $totalMedidas }}</div>
                <div class="text-xs sm:text-sm text-teal-600 font-medium group-hover:text-teal-700 transition-colors">Medidas</div>
            </a>
            
            <!-- <a href="{{ route('admin.configuracion.mercados') }}" 
               class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 p-3 sm:p-4 rounded-xl hover:border-amber-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-amber-700 mb-1 group-hover:text-amber-600 transition-colors">{{ $totalMercados }}</div>
                <div class="text-xs sm:text-sm text-amber-600 font-medium group-hover:text-amber-700 transition-colors">Mercados</div>
            </a> -->
        </div>
    </div>

    <!-- Acciones secundarias simplificadas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 sm:mb-8">
        
        <!-- Ventas semanales -->
        <a href="{{ route('admin.reportes.semanales') }}" 
           class="bg-gradient-to-br from-green-50 to-white border-2 border-green-200 rounded-xl p-4 sm:p-6 hover:border-green-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-left">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-green-200 group-hover:to-green-300 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">💰</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-green-700 mb-2 group-hover:text-green-600 transition-colors">S/ {{ number_format($ventasSemana, 2) }}</h3>
            <p class="text-sm text-green-600 group-hover:text-green-700 transition-colors">Ventas esta semana</p>
        </a>

        <!-- Total agricultores -->
        <a href="{{ route('admin.reportes.semanales') }}" 
           class="bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 rounded-xl p-4 sm:p-6 hover:border-blue-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-up">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">👨‍🌾</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-blue-700 mb-2 group-hover:text-blue-600 transition-colors">{{ $totalAgricultores }}</h3>
            <p class="text-sm text-blue-600 group-hover:text-blue-700 transition-colors">Total agricultores</p>
        </a>

        <!-- Total productos -->
        <a href="{{ route('admin.reportes.semanales') }}" 
           class="bg-gradient-to-br from-yellow-50 to-white border-2 border-yellow-200 rounded-xl p-4 sm:p-6 hover:border-yellow-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-right">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-yellow-200 group-hover:to-yellow-300 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">🥕</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-yellow-700 mb-2 group-hover:text-yellow-600 transition-colors">{{ $totalProductos }}</h3>
            <p class="text-sm text-yellow-600 group-hover:text-yellow-700 transition-colors">Total productos</p>
        </a>
    </div>

    <!-- Guía rápida mejorada con efectos -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-blue-800 mb-4 flex items-center">
            <span class="mr-2 animate-bounce-slow">📋</span> Funciones principales
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('admin.pagos.agricultores') }}" 
                class="flex items-center p-3 bg-white rounded-lg border hover:border-blue-300 hover:shadow-lg transition-all animate-slide-in-left" style="animation-delay: 0.1s;">
                    <span class="text-2xl mr-3 animate-bounce-slow">💳</span>
                    <div>
                        <h4 class="font-semibold text-blue-800">Liquidar pagos</h4>
                        <p class="text-sm text-blue-600">Pagos pendientes a agricultores</p>
                    </div>
                </a>
            
            <a href="{{ route('admin.reportes.semanales') }}" 
               class="flex items-center p-3 bg-white rounded-lg border hover:border-blue-300 hover:shadow-lg transition-all animate-slide-in-right" style="animation-delay: 0.2s;">
                <span class="text-2xl mr-3 animate-bounce-slow">📈</span>
                <div>
                    <h4 class="font-semibold text-blue-800">Reportes detallados</h4>
                    <p class="text-sm text-blue-600">Análisis completo de ventas</p>
                </div>
            </a>
        </div>
    </div>

    @if($pedidosListos > 0)
    <!-- Mensaje de urgencia animado -->
    <div class="mt-4 sm:mt-6 bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-400 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl animate-bounce">📋</span>
            </div>
            <div class="ml-3">
                <h4 class="text-base sm:text-lg font-bold text-orange-800">¡Atención!</h4>
                <p class="text-sm sm:text-base text-orange-700">
                    Tienes <strong>{{ $pedidosListos }}</strong> {{ $pedidosListos == 1 ? 'pedido listo que necesita' : 'pedidos listos que necesitan' }} ser armado.
                    <a href="{{ route('admin.pedidos.index') }}" class="underline font-semibold hover:text-orange-900 animate-pulse">
                        Ver ahora →
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($pedidosUrgentes > 0)
    <!-- Mensaje de pedidos urgentes -->
    <div class="mt-4 sm:mt-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-400 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl animate-bounce">🚨</span>
            </div>
            <div class="ml-3">
                <h4 class="text-base sm:text-lg font-bold text-red-800">¡Pedidos urgentes!</h4>
                <p class="text-sm sm:text-base text-red-700">
                    Hay <strong>{{ $pedidosUrgentes }}</strong> {{ $pedidosUrgentes == 1 ? 'pedido con más' : 'pedidos con más' }} de 1 día sin atender.
                    <a href="{{ route('admin.pedidos.index') }}" class="underline font-semibold hover:text-red-900 animate-pulse">
                        Revisar urgente →
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

@keyframes slideInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
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

@keyframes pulseBorder {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(251, 146, 60, 0.3); 
    }
    50% { 
        box-shadow: 0 0 0 8px rgba(251, 146, 60, 0); 
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

.animate-slide-in-up {
    animation: slideInUp 0.6s ease-out;
}

.animate-bounce-slow {
    animation: bounceSlow 2s infinite;
}

.animate-pulse-border {
    animation: pulseBorder 2s infinite;
}
</style>

@endsection