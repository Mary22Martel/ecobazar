@php
    $agricultor = \App\Models\User::find($agricultor_id);
    
    $query = \App\Models\OrderItem::query()
        ->join('productos', 'order_items.producto_id', '=', 'productos.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('productos.user_id', $agricultor_id);

    if ($fecha_inicio && $fecha_fin) {
        $query->whereBetween('orders.created_at', [
            \Carbon\Carbon::parse($fecha_inicio)->startOfDay(),
            \Carbon\Carbon::parse($fecha_fin)->endOfDay()
        ]);
    }

    if ($estado_filtro && $estado_filtro !== 'todos') {
        $query->where('orders.estado', $estado_filtro);
    }

    $ventas = $query->with(['order', 'product'])
        ->select([
            'order_items.*',
            'orders.id as order_id',
            'orders.nombre as cliente_nombre',
            'orders.apellido as cliente_apellido', 
            'orders.created_at as fecha_pedido',
            'orders.estado as estado_pedido',
            'productos.nombre as producto_nombre'
        ])
        ->orderBy('orders.created_at', 'desc')
        ->get();

    $totalVentas = $ventas->sum(function($item) {
        return $item->precio * $item->cantidad;
    });

    $ventasPorProducto = $ventas->groupBy('producto_nombre')->map(function($items) {
        return [
            'cantidad_total' => $items->sum('cantidad'),
            'monto_total' => $items->sum(function($item) {
                return $item->precio * $item->cantidad;
            }),
            'ventas_count' => $items->count()
        ];
    });
@endphp

<div class="space-y-6">
    
    <!-- Header del agricultor -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $agricultor->name }}</h2>
                <p class="text-green-100">
                    Mercado: {{ $agricultor->mercado->name ?? 'Sin mercado asignado' }}
                </p>
                <p class="text-green-100">
                    Período: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-green-100">Total a Pagar</p>
                <p class="text-3xl font-bold">S/ {{ number_format($totalVentas, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Resumen por productos -->
    @if($ventasPorProducto->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($ventasPorProducto as $producto => $datos)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">{{ $producto }}</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                    {{ $datos['ventas_count'] }} ventas
                </span>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-gray-600">
                    Cantidad vendida: <span class="font-medium">{{ number_format($datos['cantidad_total'], 1) }}</span>
                </p>
                <p class="text-sm text-gray-600">
                    Monto total: <span class="font-bold text-green-600">S/ {{ number_format($datos['monto_total'], 2) }}</span>
                </p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Tabla detallada de ventas -->
    @if($ventas->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detalle de Ventas Individuales</h3>
            <p class="text-sm text-gray-600">{{ $ventas->count() }} transacciones encontradas</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pedido
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Producto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio Unitario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ventas as $venta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">#{{ $venta->order_id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $venta->cliente_nombre }} {{ $venta->cliente_apellido }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $venta->producto_nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($venta->cantidad, 1) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">S/ {{ number_format($venta->precio, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">
                                S/ {{ number_format($venta->precio * $venta->cantidad, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoColors = [
                                    'pagado' => 'bg-yellow-100 text-yellow-800',
                                    'armado' => 'bg-green-100 text-green-800',
                                    'en_entrega' => 'bg-blue-100 text-blue-800',
                                    'entregado' => 'bg-purple-100 text-purple-800',
                                    'pendiente' => 'bg-gray-100 text-gray-800',
                                    'cancelado' => 'bg-red-100 text-red-800',
                                ];
                                $colorClass = $estadoColors[$venta->estado_pedido] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                {{ ucfirst($venta->estado_pedido) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($venta->fecha_pedido)->format('d/m/Y H:i') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            TOTAL A PAGAR:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-600">
                                S/ {{ number_format($totalVentas, 2) }}
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="text-center py-8">
        <p class="text-gray-500">No se encontraron ventas para este agricultor en el período seleccionado.</p>
    </div>
    @endif

    <!-- Nota importante -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h4 class="text-yellow-800 font-semibold">Información importante</h4>
                <p class="text-yellow-700 text-sm mt-1">
                    • Solo se muestran los montos correspondientes a productos vendidos<br>
                    • No se incluyen costos de delivery ni comisiones<br>
                    • Los pedidos deben estar en estado "{{ ucfirst($estado_filtro) }}" para ser considerados listos para pago
                </p>
            </div>
        </div>
    </div>

</div>