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
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">💳 Pedidos Pagados</h1>
                <p class="text-orange-100 text-base sm:text-lg">Pedidos que necesitan ser armados</p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2">
                    ← Todos los Pedidos
                </a>
                <div class="text-2xl font-bold">{{ $pedidos->total() ?? $pedidos->count() }}</div>
                <div class="text-orange-100 text-sm">por armar</div>
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
                   class="flex items-center px-4 py-2 rounded-lg bg-orange-100 text-orange-800 border-2 border-orange-200 transition-all font-semibold text-sm whitespace-nowrap">
                    💳 Pagados ({{ $pedidos->total() ?? $pedidos->count() }})
                </a>
                <a href="{{ route('admin.pedidos.listos') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    ✅ Listos
                </a>
                <a href="{{ route('admin.pedidos.armados') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-semibold text-sm whitespace-nowrap">
                    📋 Armados
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
                Pedidos Pagados Esperando Preparación
            </h3>
            <p class="text-sm text-orange-600 mt-1">Los agricultores deben preparar estos productos</p>
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
                            <div class="text-6xl mb-4">✅</div>
                            <h3 class="text-lg font-semibold mb-2 text-green-600">¡Excelente!</h3>
                            <p class="text-sm text-gray-600">No hay pedidos pagados pendientes de preparación</p>
                            <p class="text-xs text-gray-500 mt-2">Los pedidos aparecerán aquí cuando los clientes paguen</p>
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

@endsection