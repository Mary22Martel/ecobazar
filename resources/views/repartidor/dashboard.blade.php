@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header principal con información de semana de feria -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg animate-fade-in">
        <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-bounce-slow">
                <span class="text-2xl sm:text-3xl">🚛</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">¡Hola {{ Auth::user()->name }}!</h1>
            <p class="text-purple-100 text-base sm:text-lg">Panel de entregas y rutas</p>
            <div class="mt-3 bg-white/10 rounded-lg p-3">
                <p class="text-sm text-purple-100">
                    <strong>Semana de entregas:</strong> {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }} 
                    • <strong>Entrega:</strong> {{ $diaEntrega->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN DE ZONAS ASIGNADAS -->
    @if($zonasAsignadas->isNotEmpty())
    <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-4 sm:p-6 shadow-sm animate-fade-in-up">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-indigo-800 mb-2">Zonas de Entrega Asignadas</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($zonasAsignadas), 3) }} gap-3">
                    @foreach($zonasAsignadas as $zona)
                    <div class="bg-white rounded-lg p-3 border-l-4 border-indigo-400 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $zona->name }}</h4>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activa
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- RECORDATORIO SEMANAL -->
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 rounded-lg p-4 shadow-sm animate-fade-in-up">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center animate-bounce-slow">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-blue-800 mb-1 flex items-center">
                    🚛 <span class="ml-1">Información de Entregas</span>
                </h3>
                <p class="text-sm text-blue-700 leading-relaxed mb-2">
                    <strong>¡Revisa tus entregas regularmente!</strong> Los pedidos aparecen aquí cuando el administrador los ha armado y están listos para ser entregados. Tu día principal de trabajo es el sábado.
                </p>
                <div class="mt-2 text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-md inline-block">
                    📅 Día de entregas: {{ $diaEntrega->format('l d/m/Y') }}
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="flex-shrink-0 text-blue-400 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- FLUJO PRINCIPAL 1-2 (Simplificado) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6 mb-6 sm:mb-8">

        <!-- PASO 1: Entregas pendientes -->
        <a href="{{ route('repartidor.pedidos_pendientes') }}"
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-right relative {{ $entregasPendientes > 0 ? 'border-orange-400 ring-2 ring-orange-100 animate-pulse-border' : 'border-gray-300' }}">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 {{ $entregasPendientes > 0 ? 'bg-gradient-to-br from-orange-500 to-orange-600 animate-pulse' : 'bg-gradient-to-br from-gray-500 to-gray-600' }} text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                1
            </div>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center shadow-inner {{ $entregasPendientes > 0 ? 'animate-bounce-slow' : '' }}">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">ENTREGAS PENDIENTES</h2>
                    @if($entregasPendientes > 0)
                        <div class="flex items-center space-x-2">
                            <div class="bg-orange-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center animate-pulse shadow-lg">
                                <span class="text-sm sm:text-base font-bold">{{ $entregasPendientes }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-orange-600 font-semibold">{{ $entregasPendientes == 1 ? 'entrega esperando' : 'entregas esperando' }}</p>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">¡Para esta semana!</p>
                        <div class="mt-2 inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-1 animate-pulse"></span>
                            Primer paso - ¡Urgente!
                        </div>
                    @else
                        <p class="text-xs sm:text-sm text-gray-400">No hay entregas pendientes esta semana</p>
                        <div class="mt-2 inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                            Primer paso
                        </div>
                    @endif
                </div>
                @if($entregasPendientes > 0)
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full animate-ping"></div>
                    </div>
                @endif
            </div>
        </a>

        <!-- PASO 2: Entregas completadas -->
        <a href="{{ route('repartidor.entregas_completadas') }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-green-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-left relative">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                2
            </div>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">ENTREGAS COMPLETADAS</h2>
                    <p class="text-sm sm:text-base text-green-600 font-semibold">{{ $entregasCompletadas }} {{ $entregasCompletadas == 1 ? 'entrega completada' : 'entregas completadas' }}</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                        Segundo paso - Historial
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Tarjetas de información rápida -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 sm:mb-8">
        <!-- Entregas de esta semana -->
        <div class="bg-white rounded-xl shadow-lg p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl sm:text-3xl font-bold text-blue-700 mb-1">{{ $entregasSemana }}</div>
            <div class="text-xs sm:text-sm text-blue-600 font-medium">Entregas Semana</div>
        </div>
        
        <!-- Pendientes -->
        <div class="bg-white rounded-xl shadow-lg p-4 text-center border-l-4 border-orange-400">
            <div class="text-2xl sm:text-3xl font-bold text-orange-700 mb-1 {{ $entregasPendientes > 0 ? 'animate-bounce-slow' : '' }}">{{ $entregasPendientes }}</div>
            <div class="text-xs sm:text-sm text-orange-600 font-medium">Pendientes</div>
            @if($entregasPendientes > 0)
                <div class="w-2 h-2 bg-orange-500 rounded-full mx-auto mt-1 animate-pulse"></div>
            @endif
        </div>
        
        <!-- Completadas -->
        <div class="bg-white rounded-xl shadow-lg p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl sm:text-3xl font-bold text-green-700 mb-1">{{ $entregasCompletadas }}</div>
            <div class="text-xs sm:text-sm text-green-600 font-medium">Completadas</div>
        </div>
        
        <!-- Total histórico -->
        <div class="bg-white rounded-xl shadow-lg p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl sm:text-3xl font-bold text-purple-700 mb-1">{{ $totalEntregas }}</div>
            <div class="text-xs sm:text-sm text-purple-600 font-medium">Total Histórico</div>
        </div>
    </div>

    <!-- ESTADÍSTICAS POR ZONA -->
    @if(!empty($estadisticasPorZona) && count($estadisticasPorZona) > 0)
    <div class="mb-6 bg-white rounded-xl shadow-lg p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">📊</span> Estadísticas por Zona - Esta Semana
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($estadisticasPorZona), 3) }} gap-4">
            @foreach($estadisticasPorZona as $estadistica)
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-800 flex items-center">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        {{ $estadistica['zona']->name }}
                    </h4>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pendientes:</span>
                        <span class="font-semibold text-orange-600">{{ $estadistica['pendientes'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completadas:</span>
                        <span class="font-semibold text-green-600">{{ $estadistica['completadas'] }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Total:</span>
                        <span class="font-bold text-blue-600">{{ $estadistica['total'] }}</span>
                    </div>
                </div>
                
                @if($estadistica['total'] > 0)
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $porcentaje = round(($estadistica['completadas'] / $estadistica['total']) * 100);
                        @endphp
                        <div class="bg-green-500 h-2 rounded-full progress-bar" data-width="{{ $porcentaje }}"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-center">
                        {{ $estadistica['total'] > 0 ? round(($estadistica['completadas'] / $estadistica['total']) * 100, 1) : 0 }}% completado
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Guía rápida mejorada con efectos -->
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-purple-800 mb-4 flex items-center">
            <span class="mr-2 animate-bounce-slow">💡</span> ¿Cómo funciona tu trabajo como repartidor?
        </h3>
        
        <div class="space-y-3">
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">1</div>
                <div>
                    <p class="text-sm sm:text-base text-purple-800"><strong>Domingo a Viernes</strong></p>
                    <p class="text-xs sm:text-sm text-purple-600">Los clientes hacen pedidos y los agricultores los preparan. El administrador arma los pedidos completos.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.3s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">2</div>
                <div>
                    <p class="text-sm sm:text-base text-purple-800"><strong>Sábado {{ $diaEntrega->format('d/m') }} - Día de entregas</strong></p>
                    <p class="text-xs sm:text-sm text-purple-600">Realiza todas las entregas de la semana y marca como completadas en el sistema.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.4s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">3</div>
                <div>
                    <p class="text-sm sm:text-base text-purple-800"><strong>Actualiza el estado</strong></p>
                    <p class="text-xs sm:text-sm text-purple-600">Confirma cada entrega para que clientes y administrador sepan que fue completada.</p>
                </div>
            </div>
        </div>
        
        <!-- Información importante sobre la semana actual -->
        <div class="mt-4 bg-white border-l-4 border-purple-400 rounded-lg p-3">
            <h4 class="font-semibold text-purple-800 mb-1">🎯 Esta Semana</h4>
            <p class="text-sm text-purple-700">
                Semana de ventas del <strong>{{ $inicioSemana->format('d/m') }}</strong> al <strong>{{ $finSemana->format('d/m') }}</strong>. 
                @if($entregasPendientes > 0)
                    Tienes <strong>{{ $entregasPendientes }}</strong> entregas para el <strong>{{ $diaEntrega->format('d/m/Y') }}</strong>
                    @if($zonasAsignadas->isNotEmpty())
                        en {{ $zonasAsignadas->count() == 1 ? 'la zona' : 'las zonas' }}: <strong>{{ $zonasAsignadas->pluck('name')->join(', ') }}</strong>.
                    @endif
                @else
                    ¡Perfecto! No tienes entregas pendientes para esta semana.
                @endif
            </p>
        </div>
    </div>

    @if($entregasPendientes > 0)
    <!-- Mensaje de urgencia animado -->
    <div class="mt-4 sm:mt-6 bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-400 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl animate-bounce">🚛</span>
            </div>
            <div class="ml-3">
                <h4 class="text-base sm:text-lg font-bold text-orange-800">¡Tienes entregas programadas!</h4>
                <p class="text-sm sm:text-base text-orange-700">
                    {{ $entregasPendientes }} {{ $entregasPendientes == 1 ? 'entrega programada' : 'entregas programadas' }} para el <strong>{{ $diaEntrega->format('d/m/Y') }}</strong>
                    @if($zonasAsignadas->isNotEmpty())
                        en {{ $zonasAsignadas->count() == 1 ? 'tu zona asignada' : 'tus zonas asignadas' }}.
                    @endif
                    <a href="{{ route('repartidor.pedidos_pendientes') }}" class="underline font-semibold hover:text-orange-900 animate-pulse">
                        Ver detalles →
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
@keyframes pulse-border {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(251, 146, 60, 0.4);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(251, 146, 60, 0);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes slide-in-right {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-pulse-border {
    animation: pulse-border 2s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}

.animate-slide-in-right {
    animation: slide-in-right 0.6s ease-out;
}

.animate-slide-in-left {
    animation: slide-in-left 0.6s ease-out;
}

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

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

/* Efectos hover mejorados */
.block:hover .absolute {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Responsive para los números */
@media (max-width: 640px) {
    .absolute.-top-3.-left-3 {
        top: -0.5rem;
        left: -0.5rem;
    }
}

/* Progress bar dinámico */
.progress-bar {
    transition: width 0.5s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar las barras de progreso
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(function(bar) {
        const width = bar.getAttribute('data-width');
        setTimeout(function() {
            bar.style.width = width + '%';
        }, 100);
    });
});
</script>

@endsection