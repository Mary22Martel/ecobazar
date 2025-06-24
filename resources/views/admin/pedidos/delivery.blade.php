@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-3xl font-bold mb-2">🚚 Pedidos Delivery</h1>
                <p class="text-yellow-100 text-base sm:text-lg">Pedidos en entrega y entregados por delivery</p>
                @if(isset($inicioSemana) && isset($finSemana))
                <p class="text-yellow-100 text-sm mt-2">
                    📅 Semana: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-yellow-100 text-sm">delivery</div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('admin.pedidos.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
        ← Todos los Pedidos
    </a>

    <!-- Filtro de Semanas -->
    @if(isset($opcionesSemanas))
    <div class="mb-6 mt-3">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4">
            <form method="GET" action="{{ request()->url() }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class="text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtrar por Semana de Feria</span>
                        <span class="sm:hidden truncate">Semana de Feria</span>
                    </label>
                    
                    <div class="relative">
                        <select name="semana" id="semana" 
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-sm bg-white shadow-sm">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ request('semana', 0) == $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="sm:flex-shrink-0">
                    <button type="submit" 
                            class="w-full sm:w-auto bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrar
                    </button>
                </div>
            </form>
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
                <a href="{{ route('admin.pedidos.armados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados
                </a>
                <a href="{{ route('admin.pedidos.delivery', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 border-2 border-yellow-200 transition-all font-semibold text-sm whitespace-nowrap">
                    🚚 Delivery ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
                <a href="{{ route('admin.pedidos.recojo-puesto', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    🏪 Recojo Puesto
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de pedidos delivery -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-lg font-bold text-yellow-800">
                Pedidos de Delivery
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
                @endif
            </h3>
            <p class="text-sm text-yellow-600 mt-1">En entrega y entregados por delivery</p>
        </div>
        
        <!-- Vista de cards en móvil y desktop -->
        <div class="p-4">
            @forelse($pedidos as $pedido)
            <div class="mb-4 bg-white border rounded-xl shadow-sm border-l-4 border-l-yellow-400 overflow-hidden">
                <div class="p-4">
                    <!-- Header del card -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-lg font-bold text-yellow-600">#{{ $pedido->id }}</span>
                            <span class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                🚚 {{ ucfirst($pedido->estado) }}
                            </span>
                        </div>
                        <span class="text-xl font-bold text-yellow-600">
                            S/ {{ number_format($pedido->total, 2) }}
                        </span>
                    </div>

                    <!-- Información del cliente -->
                    <div class="mb-3">
                        <div class="font-semibold text-gray-900">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                        <a href="tel:{{ $pedido->telefono }}" class="text-blue-600 text-sm">
                            📞 {{ $pedido->telefono }}
                        </a>
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <div class="text-sm text-gray-800 font-medium">
                            📍 {{ $pedido->direccion }}
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ $pedido->distrito }}
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="mb-4">
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm font-medium">
                            {{ $pedido->items->count() }} productos
                        </span>
                        <div class="text-sm text-gray-600 mt-1">
                            @php
                                $productos = $pedido->items->take(2)->pluck('product.nombre')->implode(', ');
                                if($pedido->items->count() > 2) {
                                    $productos .= ' +' . ($pedido->items->count() - 2) . ' más';
                                }
                            @endphp
                            {{ $productos }}
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                           class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg text-sm transition-colors text-center font-medium flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Ver Detalle
                        </a>
                        
                        @if($pedido->estado === 'en_entrega')
                        <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}">
                            @csrf
                            <input type="hidden" name="estado" value="entregado">
                            <button type="submit" 
                                    onclick="return confirm('¿Confirmar entrega del pedido #{{ $pedido->id }}?')"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg text-sm transition-colors font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar Entregado
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <div class="text-6xl mb-4">🚚</div>
                <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos de delivery</h3>
                <p class="text-sm text-gray-500">Los pedidos de delivery aparecerán aquí cuando estén en camino o entregados</p>
            </div>
            @endforelse
        </div>
        
        <!-- Paginación -->
        @if(isset($pedidos) && method_exists($pedidos, 'hasPages') && $pedidos->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $pedidos->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

<script>
// Auto-submit del filtro
document.getElementById('semana')?.addEventListener('change', function() {
    this.form.submit();
});
</script>

@endsection