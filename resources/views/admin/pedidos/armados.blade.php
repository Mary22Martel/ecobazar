@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-3xl font-bold mb-2">📋 Pedidos Armados</h1>
                <p class="text-purple-100 text-base sm:text-lg">Pedidos listos para entrega</p>
                @if(isset($inicioSemana) && isset($finSemana))
                <p class="text-purple-100 text-sm mt-2">
                    📅 Semana: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }} 
                    @if(isset($diaEntrega))
                        • Entrega: {{ $diaEntrega->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-purple-100 text-sm">para entregar</div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('admin.pedidos.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
        ← Todos los Pedidos
    </a>

    <!-- Filtro de Semanas - Responsive mejorado -->
    @if(isset($opcionesSemanas))
    <div class="mb-6 mt-3">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 filtro-container">
            <form method="GET" action="{{ request()->url() }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                
                <!-- Label y select en móvil -->
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class=" text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm bg-white shadow-sm overflow-hidden text-ellipsis">
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
                            class="w-full sm:w-auto bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
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
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-2">
                    <div class="flex items-center text-xs text-purple-700">
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
            <div class="text-2xl font-bold text-purple-600">{{ $pedidos->total() ?? $pedidos->count() }}</div>
            <div class="text-sm text-purple-700">Armados</div>
            <div class="text-xs text-gray-500">Esta semana</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            @php
                $pedidosDelivery = $pedidos->where('delivery', 'delivery')->count();
            @endphp
            <div class="text-2xl font-bold text-yellow-600">{{ $pedidosDelivery }}</div>
            <div class="text-sm text-yellow-700">Delivery</div>
            <div class="text-xs text-gray-500">Para repartir</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            @php
                $pedidosPuesto = $pedidos->where('delivery', 'puesto')->count();
            @endphp
            <div class="text-2xl font-bold text-green-600">{{ $pedidosPuesto }}</div>
            <div class="text-sm text-green-700">Puesto</div>
            <div class="text-xs text-gray-500">Para recoger</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">S/ {{ number_format($pedidos->sum('total'), 2) }}</div>
            <div class="text-sm text-blue-700">Valor</div>
            <div class="text-xs text-gray-500">Por entregar</div>
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
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados
                </a>
                <a href="{{ route('admin.pedidos.listos', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos
                </a>
                <a href="{{ route('admin.pedidos.armados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-purple-100 text-purple-800 border-2 border-purple-200 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
                <a href="{{ route('admin.pedidos.expirados', $currentParams) }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ⏰ Expirados
                </a>
            </div>
        </div>
    </div>

    @if($pedidos->count() > 0)
    <!-- Información de entrega -->
    <div class="bg-purple-50 border-l-4 border-purple-400 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <span class="text-2xl mr-3">🚚</span>
            <div>
                <h4 class="text-lg font-bold text-purple-800">¡Pedidos listos para entregar!</h4>
                <p class="text-purple-700">
                    Estos pedidos están completamente armados y empacados. 
                    <strong>Coordina la entrega</strong> según el tipo (delivery o puesto).
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de pedidos armados -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-purple-50">
            <h3 class="text-lg font-bold text-purple-800">
                Pedidos Armados y Listos
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
                @endif
                @if(isset($inicioSemana) && isset($finSemana))
                   <br> <span class="text-sm text-purple-600 font-normal">
                        - Semana {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }}
                    </span>
                @endif
            </h3>
            <p class="text-sm text-purple-600 mt-1">Esperando ser entregados a los clientes</p>
        </div>
        
        <!-- Vista de tabla en desktop -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tipo Entrega</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Dirección</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Productos</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    @php
                        $horasArmado = $pedido->updated_at->diffInHours(now());
                        $esDelivery = $pedido->delivery === 'delivery';
                    @endphp
                    
                    <tr class="hover:bg-purple-50 transition-colors {{ $esDelivery ? 'bg-yellow-50 border-l-4 border-l-yellow-400' : 'bg-green-50 border-l-4 border-l-green-400' }}">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-purple-600">#{{ $pedido->id }}</span>
                            @if($esDelivery)
                                <div class="text-xs text-yellow-600 font-semibold">🚚 DELIVERY</div>
                            @else
                                <div class="text-xs text-green-600 font-semibold">🏪 PUESTO</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                                <div class="text-sm text-blue-600 hover:text-blue-800">
                                    <a href="tel:{{ $pedido->telefono }}" class="flex items-center">
                                        📞 {{ $pedido->telefono }}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($esDelivery)
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                    🚚 Delivery
                                </span>
                                @php
                                    $zona = \App\Models\Zone::where('name', $pedido->distrito)->first();
                                    $costoEnvio = $zona ? $zona->delivery_cost : 0;
                                @endphp
                                @if($costoEnvio > 0)
                                    <div class="text-xs text-yellow-600 mt-1">
                                        Costo: S/ {{ number_format($costoEnvio, 2) }}
                                    </div>
                                @endif
                            @else
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    🏪 Recoger en Puesto
                                </span>
                                <div class="text-xs text-green-600 mt-1">
                                    Cliente debe recoger
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($esDelivery)
                                <div class="text-sm text-gray-800 font-medium">
                                    {{ $pedido->direccion }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $pedido->distrito }}
                                </div>
                            @else
                                <div class="text-sm text-gray-500 italic">
                                    Feria sabatina
                                </div>
                                <div class="text-xs text-gray-400">
                                    Punto Verde
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="space-y-1">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm font-medium">
                                    {{ $pedido->items->count() }} items
                                </span>
                                <div class="text-xs text-gray-600">
                                    @php
                                        $productos = $pedido->items->take(2)->pluck('product.nombre')->implode(', ');
                                        if($pedido->items->count() > 2) {
                                            $productos .= ' +' . ($pedido->items->count() - 2) . ' más';
                                        }
                                    @endphp
                                    {{ $productos }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-bold text-purple-600">
                                S/ {{ number_format($pedido->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                                   class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm transition-colors text-center">
                                    👁️ Ver
                                </a>
                                
                                @if($esDelivery)
                                <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="estado" value="en_entrega">
                                    <button type="submit" 
                                            onclick="return confirm('¿Marcar como EN ENTREGA el pedido #{{ $pedido->id }}?')"
                                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors font-semibold">
                                        🚚 EN ENTREGA
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="estado" value="entregado">
                                    <button type="submit" 
                                            onclick="return confirm('¿Confirmar que el cliente recogió el pedido #{{ $pedido->id }}?')"
                                            class="w-full bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors font-semibold">
                                        ✅ ENTREGADO
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">📋</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos armados</h3>
                            @if(isset($inicioSemana) && isset($finSemana))
                                <p class="text-sm">No hay pedidos armados en la semana del {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}</p>
                            @else
                                <p class="text-sm text-gray-500">Los pedidos aparecerán aquí cuando los armes desde la sección "Listos"</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vista de cards en móvil y tablet -->
        <div class="lg:hidden">
            @forelse($pedidos as $pedido)
            @php
                $esDelivery = $pedido->delivery === 'delivery';
            @endphp
            
            <div class="mb-4 bg-white border rounded-xl shadow-sm {{ $esDelivery ? 'border-l-4 border-l-yellow-400' : 'border-l-4 border-l-green-400' }} overflow-hidden">
                <div class="p-4">
                    <!-- Header del card -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-lg font-bold text-purple-600">#{{ $pedido->id }}</span>
                            @if($esDelivery)
                                <span class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                    🚚 Delivery
                                </span>
                            @else
                                <span class="ml-2 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                    🏪 Puesto
                                </span>
                            @endif
                        </div>
                        <span class="text-xl font-bold text-purple-600">
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
                        @if($esDelivery)
                            <div class="text-sm text-gray-800 font-medium">
                                📍 {{ $pedido->direccion }}
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $pedido->distrito }}
                                @php
                                    $zona = \App\Models\Zone::where('name', $pedido->distrito)->first();
                                    $costoEnvio = $zona ? $zona->delivery_cost : 0;
                                @endphp
                                @if($costoEnvio > 0)
                                    • Envío: S/ {{ number_format($costoEnvio, 2) }}
                                @endif
                            </div>
                        @else
                            <div class="text-sm text-gray-600">
                                📍 Feria sabatina - Punto Verde
                            </div>
                        @endif
                    </div>

                    <!-- Productos -->
                    <div class="mb-4">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm font-medium">
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
                           class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-3 rounded-lg text-sm transition-colors text-center font-medium flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Ver Detalle
                        </a>
                        
                        @if($esDelivery)
                        <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}">
                            @csrf
                            <input type="hidden" name="estado" value="en_entrega">
                            <button type="submit" 
                                    onclick="return confirm('¿Marcar como EN ENTREGA el pedido #{{ $pedido->id }}?')"
                                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg text-sm transition-colors font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Marcar en Entrega
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}">
                            @csrf
                            <input type="hidden" name="estado" value="entregado">
                            <button type="submit" 
                                    onclick="return confirm('¿Confirmar que el cliente recogió el pedido #{{ $pedido->id }}?')"
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
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos armados</h3>
                @if(isset($inicioSemana) && isset($finSemana))
                    <p class="text-sm">No hay pedidos armados en la semana del {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}</p>
                @else
                    <p class="text-sm text-gray-500">Los pedidos aparecerán aquí cuando los armes desde la sección "Listos"</p>
                @endif
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

    <!-- Resumen y próximos pasos -->
    @if($pedidos->count() > 0)
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resumen por tipo de entrega -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Resumen por Entrega</h3>
            
            @php
                $delivery = $pedidos->where('delivery', 'delivery');
                $puesto = $pedidos->where('delivery', 'puesto');
                $totalVentas = $pedidos->sum('total');
            @endphp
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div>
                        <span class="font-semibold text-yellow-800">🚚 Delivery</span>
                        <div class="text-sm text-yellow-600">Para repartir</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-yellow-700">{{ $delivery->count() }}</div>
                        <div class="text-sm text-yellow-600">S/ {{ number_format($delivery->sum('total'), 2) }}</div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <div>
                        <span class="font-semibold text-green-800">🏪 Puesto</span>
                        <div class="text-sm text-green-600">Para recoger</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-700">{{ $puesto->count() }}</div>
                        <div class="text-sm text-green-600">S/ {{ number_format($puesto->sum('total'), 2) }}</div>
                    </div>
                </div>
                
                <div class="border-t pt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800">Total General</span>
                        <span class="text-xl font-bold text-purple-600">S/ {{ number_format($totalVentas, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos pasos -->
        <div class="bg-purple-50 rounded-xl border border-purple-200 p-4 sm:p-6">
            <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center">
                <span class="mr-2">🎯</span> Próximos Pasos
            </h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-yellow-200 text-yellow-800 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">🚚</span>
                    <div>
                        <div class="font-semibold text-purple-800">Para Delivery:</div>
                        <div class="text-purple-700">Coordinar con repartidor → Marcar "En Entrega" → Confirmar "Entregado"</div>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">🏪</span>
                    <div>
                        <div class="font-semibold text-purple-800">Para Puesto:</div>
                        <div class="text-purple-700">Esperar al cliente en la feria → Entregar pedido → Marcar "Entregado"</div>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">💰</span>
                    <div>
                        <div class="font-semibold text-purple-800">Después:</div>
                        <div class="text-purple-700">Los pedidos entregados se contabilizan para pagar a agricultores</div>
                    </div>
                </div>
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
    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
}

/* Hover states para desktop */
@media (min-width: 641px) {
    select:hover {
        border-color: #8b5cf6;
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