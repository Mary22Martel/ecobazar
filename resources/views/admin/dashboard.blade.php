@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header principal con información de semana de feria -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg animate-fade-in">
        <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-bounce-slow">
                <span class="text-2xl sm:text-3xl">⚙️</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">¡Hola Administrador!</h1>
            <p class="text-blue-100 text-base sm:text-lg">Panel de gestión Punto Verde</p>
            <div class="mt-3 bg-white/10 rounded-lg p-3">
                <p class="text-sm text-blue-100">
                    <strong>Semana de Feria:</strong> {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }} 
                    • <strong>Entrega:</strong> {{ $diaEntrega->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navegación rápida -->
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
                 <a href="{{ route('admin.repartidores.index') }}" 
                   class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all font-semibold text-sm sm:text-base whitespace-nowrap transform hover:scale-105">
                    👥 <span class="ml-1 sm:ml-2">REPARTIDORES</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Acción principal - Pedidos que necesitan atención -->
    @if($pedidosUrgentes > 0)
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('admin.pedidos.listos') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-orange-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-pulse-border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center shadow-inner animate-bounce-slow">
                        <span class="text-2xl sm:text-3xl">🚨</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-orange-800 mb-1">¡PEDIDOS PARA ARMAR!</h2>
                    <div class="flex items-center space-x-2">
                        <div class="bg-orange-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center animate-pulse shadow-lg">
                            <span class="text-sm sm:text-base font-bold">{{ $pedidosUrgentes }}</span>
                        </div>
                        <p class="text-sm sm:text-base text-orange-600 font-semibold">{{ $pedidosUrgentes == 1 ? 'pedido listo para armar' : 'pedidos listos para armar' }}</p>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">¡Necesita ser armado ahora!</p>
                </div>
                <div class="flex-shrink-0 ml-2">
                    <div class="w-3 h-3 bg-orange-500 rounded-full animate-ping"></div>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Resumen de pedidos para esta semana -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2 animate-bounce-slow">📊</span> Resumen de Pedidos - Esta Semana
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4">
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 p-3 sm:p-4 rounded-xl hover:border-blue-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-blue-700 mb-1 group-hover:text-blue-600 transition-colors">{{ $pedidosSemana }}</div>
                <div class="text-xs sm:text-sm text-blue-600 font-medium group-hover:text-blue-700 transition-colors">Total Semana</div>
            </a>
            
            
            <a href="{{ route('admin.pedidos.listos') }}" 
               class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-200 p-3 sm:p-4 rounded-xl hover:border-yellow-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-yellow-700 mb-1 group-hover:text-yellow-600 transition-colors {{ $pedidosListosSemana > 0 ? 'animate-bounce-slow' : '' }}">{{ $pedidosListosSemana }}</div>
                <div class="text-xs sm:text-sm text-yellow-600 font-medium group-hover:text-yellow-700 transition-colors">Listos</div>
                @if($pedidosListosSemana > 0)
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mx-auto mt-1 animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('admin.pedidos.armados') }}" 
               class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 p-3 sm:p-4 rounded-xl hover:border-green-300 hover:shadow-lg transition-all transform hover:scale-110 text-center group">
                <div class="text-2xl sm:text-3xl font-bold text-green-700 mb-1 group-hover:text-green-600 transition-colors">{{ $pedidosArmadosSemana }}</div>
                <div class="text-xs sm:text-sm text-green-600 font-medium group-hover:text-green-700 transition-colors">Armados</div>
            </a>
            
           
        </div>
    </div>

    <!-- Estadísticas de ventas de la semana -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2">💰</span> Ventas - Esta Semana
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
            <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-green-700 mb-1">S/ {{ number_format($ventasSemanaActual, 2) }}</div>
                <div class="text-xs sm:text-sm text-green-600 font-medium">Ventas Completadas</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-blue-700 mb-1">S/ {{ number_format($ventasPotencialesSemana, 2) }}</div>
                <div class="text-xs sm:text-sm text-blue-600 font-medium">Ventas Potenciales</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-purple-700 mb-1">{{ $agricultoresActivosSemana }}</div>
                <div class="text-xs sm:text-sm text-purple-600 font-medium">Agricultores Activos</div>
            </div>
            
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-indigo-700 mb-1">{{ $clientesUnicosSemana }}</div>
                <div class="text-xs sm:text-sm text-indigo-600 font-medium">Clientes Únicos</div>
            </div>
        </div>
        
        <!-- Estadísticas adicionales de la semana -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 border rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-gray-700">{{ number_format($tasaConversionSemana, 1) }}%</div>
                <div class="text-xs text-gray-600">Tasa Conversión</div>
            </div>
            
            <div class="bg-gray-50 border rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-gray-700">S/ {{ number_format($promedioVentaSemana, 2) }}</div>
                <div class="text-xs text-gray-600">Promedio/Pedido</div>
            </div>
            
            <div class="bg-gray-50 border rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-gray-700">{{ $productosVendidosSemana }}</div>
                <div class="text-xs text-gray-600">Productos Vendidos</div>
            </div>
        </div>

        @if($topCategoriaSemana)
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-sm font-semibold text-yellow-800">🏆 Categoría Estrella de la Semana</div>
            <div class="text-lg font-bold text-yellow-700">{{ $topCategoriaSemana->nombre }}</div>
            <div class="text-xs text-yellow-600">{{ $topCategoriaSemana->items_vendidos }} productos vendidos</div>
        </div>
        @endif
    </div>

    <!-- Totales globales del sistema -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2">🌍</span> Totales Globales del Sistema
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border-2 border-emerald-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-emerald-700 mb-1">S/ {{ number_format($totalVentasGlobal, 2) }}</div>
                <div class="text-xs sm:text-sm text-emerald-600 font-medium">Ventas Totales</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-blue-700 mb-1">{{ $totalPedidosGlobal }}</div>
                <div class="text-xs sm:text-sm text-blue-600 font-medium">Total Pedidos</div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-orange-700 mb-1">{{ $totalAgricultores }}</div>
                <div class="text-xs sm:text-sm text-orange-600 font-medium">Agricultores</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 p-3 sm:p-4 rounded-xl text-center">
                <div class="text-2xl sm:text-3xl font-bold text-purple-700 mb-1">{{ $totalClientes }}</div>
                <div class="text-xs sm:text-sm text-purple-600 font-medium">Clientes</div>
            </div>
        </div>
        
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-50 border rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-gray-700">{{ $totalProductos }}</div>
                <div class="text-xs text-gray-600">Productos en Catálogo</div>
            </div>
            
            <div class="bg-gray-50 border rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-gray-700">S/ {{ number_format($promedioVentaPorPedido, 2) }}</div>
                <div class="text-xs text-gray-600">Promedio Global/Pedido</div>
            </div>
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
            
        </div>
    </div>

    <!-- Alertas y notificaciones importantes -->
    @if($productosStockBajo > 0 || $productosSinStock > 0)
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
            <span class="mr-2">⚠️</span> Alertas de Inventario
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($productosSinStock > 0)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🚫</span>
                    <div>
                        <h4 class="font-semibold text-red-800">Sin Stock</h4>
                        <p class="text-sm text-red-700">{{ $productosSinStock }} productos sin inventario</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($productosStockBajo > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">⚠️</span>
                    <div>
                        <h4 class="font-semibold text-yellow-800">Stock Bajo</h4>
                        <p class="text-sm text-yellow-700">{{ $productosStockBajo }} productos con menos de 5 unidades</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Acciones rápidas principales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 sm:mb-8">
        
        <!-- Liquidar pagos -->
        <a href="{{ route('admin.pagos.agricultores') }}" 
           class="bg-gradient-to-br from-green-50 to-white border-2 border-green-200 rounded-xl p-4 sm:p-6 hover:border-green-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-left">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-green-200 group-hover:to-green-300 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">💰</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-green-700 mb-2 group-hover:text-green-600 transition-colors">Liquidar Pagos</h3>
            <p class="text-sm text-green-600 group-hover:text-green-700 transition-colors">Pagos pendientes a agricultores</p>
        </a>

        <!-- Reportes detallados -->
        <a href="{{ route('admin.reportes.semanales') }}" 
           class="bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 rounded-xl p-4 sm:p-6 hover:border-blue-300 hover:shadow-xl transition-all transform hover:scale-105 text-center group animate-slide-in-up">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                <span class="text-2xl sm:text-3xl group-hover:animate-bounce">📈</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-blue-700 mb-2 group-hover:text-blue-600 transition-colors">Reportes Detallados</h3>
            <p class="text-sm text-blue-600 group-hover:text-blue-700 transition-colors">Análisis completo de ventas</p>
        </a>

        
    </div>

    <!-- Guía rápida mejorada -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-blue-800 mb-4 flex items-center">
            <span class="mr-2 animate-bounce-slow">💡</span> Flujo de Trabajo Semanal
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg border p-3">
                <h4 class="font-semibold text-blue-800 mb-2">📅 Domingo - Viernes</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Los clientes realizan pedidos</li>
                    <li>• Agricultores preparan productos</li>
                    <li>• Admin arma pedidos listos</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg border p-3">
                <h4 class="font-semibold text-blue-800 mb-2">🚚 Sábado</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Día de entrega en la feria</li>
                    <li>• Liquidación de pagos</li>
                    <li>• Reportes semanales</li>
                </ul>
            </div>
        </div>
    </div>

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