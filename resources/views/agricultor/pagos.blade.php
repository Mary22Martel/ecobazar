@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Header Principal -->
    <div class="bg-white shadow-lg border-b-4 border-green-500">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">
                        💰 Panel de Ventas
                    </h1>
                    <p class="text-lg text-gray-600">
                        📅 Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
                    </p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        Semana Actual (Lunes a Domingo)
                    </div>
                </div>
                
                <!-- Total a Pagar - Destacado -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-xl">
                    <div class="text-center">
                        <p class="text-green-100 text-sm font-medium mb-1">💰 Total a Recibir</p>
                        <p class="text-4xl font-bold">S/ {{ number_format($totalPagar, 2) }}</p>
                        <p class="text-green-100 text-sm mt-1">{{ $totalPedidos }} pedidos armados</p>
                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-20">
                            <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                            Solo pedidos verificados
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @if($pagos->isEmpty())
            <!-- Estado Vacío Mejorado -->
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="bg-white rounded-3xl p-8 shadow-lg">
                        <div class="text-6xl mb-4">🌱</div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-4">No hay ventas esta semana</h3>
                        <p class="text-gray-500 mb-6">Aún no tienes productos vendidos en el período actual.</p>
                        <a href="{{ route('productos.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar Productos
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Cards de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Productos -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Productos Vendidos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalProductos }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Cantidad -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Cantidad Total</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCantidad) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Armados -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pedidos Listos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['armados']['count'] }}</p>
                            <p class="text-sm text-green-600">S/ {{ number_format($estadisticas['armados']['monto'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Entregados -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Entregados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['entregados']['count'] }}</p>
                            <p class="text-sm text-purple-600">S/ {{ number_format($estadisticas['entregados']['monto'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos Temporalmente Deshabilitados -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Panel de Top Productos -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-100 p-2 rounded-full mr-3">🏆</span>
                        Top 5 Productos Más Vendidos
                    </h3>
                    @if($topProductos->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($topProductos as $index => $producto)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="bg-blue-500 text-white text-sm font-bold rounded-full w-6 h-6 flex items-center justify-center mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $producto['producto']->nombre }}</p>
                                            <p class="text-sm text-gray-500">{{ $producto['cantidad'] }} vendidos</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-green-600">S/ {{ number_format($producto['monto'], 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No hay datos de productos aún</p>
                    @endif
                </div>

                <!-- Panel de Ventas por Día -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-green-100 p-2 rounded-full mr-3">📅</span>
                        Ventas por Día de la Semana
                    </h3>
                    <div class="space-y-3">
                        @php
                            $diasSemana = [
                                'Monday' => 'Lunes',
                                'Tuesday' => 'Martes', 
                                'Wednesday' => 'Miércoles',
                                'Thursday' => 'Jueves',
                                'Friday' => 'Viernes',
                                'Saturday' => 'Sábado',
                                'Sunday' => 'Domingo'
                            ];
                        @endphp
                        @foreach($diasSemana as $diaEn => $diaEs)
                            @php
                                $ventasDia = $ventasPorDia[$diaEn] ?? ['monto' => 0, 'pedidos' => 0];
                            @endphp
                            <div class="flex items-center justify-between p-3 {{ $ventasDia['monto'] > 0 ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }} rounded-lg">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-3 {{ $ventasDia['monto'] > 0 ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    <span class="font-medium text-gray-700">{{ $diaEs }}</span>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold {{ $ventasDia['monto'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        S/ {{ number_format($ventasDia['monto'], 2) }}
                                    </p>
                                    @if($ventasDia['monto'] > 0)
                                        <p class="text-xs text-green-500">{{ $ventasDia['pedidos'] }} pedidos</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tabla Detallada Mejorada -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-gray-200 p-2 rounded-full mr-3">📋</span>
                        Detalle de Productos Vendidos
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Prom.
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pedidos
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pagos as $index => $pago)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                                <span class="text-sm font-bold text-green-600">#{{ $index + 1 }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $pago['producto']->nombre }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $pago['producto']->categoria->nombre ?? 'Sin categoría' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                            {{ number_format($pago['cantidad']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm text-gray-900">
                                            S/ {{ number_format($pago['precio_promedio'], 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                                            {{ $pago['pedidos_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-lg font-bold text-green-600">
                                            S/ {{ number_format($pago['monto'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-900">
                                    Total a Pagar:
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-2xl font-bold text-green-600">
                                        S/ {{ number_format($totalPagar, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-blue-800 mb-2">ℹ️ Información sobre Pagos</h4>
                        <div class="text-blue-700 space-y-2">
                            <p>• Los pagos se calculan de <strong>sábado a viernes</strong> de cada semana.</p>
                            <p>• <strong>IMPORTANTE:</strong> Solo se pagan los pedidos en estado <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full font-semibold">✅ ARMADO</span>.</p>
                            <p>• Los pedidos armados indican que fueron verificados y están listos para entrega.</p>
                            <p>• Los montos se pagan típicamente los <strong>sábados</strong> después del cierre semanal.</p>
                            <p>• Para consultas sobre pagos, contacta al administrador del mercado.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Estado de Pedidos -->
            <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="bg-yellow-100 p-2 rounded-full mr-3">📊</span>
                    Estado de tus Pedidos esta Semana
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pedidos Pagados -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600">💰 Pagados</p>
                                <p class="text-lg font-bold text-blue-800">{{ $estadisticas['pagados']['count'] }} pedidos</p>
                                <p class="text-sm text-blue-600">S/ {{ number_format($estadisticas['pagados']['monto'], 2) }}</p>
                            </div>
                            <div class="text-blue-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-blue-500 mt-2">Esperando ser armados</p>
                    </div>

                    <!-- Pedidos Armados (Lo que se paga) -->
                    <div class="bg-green-50 border-2 border-green-400 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-green-600">✅ ARMADOS</p>
                                <p class="text-lg font-bold text-green-800">{{ $estadisticas['armados']['count'] }} pedidos</p>
                                <p class="text-sm font-bold text-green-600">S/ {{ number_format($estadisticas['armados']['monto'], 2) }}</p>
                            </div>
                            <div class="text-green-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-green-600 mt-2 font-semibold">💸 Esto se te pagará</p>
                    </div>

                    <!-- Pedidos Entregados -->
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-600">🚚 Entregados</p>
                                <p class="text-lg font-bold text-purple-800">{{ $estadisticas['entregados']['count'] }} pedidos</p>
                                <p class="text-sm text-purple-600">S/ {{ number_format($estadisticas['entregados']['monto'], 2) }}</p>
                            </div>
                            <div class="text-purple-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-purple-500 mt-2">Ya completados</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection