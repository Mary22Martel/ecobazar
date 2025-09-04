@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Detalle de Entregas</h1>
                    <p class="mt-2 text-sm md:text-base text-gray-600">
                        Repartidor: <span class="font-semibold text-blue-600">{{ $repartidor->name }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Día de entrega: <span class="font-semibold text-green-600">{{ $diaEntrega->format('d/m/Y') }}</span>
                    </p>
                </div>
                <a href="{{ route('admin.repartidores.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Filtro de Semanas -->
        @if(isset($opcionesSemanas))
        <div class="mb-4 md:mb-6">
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label for="semana" class="block text-sm font-medium text-gray-700 mb-1">
                            Filtrar por Semana de Feria
                        </label>
                        <select name="semana" id="semana" 
                                class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ request('semana', 0) == $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:self-end">
                        <button type="submit" 
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Resumen de Pagos -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Resumen de Pagos por Entregas</h2>
            </div>
            
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $estadisticasEntregas['total_entregas'] }}</div>
                        <div class="text-sm text-blue-600">Total Entregas</div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $estadisticasEntregas['entregas_completadas'] }}</div>
                        <div class="text-sm text-green-600">Completadas</div>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ count($pedidosPorZona) }}</div>
                        <div class="text-sm text-purple-600">Zonas Cubiertas</div>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">S/ {{ number_format($totalAPagar, 2) }}</div>
                        <div class="text-sm text-yellow-600">Total a Pagar</div>
                    </div>
                </div>

                <!-- Nota sobre el pago -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-amber-800">Información de Pago</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                El monto mostrado es referencial basado en las tarifas del sistema. 
                                El pago real debe acordarse según lo establecido con el repartidor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entregas por Zona -->
        <div class="space-y-6">
            @if(count($pedidosPorZona) > 0)
                @foreach($pedidosPorZona as $zona => $pedidos)
                    @php
                        $zonaInfo = $zonasInfo->get($zona);
                        $tarifaZona = $zonaInfo ? $zonaInfo->delivery_cost : 0;
                        $pedidosCompletados = $pedidos->where('estado', 'entregado')->count();
                        $totalPedidosZona = $pedidos->count();
                        $pagoZona = $pedidosCompletados * $tarifaZona;
                    @endphp
                    
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $zona }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pedidosCompletados }}/{{ $totalPedidosZona }} pedidos entregados
                                        • Tarifa: S/ {{ number_format($tarifaZona, 2) }} por entrega
                                    </p>
                                </div>
                                <div class="mt-2 sm:mt-0">
                                    <div class="text-lg font-bold text-green-600">
                                        S/ {{ number_format($pagoZona, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Total zona</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @foreach($pedidos->sortBy('estado') as $pedido)
                                <div class="p-4 md:p-6">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <span class="text-sm font-medium text-gray-900">
                                                    Pedido #{{ $pedido->id }}
                                                </span>
                                                
                                                @if($pedido->estado === 'entregado')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Entregado
                                                    </span>
                                                @elseif($pedido->estado === 'en_entrega')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        En proceso
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst($pedido->estado) }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>
                                                    <span class="font-medium">Cliente:</span> 
                                                    {{ $pedido->nombre }} {{ $pedido->apellido }}
                                                </p>
                                                <p>
                                                    <span class="font-medium">Dirección:</span> 
                                                    {{ $pedido->direccion }}
                                                </p>
                                                <p>
                                                    <span class="font-medium">Teléfono:</span> 
                                                    {{ $pedido->telefono }}
                                                </p>
                                                <p>
                                                    <span class="font-medium">Total pedido:</span> 
                                                    S/ {{ number_format($pedido->total, 2) }}
                                                </p>
                                                @if($pedido->estado === 'entregado')
                                                    <p class="text-xs text-green-600">
                                                        <span class="font-medium">Entregado:</span> 
                                                        {{ $pedido->updated_at->format('d/m/Y H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 sm:mt-0 sm:ml-4 text-right">
                                            @if($pedido->estado === 'entregado')
                                                <div class="text-lg font-semibold text-green-600">
                                                    S/ {{ number_format($tarifaZona, 2) }}
                                                </div>
                                                <div class="text-xs text-green-500">Pago por entrega</div>
                                            @else
                                                <div class="text-sm text-gray-500">
                                                    Sin pago (no entregado)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white shadow rounded-lg p-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay entregas para mostrar</h3>
                    <p class="text-gray-600">
                        Este repartidor no tiene pedidos asignados para el día {{ $diaEntrega->format('d/m/Y') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection