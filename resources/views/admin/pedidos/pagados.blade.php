@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-3xl font-bold mb-2">💳 Pedidos Pagados</h1>
                <p class="text-orange-100 text-base sm:text-lg">Pedidos que necesitan ser armados por el agricultor</p>
                @if(isset($inicioSemana) && isset($finSemana))
                <p class="text-orange-100 text-sm mt-2">
                    📅 Semana: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-orange-100 text-sm">por armar</div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('admin.pedidos.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
        ← Todos los pedidos
    </a>

    <!-- Filtro de Semanas - Responsive mejorado -->
    @if(isset($opcionesSemanas))
    <div class="mb-6 mt-3">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 filtro-container">
            <form method="GET" action="{{ request()->url() }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                
                <!-- Label y select en móvil -->
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class=" text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm bg-white shadow-sm overflow-hidden text-ellipsis">
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
                            class="w-full sm:w-auto bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
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
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-2">
                    <div class="flex items-center text-xs text-orange-700">
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

    <!-- Navegación entre estados -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <div class="flex space-x-2 min-w-max">
                @php
                    $currentParams = request()->query();
                @endphp
                
                <a href="{{ route('admin.pedidos.index', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📦 Todos
                </a>
                <a href="{{ route('admin.pedidos.pagados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-orange-100 text-orange-800 border-2 border-orange-200 transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
                <a href="{{ route('admin.pedidos.listos', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos
                </a>
                <a href="{{ route('admin.pedidos.armados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados
                </a>
                <a href="{{ route('admin.pedidos.expirados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ⏰ Expirados
                </a>
            </div>
        </div>
    </div>

    @if($pedidos->count() > 0)
    <!-- Alerta de instrucciones -->
    <div class="bg-orange-50 border-l-4 border-orange-400 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <span class="text-2xl mr-3">⚠️</span>
            <div>
                <h4 class="text-lg font-bold text-orange-800">¡Atención Administrador!</h4>
                <p class="text-orange-700">
                    Estos pedidos ya están <strong>pagados</strong> y esperan que los agricultores preparen los productos. 
                    Una vez que los agricultores marquen como "Listo", podrás armarlos.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de pedidos pagados -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-orange-50">
            <h3 class="text-lg font-bold text-orange-800">
                Pedidos Pagados 
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
                @endif
                @if(isset($inicioSemana) && isset($finSemana))
                   <br> <span class="text-sm text-orange-600 font-normal">
                        - Semana {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }}
                    </span>
                @endif
            </h3>
            <h2 class="text-sm text-orange-600 mt-1">Pedidos que están siendo armados por los agricultores</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Pedido</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Agricultores</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Productos</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Entrega</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    @php
                        $agricultoresUnicos = $pedido->items->groupBy('product.user_id')->count();
                        $agricultoresNombres = $pedido->items->pluck('product.user.name')->unique()->filter()->implode(', ');
                    @endphp
                    
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-orange-600">#{{ $pedido->id }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                                <div class="text-sm text-gray-500">
                                    <a href="tel:{{ $pedido->telefono }}" class="hover:text-blue-600">{{ $pedido->telefono }}</a>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $pedido->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $pedido->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $agricultoresUnicos }} {{ $agricultoresUnicos == 1 ? 'agricultor' : 'agricultores' }}
                                </span>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ Str::limit($agricultoresNombres, 30) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-sm font-medium">
                                {{ $pedido->items->count() }} items
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-bold text-green-600">
                                S/ {{ number_format($pedido->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @if($pedido->delivery === 'delivery')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                                    🚚 Delivery
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $pedido->distrito }}</div>
                            @else
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                    🏪 Puesto
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                                   class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    👁️ Ver Detalle
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <h3 class="text-lg font-semibold mb-2">No hay pedidos pagados</h3>
                            @if(isset($inicioSemana) && isset($finSemana))
                                <p class="text-sm">No hay pedidos pagados en la semana del {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}</p>
                            @else
                                <p class="text-sm text-gray-600">No hay pedidos pagados pendientes de preparación</p>
                                <p class="text-xs text-gray-500 mt-2">Los pedidos aparecerán aquí cuando los clientes paguen</p>
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

    <!-- Resumen rápido -->
    @if($pedidos->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Resumen de Pedidos Pagados</h3>
        
        @php
            $totalVentas = $pedidos->sum('total');
            $totalProductos = $pedidos->sum(function($pedido) {
                return $pedido->items->count();
            });
        @endphp
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $pedidos->count() }}</div>
                <div class="text-sm text-orange-700">Pedidos Pagados</div>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $totalProductos }}</div>
                <div class="text-sm text-blue-700">Total Productos</div>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">S/ {{ number_format($totalVentas, 2) }}</div>
                <div class="text-sm text-green-700">Valor Total</div>
            </div>
        </div>
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
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Hover states para desktop */
@media (min-width: 641px) {
    select:hover {
        border-color: #f97316;
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