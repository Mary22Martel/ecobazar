@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-3xl font-bold mb-2">🏪 Pedidos para Recojo en Puesto</h1>
                <p class="text-green-100 text-base sm:text-lg">Pedidos armados esperando en la feria</p>
                @if(isset($inicioSemana) && isset($finSemana))
                <p class="text-green-100 text-sm mt-2">
                    📅 Semana: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-green-100 text-sm">listos</div>
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
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtrar por Semana de Feria</span>
                        <span class="sm:hidden truncate">Semana de Feria</span>
                    </label>
                    
                    <div class="relative">
                        <select name="semana" id="semana" 
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm bg-white shadow-sm">
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
                            class="w-full sm:w-auto bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
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
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    🚚 Delivery
                </a>
                <a href="{{ route('admin.pedidos.recojo-puesto', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-green-100 text-green-800 border-2 border-green-200 transition-all font-semibold text-sm whitespace-nowrap">
                    🏪 Recojo Puesto ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de pedidos recojo en puesto -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-bold text-green-800">
                Pedidos Armados para Recojo en Puesto
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
                @endif
            </h3>
            <p class="text-sm text-green-600 mt-1">Listos para ser recogidos en la feria sabatina</p>
        </div>
        
        <!-- Vista de cards -->
        <div class="p-4">
            @forelse($pedidos as $pedido)
            <div class="mb-4 bg-white border rounded-xl shadow-sm border-l-4 border-l-green-400 overflow-hidden">
                <div class="p-4">
                    <!-- Header del card -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-lg font-bold text-green-600">#{{ $pedido->id }}</span>
                            <span class="ml-2 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                🏪 Listo para Recoger
                            </span>
                        </div>
                        <span class="text-xl font-bold text-green-600">
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

                    <!-- Lugar de entrega -->
                    <div class="mb-3">
                        <div class="text-sm text-gray-800 font-medium">
                            📍 Feria sabatina - Punto Verde
                        </div>
                        <div class="text-xs text-gray-600">
                            Armado el {{ $pedido->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="mb-4">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
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
                           class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg text-sm transition-colors text-center font-medium flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Ver Detalle
                        </a>
                        
                        <!-- Botón informativo -->
                        <div class="w-full bg-blue-50 text-blue-700 px-4 py-3 rounded-lg text-sm text-center font-medium flex items-center justify-center border border-blue-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Esperando cliente en feria
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <div class="text-6xl mb-4">🏪</div>
                <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos para recojo en puesto</h3>
                <p class="text-sm text-gray-500">Los pedidos armados para recoger en la feria aparecerán aquí</p>
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

    <!-- Información importante -->
    @if($pedidos->count() > 0)
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">📋 Información sobre Pedidos de Puesto</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <p>• Los pedidos de puesto permanecen en estado <strong>"Armado"</strong></p>
                    <p>• Los clientes los recogen directamente en la feria sabatina</p>
                    <p>• Se organizan por fecha de creación del pedido</p>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
document.getElementById('semana')?.addEventListener('change', function() {
    this.form.submit();
});
</script>

@endsection