@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📦 Gestión de Pedidos</h1>
                <p class="text-blue-100 text-base sm:text-lg">Administración de órdenes del sistema</p>
                @if(isset($inicioSemana) && isset($finSemana))
                <p class="text-blue-100 text-sm mt-2">
                    📅 Semana: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            
        </div>
    </div>
    <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
                ← Volver a inicio
    </a>

    <!-- Filtro de Semanas - Responsive mejorado -->
    @if(isset($opcionesSemanas))
    <div class="mb-6 mt-3">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 filtro-container">
            <form method="GET" action="{{ request()->url() }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                
                <!-- Label y select en móvil -->
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class=" text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtrar por Semana de Feria</span>
                        <span class="sm:hidden truncate">Semana de Feria</span>
                    </label>
                    <p class="text-xs text-gray-500 hidden sm:block">
                        Las ventas van de domingo a viernes, y se entregan el sábado en la feria
                    </p>
                    
                    <!-- Select mejorado para móvil -->
                    <div class="relative">
                        <select name="semana" id="semana" 
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white shadow-sm overflow-hidden text-ellipsis">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ request('semana', 0) == $valor ? 'selected' : '' }}>
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
                            class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sm:hidden">Filtrar</span>
                        <span class="hidden sm:inline">Filtrar</span>
                    </button>
                </div>
            </form>
            
            <!-- Indicador de semana actual en móvil -->
            <div class="mt-3 sm:hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                    <div class="flex items-center text-xs text-blue-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">Mostrando:</span>
                        <span class="ml-1 truncate">{{ $opcionesSemanas[request('semana', 0)] ?? 'Esta semana' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Estadísticas rápidas de la semana -->
    @if(isset($estadisticasSemana))
    <div class="mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $estadisticasSemana['total'] }}</div>
            <div class="text-sm text-blue-700">Total Pedidos</div>
            <div class="text-xs text-gray-500">Esta semana</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $estadisticasSemana['pendientes'] }}</div>
            <div class="text-sm text-orange-700">Pendientes</div>
            <div class="text-xs text-gray-500">Por pagar</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $estadisticasSemana['listos'] }}</div>
            <div class="text-sm text-green-700">Listos</div>
            <div class="text-xs text-gray-500">Por armar</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">S/ {{ number_format($estadisticasSemana['ventas'], 2) }}</div>
            <div class="text-sm text-purple-700">Ventas</div>
            <div class="text-xs text-gray-500">Completadas</div>
        </div>
    </div>
    @endif

    <!-- Filtros rápidos -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <div class="flex space-x-2 min-w-max">
                @php
                    $currentParams = request()->query();
                @endphp
                
                <a href="{{ route('admin.pedidos.index', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ !request()->route()->named('admin.pedidos.pagados') && !request()->route()->named('admin.pedidos.listos') && !request()->route()->named('admin.pedidos.armados') && !request()->route()->named('admin.pedidos.expirados') ? 'bg-blue-100 text-blue-800 border-2 border-blue-200' : 'text-gray-700 hover:bg-gray-50' }} transition-all font-semibold text-sm whitespace-nowrap">
                    📦 Todos los Pedidos
                </a>
                <a href="{{ route('admin.pedidos.pagados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->route()->named('admin.pedidos.pagados') ? 'bg-orange-100 text-orange-800 border-2 border-orange-200' : 'text-gray-700 hover:bg-gray-50' }} transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados 
                </a>
                <a href="{{ route('admin.pedidos.listos', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->route()->named('admin.pedidos.listos') ? 'bg-green-100 text-green-800 border-2 border-green-200' : 'text-gray-700 hover:bg-gray-50' }} transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos (Para Armar)
                </a>
                <a href="{{ route('admin.pedidos.armados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->route()->named('admin.pedidos.armados') ? 'bg-purple-100 text-purple-800 border-2 border-purple-200' : 'text-gray-700 hover:bg-gray-50' }} transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados (Para Entregar)
                </a>
                <a href="{{ route('admin.pedidos.expirados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->route()->named('admin.pedidos.expirados') ? 'bg-red-100 text-red-800 border-2 border-red-200' : 'text-gray-700 hover:bg-gray-50' }} transition-all font-semibold text-sm whitespace-nowrap">
                    ⏰ Expirados
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de pedidos -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">
                Pedidos Registrados 
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
                @endif
                @if(isset($inicioSemana) && isset($finSemana))
                   <br> <span class="text-sm text-gray-600 font-normal">
                        - Semana {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }}
                    </span>
                @endif
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Entrega</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Productos</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-blue-600">#{{ $pedido->id }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                                <div class="text-sm text-gray-500">{{ $pedido->telefono }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $estadoConfig = [
                                    'pendiente' => ['texto' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-800'],
                                    'pagado' => ['texto' => 'Pagado', 'color' => 'bg-orange-100 text-orange-800'],
                                    'listo' => ['texto' => 'Listo', 'color' => 'bg-green-100 text-green-800'],
                                    'armado' => ['texto' => 'Armado', 'color' => 'bg-blue-100 text-blue-800'],
                                    'en_entrega' => ['texto' => 'En Entrega', 'color' => 'bg-purple-100 text-purple-800'],
                                    'entregado' => ['texto' => 'Entregado', 'color' => 'bg-emerald-100 text-emerald-800'],
                                    'cancelado' => ['texto' => 'Cancelado', 'color' => 'bg-red-100 text-red-800'],
                                    'expirado' => ['texto' => 'Expirado', 'color' => 'bg-red-200 text-red-900']
                                ];
                                $config = $estadoConfig[$pedido->estado] ?? ['texto' => ucfirst($pedido->estado), 'color' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['color'] }}">
                                {{ $config['texto'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @if($pedido->delivery === 'delivery')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium">
                                    🚚 Delivery
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $pedido->distrito }}</div>
                            @else
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium">
                                    🏪 Puesto
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
                                {{ $pedido->items->count() }} items
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-bold text-green-600">
                                S/ {{ number_format($pedido->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $pedido->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $pedido->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Ver
                                </a>
                                
                                @if($pedido->estado === 'listo')
                                <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="estado" value="armado">
                                    <button type="submit" 
                                            onclick="return confirm('¿Marcar pedido como ARMADO?')"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Armar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">📦</div>
                            <h3 class="text-lg font-semibold mb-2">No hay pedidos</h3>
                            @if(isset($inicioSemana) && isset($finSemana))
                                <p class="text-sm">No hay pedidos en la semana del {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}</p>
                            @else
                                <p class="text-sm">Los pedidos aparecerán aquí cuando los clientes hagan compras</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($pedidos) && method_exists($pedidos, 'hasPages') && $pedidos->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $pedidos->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Acciones rápidas -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.reportes.semanales', isset($currentParams) ? $currentParams : []) }}" 
           class="bg-green-50 border-2 border-green-200 rounded-xl p-4 hover:border-green-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">💰</span>
            <h4 class="font-semibold text-green-800">Pagos Agricultores</h4>
            <p class="text-sm text-green-600">Liquidar pagos pendientes</p>
        </a>
        
        <a href="{{ route('admin.reportes.semanales', isset($currentParams) ? $currentParams : []) }}" 
           class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">📈</span>
            <h4 class="font-semibold text-blue-800">Reportes</h4>
            <p class="text-sm text-blue-600">Análisis detallado</p>
        </a>
        
        <a href="{{ route('admin.configuracion.zonas') }}" 
           class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">⚙️</span>
            <h4 class="font-semibold text-gray-800">Configuración</h4>
            <p class="text-sm text-gray-600">Zonas, categorías, medidas</p>
        </a>
    </div>

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
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Hover states para desktop */
@media (min-width: 641px) {
    select:hover {
        border-color: #3b82f6;
    }
}

@keyframes bounce {
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

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

<script>
// Auto-submit mejorado con indicador de carga
document.getElementById('semana')?.addEventListener('change', function() {
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