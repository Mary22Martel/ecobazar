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
                    📅 Mostrando: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Filtro de Semanas -->
    @if(isset($opcionesSemanas))
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" action="{{ request()->url() }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="semana" class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Filtrar por Semana de Feria
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Las ventas van de domingo a viernes, y se entregan el sábado en la feria
                    </p>
                    <select name="semana" id="semana" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($opcionesSemanas as $valor => $label)
                            <option value="{{ $valor }}" {{ request('semana', 0) == $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                        🔍 Filtrar
                    </button>
                </div>
            </form>
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
                    <span class="text-sm text-gray-600 font-normal">
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
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    👁️ Ver
                                </a>
                                
                                @if($pedido->estado === 'listo')
                                <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="estado" value="armado">
                                    <button type="submit" 
                                            onclick="return confirm('¿Marcar pedido como ARMADO?')"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                        ✅ Armar
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

<script>
// Auto-submit form when week selection changes
document.getElementById('semana')?.addEventListener('change', function() {
    this.form.submit();
});
</script>

<style>
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

@endsection