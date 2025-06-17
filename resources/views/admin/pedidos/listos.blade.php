@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">✅ Pedidos Listos</h1>
                <p class="text-green-100 text-base sm:text-lg">Productos preparados, listos para armar</p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2">
                    ← Todos los Pedidos
                </a>
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-green-100 text-sm">por armar</div>
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
                   class="flex items-center px-4 py-2 rounded-lg bg-green-100 text-green-800 border-2 border-green-200 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
                <a href="{{ route('admin.pedidos.armados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados
                </a>
            </div>
        </div>
    </div>

    @if($pedidos->count() > 0)
    <!-- Alerta de acción requerida -->
    <div class="bg-green-50 border-l-4 border-green-400 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <span class="text-2xl mr-3">🎯</span>
            <div>
                <h4 class="text-lg font-bold text-green-800">¡Es hora de armar pedidos!</h4>
                <p class="text-green-700">
                    Los agricultores ya prepararon estos productos. 
                    <strong>Recógelos, revisa que esté todo completo y marca como "Armado"</strong> cuando termines.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Lista de pedidos listos -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-bold text-green-800">
                Pedidos Listos para Armar
            </h3>
            <p class="text-sm text-green-600 mt-1">Los agricultores completaron la preparación</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Pedido</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Productos</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Agricultores</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Entrega</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    @php
                        $agricultoresUnicos = $pedido->items->groupBy('product.user_id')->count();
                        $agricultoresNombres = $pedido->items->pluck('product.user.name')->unique()->filter()->take(2)->implode(', ');
                        if($agricultoresUnicos > 2) {
                            $agricultoresNombres .= ' +' . ($agricultoresUnicos - 2) . ' más';
                        }
                    @endphp
                    
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-green-600">#{{ $pedido->id }}</span>
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
                            <div class="space-y-1">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                                    {{ $pedido->items->count() }} productos
                                </span>
                                <div class="text-xs text-gray-600">
                                    {{ $pedido->items->pluck('product.nombre')->take(2)->implode(', ') }}
                                    @if($pedido->items->count() > 2)
                                        +{{ $pedido->items->count() - 2 }} más
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $agricultoresUnicos }}
                                </span>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ $agricultoresNombres }}
                                </div>
                            </div>
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
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors text-center">
                                    👁️ Ver
                                </a>
                                
                                <form method="POST" action="{{ route('admin.pedido.estado', $pedido->id) }}" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="estado" value="armado">
                                    <button type="submit" 
                                            onclick="return confirm('¿Confirmas que armaste completamente el pedido #{{ $pedido->id }}?')"
                                            class="w-full bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors font-semibold">
                                        📦 ARMADO
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">😴</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-600">No hay pedidos por armar</h3>
                            <p class="text-sm text-gray-500">Los pedidos aparecerán aquí cuando los agricultores terminen de prepararlos</p>
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

    <!-- Resumen y consejos -->
    @if($pedidos->count() > 0)
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resumen -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Resumen de Trabajo</h3>
            
            @php
                $totalVentas = $pedidos->sum('total');
                $pedidosDelivery = $pedidos->where('delivery', 'delivery')->count();
                $totalProductos = $pedidos->sum(function($pedido) {
                    return $pedido->items->count();
                });
            @endphp
            
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $pedidos->count() }}</div>
                    <div class="text-sm text-green-700">Por Armar</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $pedidosDelivery }}</div>
                    <div class="text-sm text-yellow-700">Delivery</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $totalProductos }}</div>
                    <div class="text-sm text-blue-700">Productos</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">S/ {{ number_format($totalVentas, 2) }}</div>
                    <div class="text-sm text-gray-700">Valor Total</div>
                </div>
            </div>
        </div>

        <!-- Consejos para armar -->
        <div class="bg-green-50 rounded-xl border border-green-200 p-4 sm:p-6">
            <h3 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                <span class="mr-2">💡</span> Consejos para Armar
            </h3>
            
            <ul class="space-y-2 text-sm text-green-700">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-5 h-5 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-2 mt-0.5">1</span>
                    <span><strong>Organiza por tipo de entrega</strong> (delivery o puesto)</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-5 h-5 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-2 mt-0.5">2</span>
                    <span><strong>Verifica cada producto</strong> antes de empacar</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-5 h-5 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-2 mt-0.5">3</span>
                    <span><strong>Etiqueta claramente</strong> con nombre del cliente y #pedido</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-5 h-5 bg-green-200 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-2 mt-0.5">4</span>
                    <span><strong>Marca como "Armado"</strong> solo cuando esté 100% completo</span>
                </li>
            </ul>
        </div>
    </div>
    @endif

</div>

@endsection