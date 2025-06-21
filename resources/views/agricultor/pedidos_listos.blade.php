@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    <!-- Header responsive mejorado -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2">✅ PEDIDOS LISTOS</h1>
                <p class="text-green-100 text-sm sm:text-lg">
                    📅 {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }} 
                    (Entrega: {{ $diaEntrega->format('d/m/Y') }})
                </p>
            </div>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="bg-white/20 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white/30 transition-all text-sm sm:text-base text-center">
                ← Volver al inicio
            </a>
        </div>
    </div>

    <!-- FILTRO DE SEMANAS - Responsive mejorado -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 filtro-container">
            <form method="GET" action="{{ route('agricultor.pedidos_listos') }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                
                <!-- Label y select en móvil -->
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class="text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtrar por Semana de Feria</span>
                        <span class="sm:hidden truncate">Semana de Feria</span>
                    </label>
                    
                    <!-- Select mejorado para móvil -->
                    <div class="relative">
                        <select name="semana" id="semana" 
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm bg-white shadow-sm overflow-hidden text-ellipsis">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ $semanaSeleccionada === $valor ? 'selected' : '' }}>
                                    @if(strlen($label) > 25)
                                        {{ substr($label, 0, 22) }}...
                                    @else
                                        {{ $label }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <!-- Icono de dropdown personalizado -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Botón responsive -->
                <div class="sm:flex-shrink-0">
                    <button type="submit" 
                            class="w-full sm:w-auto bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sm:hidden">Filtrar Semana</span>
                        <span class="hidden sm:inline">Ver Semana</span>
                    </button>
                </div>
            </form>
            
            <!-- Indicador de semana actual en móvil -->
            <div class="mt-3 sm:hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-2">
                    <div class="flex items-center text-xs text-green-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">Mostrando:</span>
                        <span class="ml-1 truncate">{{ $opcionesSemanas[$semanaSeleccionada] ?? 'Esta semana' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pedidos->whereIn('estado', ['listo', 'armado', 'entregado'])->isEmpty())
        <!-- Estado vacío responsive -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-6 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="text-4xl sm:text-6xl mb-3 sm:mb-4">📋</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">No tienes pedidos listos esta semana</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-base sm:text-lg">
                    Del {{ $fechaInicio->format('d/m') }} al {{ $fechaFin->format('d/m') }} no marcaste pedidos como listos
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('agricultor.pedidos_pendientes', ['semana' => $semanaSeleccionada]) }}" 
                       class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-orange-600 hover:to-orange-700 transform hover:scale-105 transition-all shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Ver Pendientes
                    </a>
                    <a href="{{ route('agricultor.dashboard') }}" 
                       class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Inicio
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Lista de pedidos responsive -->
        <div class="space-y-4 sm:space-y-6">
            @foreach($pedidos->whereIn('estado', ['listo', 'armado', 'entregado']) as $pedido)
                @php
                    $misProductos = $pedido->items->where('product.user_id', Auth::id());
                    $valorTotal = $misProductos->sum(function($item) {
                        return $item->cantidad * $item->precio;
                    });
                @endphp
                
                @if($misProductos->count() > 0)
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
    <!-- Header compacto -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-full p-2 flex-shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">PEDIDO #{{ $pedido->id }}</h2>
                    <p class="text-green-100 text-sm">{{ $pedido->nombre }} {{ $pedido->apellido }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xl font-bold">S/ {{ number_format($valorTotal, 2) }}</div>
                <div class="text-green-100 text-xs">Tu ganancia</div>
            </div>
        </div>
    </div>

    <!-- Contenido con productos -->
    <div class="p-4">
        <!-- Resumen simple de productos -->
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">
                        {{ $misProductos->count() }} {{ $misProductos->count() === 1 ? 'producto' : 'productos' }} preparados
                    </span>
                </div>
                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-medium">
                    ✅ Listo
                </span>
            </div>
            
            <!-- Lista de productos -->
            <div class="space-y-2">
                @foreach($misProductos as $item)
                <div class="flex items-center justify-between bg-white rounded-md p-2 border border-gray-100">
                    <div class="flex items-center space-x-2 flex-1 min-w-0">
                        <div class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></div>
                        <span class="text-sm font-medium text-gray-800 truncate">{{ $item->product->nombre }}</span>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="text-sm font-bold text-green-600">{{ $item->cantidad }}</span>
                        <span class="text-xs text-gray-500 ml-1">{{ $item->product->medida->nombre ?? 'und' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Solo el botón de ver detalles -->
        <a href="{{ route('agricultor.pedido_detalle', $pedido->id) }}"
         class="w-full bg-gradient-to-r from-gray-600 to-gray-700 text-white px-4 py-3 rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all text-center font-medium text-sm flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Ver detalles completos
        </a>
    </div>
</div>
                @endif
            @endforeach
        </div>
    @endif

</div>

<style>
/* Mejoras específicas para el select en móvil */
@media (max-width: 640px) {
    select {
        font-size: 16px; /* Evita zoom en iOS */
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    /* Mejora del botón en móvil */
    .filter-button {
        min-height: 44px; /* Área de toque recomendada */
    }
    
    /* Contenedor del filtro más estrecho en móvil */
    .filtro-container {
        overflow: hidden;
    }
    
    /* Opciones del select más cortas */
    select option {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}

/* Animación suave para el cambio de semana */
.week-transition {
    transition: all 0.3s ease-in-out;
}

/* Estado focus mejorado */
select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Hover states para desktop */
@media (min-width: 641px) {
    select:hover {
        border-color: #22c55e;
    }
}
</style>

<script>
// Auto-submit mejorado con indicador de carga
document.getElementById('semana').addEventListener('change', function() {
    const button = this.form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    // Mostrar estado de carga
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="sm:hidden">Cargando...</span>
        <span class="hidden sm:inline">Filtrando...</span>
    `;
    
    button.disabled = true;
    
    // Submit el formulario
    this.form.submit();
});

// Restaurar estado si hay error
window.addEventListener('pageshow', function() {
    const button = document.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = false;
    }
});
</script>

@endsection