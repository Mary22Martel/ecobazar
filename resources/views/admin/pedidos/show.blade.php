@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-4xl">
    
    <!-- Header del pedido -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📦 Pedido #{{ $order->id }}</h1>
                <p class="text-blue-100 text-base sm:text-lg">{{ $order->nombre }} {{ $order->apellido }}</p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.pedidos.index') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2">
                    ← Volver a Pedidos
                </a>
                @php
                    $estadoConfig = [
                        'pendiente' => ['texto' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-800'],
                        'pagado' => ['texto' => 'Pagado', 'color' => 'bg-orange-100 text-orange-800'],
                        'listo' => ['texto' => 'Listo', 'color' => 'bg-green-100 text-green-800'],
                        'armado' => ['texto' => 'Armado', 'color' => 'bg-blue-100 text-blue-800'],
                        'en_entrega' => ['texto' => 'En Entrega', 'color' => 'bg-purple-100 text-purple-800'],
                        'entregado' => ['texto' => 'Entregado', 'color' => 'bg-emerald-100 text-emerald-800'],
                        'cancelado' => ['texto' => 'Cancelado', 'color' => 'bg-red-100 text-red-800']
                    ];
                    $config = $estadoConfig[$order->estado] ?? ['texto' => ucfirst($order->estado), 'color' => 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="px-4 py-2 rounded-lg text-sm font-bold {{ $config['color'] }}">
                    {{ $config['texto'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Información del cliente -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">👤</span> Información del Cliente
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Cliente</label>
                <p class="text-lg font-bold text-gray-800">{{ $order->nombre }} {{ $order->apellido }}</p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Teléfono</label>
                <p class="text-lg font-bold text-blue-600">
                    <a href="tel:{{ $order->telefono }}" class="hover:underline">{{ $order->telefono }}</a>
                </p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Tipo de Entrega</label>
                @if($order->delivery === 'delivery')
                    <div>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                            🚚 Delivery
                        </span>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->direccion }}, {{ $order->distrito }}</p>
                    </div>
                @else
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        🏪 Recoger en Puesto
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Productos por agricultor -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">📋</span> Lista de Productos por Agricultor
        </h3>
        
        @php
            $productosPorAgricultor = $order->items->groupBy(function($item) {
                return $item->product->user->name ?? 'Agricultor Desconocido';
            });
        @endphp
        
        <div class="space-y-4">
            @foreach($productosPorAgricultor as $agricultor => $productos)
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-800 rounded-full flex items-center justify-center text-xs font-bold mr-2">
                        {{ substr($agricultor, 0, 2) }}
                    </span>
                    {{ $agricultor }}
                </h4>
                
                <div class="space-y-2">
                    @foreach($productos as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0 bg-white rounded px-3">
                        <div class="flex-1">
                            <span class="text-gray-900 font-medium">{{ $item->product->nombre ?? 'Producto no encontrado' }}</span>
                            <span class="text-sm text-gray-500 ml-2">
                                (S/ {{ number_format($item->precio, 2) }} c/u)
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900">
                                {{ $item->cantidad }} {{ $item->product->medida->nombre ?? 'und' }}
                            </span>
                            <div class="text-sm text-green-600 font-semibold">
                                S/ {{ number_format($item->precio * $item->cantidad, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @php
                    $totalAgricultor = $productos->sum(function($item) {
                        return $item->precio * $item->cantidad;
                    });
                @endphp
            </div>
            @endforeach
        </div>
    </div>

    <!-- Instrucciones para armar -->
    @if($order->estado === 'listo')
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-orange-800 mb-4 flex items-center">
            <span class="mr-2">📋</span> Instrucciones para Armar el Pedido
        </h3>
        
        @php
            $agricultores = $productosPorAgricultor->keys()->implode(', ');
            $totalProductos = $order->items->count();
        @endphp
        
        <ol class="space-y-3">
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                <span class="text-orange-800"><strong>Solicitar productos a:</strong> {{ $agricultores }}</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                <span class="text-orange-800"><strong>Verificar que tienes {{ $totalProductos }} productos completos</strong> según la lista de arriba</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                <span class="text-orange-800"><strong>Juntar todos los productos</strong> en una bolsa o caja</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center text-sm font-bold">4</span>
                <span class="text-orange-800"><strong>Etiquetar el paquete con:</strong> "{{ $order->nombre }} {{ $order->apellido }}" (Pedido #{{ $order->id }})</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-800 rounded-full flex items-center justify-center text-sm font-bold">5</span>
                <span class="text-orange-800"><strong>Marcar como ARMADO</strong> cuando esté completamente listo</span>
            </li>
        </ol>
    </div>
    @endif

    <!-- Resumen de pagos -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">💰</span> Resumen de Pagos
        </h3>
        
        <div class="space-y-3">
            @php $totalGeneral = 0; @endphp
            @foreach($productosPorAgricultor as $agricultor => $productos)
                @php
                    $totalAgricultor = $productos->sum(function($item) {
                        return $item->precio * $item->cantidad;
                    });
                    $totalGeneral += $totalAgricultor;
                @endphp
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">{{ $agricultor }}</span>
                    <span class="font-semibold text-gray-900">S/ {{ number_format($totalAgricultor, 2) }}</span>
                </div>
            @endforeach
            
            @if($order->delivery === 'delivery' && $order->distrito)
                @php
                    $zona = \App\Models\Zone::where('name', $order->distrito)->first();
                    $costoEnvio = $zona ? $zona->delivery_cost : 0;
                @endphp
                @if($costoEnvio > 0)
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Costo de Envío ({{ $order->distrito }})</span>
                    <span class="font-semibold text-gray-900">S/ {{ number_format($costoEnvio, 2) }}</span>
                </div>
                @php $totalGeneral += $costoEnvio; @endphp
                @endif
            @endif
            
            <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                <span class="text-lg font-bold text-gray-900">TOTAL DEL PEDIDO</span>
                <span class="text-xl font-bold text-blue-600">S/ {{ number_format($totalGeneral, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Cambiar estado del pedido -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">🔄</span> Cambiar Estado del Pedido
        </h3>
        
        <form method="POST" action="{{ route('admin.pedido.estado', $order->id) }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                    $estadosDisponibles = [
                        'pendiente' => ['texto' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-800 hover:bg-gray-200'],
                        'pagado' => ['texto' => 'Pagado', 'color' => 'bg-orange-100 text-orange-800 hover:bg-orange-200'],
                        'listo' => ['texto' => 'Listo', 'color' => 'bg-green-100 text-green-800 hover:bg-green-200'],
                        'armado' => ['texto' => 'Armado', 'color' => 'bg-blue-100 text-blue-800 hover:bg-blue-200'],
                        'en_entrega' => ['texto' => 'En Entrega', 'color' => 'bg-purple-100 text-purple-800 hover:bg-purple-200'],
                        'entregado' => ['texto' => 'Entregado', 'color' => 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'],
                        'cancelado' => ['texto' => 'Cancelado', 'color' => 'bg-red-100 text-red-800 hover:bg-red-200']
                    ];
                @endphp
                
                @foreach($estadosDisponibles as $estado => $config)
                <label class="cursor-pointer">
                    <input type="radio" name="estado" value="{{ $estado }}" 
                           {{ $order->estado === $estado ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="px-4 py-3 rounded-lg border-2 transition-all text-center font-semibold text-sm
                                {{ $order->estado === $estado ? 'border-blue-500 ' . $config['color'] : 'border-gray-200 ' . $config['color'] }}
                                peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200">
                        {{ $config['texto'] }}
                    </div>
                </label>
                @endforeach
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" 
                        onclick="return confirm('¿Estás seguro de cambiar el estado del pedido?')"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                    💾 Actualizar Estado
                </button>
                
                @if($order->estado === 'listo')
                <button type="submit" 
                        onclick="document.querySelector('input[value=armado]').checked = true; return confirm('¿Confirmas que el pedido está completamente armado?')"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                    ✅ Marcar como Armado
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="bg-gray-50 rounded-xl p-4 sm:p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">📊</span> Información Adicional
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500">ID del Pedido:</span>
                <span class="font-semibold text-gray-800 block">#{{ $order->id }}</span>
            </div>
            <div>
                <span class="text-gray-500">Fecha de Pedido:</span>
                <span class="font-semibold text-gray-800 block">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Cantidad de Items:</span>
                <span class="font-semibold text-gray-800 block">{{ $order->items->count() }} productos</span>
            </div>
            <div>
                <span class="text-gray-500">Agricultores Involucrados:</span>
                <span class="font-semibold text-gray-800 block">{{ $productosPorAgricultor->count() }} agricultores</span>
            </div>
            <div>
                <span class="text-gray-500">Cliente Usuario:</span>
                <span class="font-semibold text-gray-800 block">{{ $order->user->name ?? 'No registrado' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Email Cliente:</span>
                <span class="font-semibold text-gray-800 block">{{ $order->user->email ?? 'No disponible' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Método de Pago:</span>
                <span class="font-semibold text-gray-800 block">MercadoPago</span>
            </div>
            <div>
                <span class="text-gray-500">Última Actualización:</span>
                <span class="font-semibold text-gray-800 block">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

</div>

<style>
/* Mejorar la apariencia de los radio buttons */
input[type="radio"]:checked + div {
    transform: scale(0.98);
}

input[type="radio"] + div:hover {
    transform: scale(1.02);
}
</style>

@endsection