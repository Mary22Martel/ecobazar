@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">⏰ Pedidos Expirados</h1>
                <p class="text-red-100 text-base sm:text-lg">Pedidos que no fueron pagados a tiempo</p>
            </div>
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver a Pedidos
            </a>
        </div>
    </div>

    <!-- Filtros rápidos -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <div class="flex space-x-2 min-w-max">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📦 Todos los Pedidos
                </a>
                <a href="{{ route('admin.pedidos.pagados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados (Por Armar)
                </a>
                <a href="{{ route('admin.pedidos.listos') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos (Para Armar)
                </a>
                <a href="{{ route('admin.pedidos.armados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados (Para Entregar)
                </a>
                <a href="{{ route('admin.pedidos.expirados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg bg-red-100 text-red-800 border-2 border-red-200 transition-all font-semibold text-sm whitespace-nowrap">
                    ⏰ Expirados
                </a>
            </div>
        </div>
    </div>

    <!-- Información sobre pedidos expirados -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <span class="text-2xl">ℹ️</span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800">Información sobre pedidos expirados</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Los pedidos expiran automáticamente después de 20 minutos sin pago</li>
                        <li>El stock se libera automáticamente cuando un pedido expira</li>
                        <li>Los pedidos expirados no aparecen en las listas principales para evitar confusión</li>
                        <li>Esta vista es solo para historial y auditoría</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de pedidos expirados -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">
                Pedidos Expirados 
                @if(isset($pedidos))
                    ({{ method_exists($pedidos, 'total') ? $pedidos->total() : $pedidos->count() }})
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
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Expiró</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    <tr class="hover:bg-red-50 transition-colors opacity-75">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-red-600">#{{ $pedido->id }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $pedido->nombre }} {{ $pedido->apellido }}</div>
                                <div class="text-sm text-gray-500">{{ $pedido->telefono }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-200 text-red-900">
                                ⏰ Expirado
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @if($pedido->delivery === 'delivery')
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">
                                    🚚 Delivery
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $pedido->distrito }}</div>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">
                                    🏪 Puesto
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-sm">
                                {{ $pedido->items->count() }} items
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-bold text-gray-500 line-through">
                                S/ {{ number_format($pedido->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $pedido->updated_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $pedido->updated_at->format('H:i') }}
                            </div>
                            @if($pedido->expires_at)
                            <div class="text-xs text-red-500 mt-1">
                                Límite: {{ $pedido->expires_at->format('H:i') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.pedido.detalle', $pedido->id) }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    👁️ Ver Detalle
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">✅</div>
                            <h3 class="text-lg font-semibold mb-2">¡Excelente!</h3>
                            <p class="text-sm">No hay pedidos expirados. Todos los clientes están pagando a tiempo.</p>
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

    <!-- Estadísticas de expiración -->
    @if($pedidos->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Estadísticas de Expiración</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $pedidos->count() }}</div>
                <div class="text-sm text-red-700">Total Expirados</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">
                    S/ {{ number_format($pedidos->sum('total'), 2) }}
                </div>
                <div class="text-sm text-red-700">Valor Total Perdido</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $pedidos->sum(function($p) { return $p->items->sum('cantidad'); }) }}
                </div>
                <div class="text-sm text-red-700">Productos Liberados</div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection