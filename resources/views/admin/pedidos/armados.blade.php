@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📋 Pedidos Armados</h1>
                <p class="text-purple-100 text-base sm:text-lg">Pedidos listos para entrega</p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2">
                    ← Todos los Pedidos
                </a>
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-purple-100 text-sm">para entregar</div>
            </div>
        </div>
    </div>

    <!-- Navegación entre estados -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <div class="flex space-x-2 min-w-max">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📦 Todos
                </a>
                <a href="{{ route('admin.pedidos.pagados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados
                </a>
                <a href="{{ route('admin.pedidos.listos') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos
                </a>
                <a href="{{ route('admin.pedidos.armados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-purple-100 text-purple-800 border-2 border-purple-200 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados ({{ $pedidos->total() ?? $pedidos->count() }})
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
            </h3>
            <p class="text-sm text-purple-600 mt-1">Esperando ser entregados a los clientes</p>
        </div>
        
        <div class="overflow-x-auto">
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
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">📦</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos armados</h3>
                            <p class="text-sm text-gray-500">Los pedidos aparecerán aquí cuando los armes desde la sección "Listos"</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($pedidos) && method_exists($pedidos, 'hasPages') && $pedidos->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $pedidos->links() }}
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

@endsection