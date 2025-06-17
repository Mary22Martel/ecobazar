{{-- resources/views/agricultor/pagos.blade.php --}}
@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    
    <!-- Header principal responsivo -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">💰 MIS PAGOS</h1>
                <p class="text-green-100 text-base sm:text-lg mb-2">
                    📅 Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
                </p>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                    <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                    Semana {{ $fechaInicio->weekOfYear }} del {{ $fechaInicio->year }}
                </div>
            </div>
            
            <!-- Total a Pagar destacado -->
            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 sm:p-6 text-center">
                <p class="text-green-100 text-sm font-medium mb-1">💰 Total a Recibir</p>
                <p class="text-3xl sm:text-4xl font-bold">S/ {{ number_format($totalPagar, 2) }}</p>
                <p class="text-green-100 text-sm mt-1">{{ $totalPedidos }} pedidos armados</p>
            </div>
        </div>
    </div>

    <!-- FILTRO DE SEMANAS - IGUAL QUE EL ADMIN -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" action="{{ route('agricultor.pagos') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="semana" class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Seleccionar Semana
                    </label>
                    <select name="semana" id="semana" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @foreach($opcionesSemanas as $valor => $label)
                            <option value="{{ $valor }}" {{ $semanaSeleccionada === $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors font-semibold">
                        🔍 Consultar
                    </button>
                    @if($totalPagar > 0)
                        <a href="{{ route('agricultor.pagos.exportar', ['semana' => $semanaSeleccionada]) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors font-semibold">
                            📥 Exportar CSV
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Navegación de regreso -->
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('agricultor.dashboard') }}" 
           class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
    </div>

    @if($pagos->isEmpty())
        <!-- Estado vacío mejorado -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-8 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="text-5xl sm:text-6xl mb-4">🌱</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">No hay ventas en esta semana</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-base sm:text-lg">
                    No tienes productos vendidos en el período del {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}.
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('productos.create') }}" 
                       class="inline-flex items-center justify-center bg-gradient-to-r from-green-500 to-green-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar Productos
                    </a>
                    <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                       class="inline-flex items-center justify-center bg-gray-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:bg-gray-600 transform hover:scale-105 transition-all shadow-lg">
                        📦 Ver Pedidos
                    </a>
                </div>
                
                <!-- Sugerencia para cambiar semana -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-blue-700 text-sm">
                        💡 <strong>Sugerencia:</strong> Prueba seleccionar una semana diferente en el filtro de arriba para ver tus ventas anteriores.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Cards de estadísticas responsivas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Productos -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <span class="text-xl sm:text-2xl">📦</span>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600 truncate">Productos Vendidos</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalProductos }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Cantidad -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border-l-4 border-green-400">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <span class="text-xl sm:text-2xl">📊</span>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600 truncate">Cantidad Total</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($totalCantidad) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pedidos Armados -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border-l-4 border-green-600">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                        <span class="text-xl sm:text-2xl">✅</span>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600 truncate">Pedidos Listos</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $estadisticas['armado']['count'] ?? 0 }}</p>
                        <p class="text-sm text-green-600 truncate">S/ {{ number_format($estadisticas['armado']['monto'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pedidos Entregados -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg border-l-4 border-gray-400">
                <div class="flex items-center">
                    <div class="bg-gray-100 p-3 rounded-full flex-shrink-0">
                        <span class="text-xl sm:text-2xl">🚚</span>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600 truncate">Entregados</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $estadisticas['entregado']['count'] ?? 0 }}</p>
                        <p class="text-sm text-gray-600 truncate">S/ {{ number_format($estadisticas['entregado']['monto'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top productos y ventas por día - responsivo -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Top productos -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-green-100 p-2 rounded-full mr-3">🏆</span>
                    Top 5 Productos
                </h3>
                @if($topProductos->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($topProductos as $index => $producto)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center min-w-0 flex-1">
                                    <span class="bg-green-500 text-white text-sm font-bold rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-800 truncate">{{ $producto['producto']->nombre }}</p>
                                        <p class="text-sm text-gray-500">{{ $producto['cantidad'] }} vendidos</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <p class="font-bold text-green-600 text-sm sm:text-base">S/ {{ number_format($producto['monto'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No hay datos de productos aún</p>
                @endif
            </div>

            <!-- Ventas por día -->
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-lg">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
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
                            <div class="flex items-center min-w-0 flex-1">
                                <span class="w-3 h-3 rounded-full mr-3 flex-shrink-0 {{ $ventasDia['monto'] > 0 ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                <span class="font-medium text-gray-700">{{ $diaEs }}</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-2">
                                <p class="font-bold text-sm sm:text-base {{ $ventasDia['monto'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
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

        <!-- Tabla detallada responsive -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 sm:mb-8">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-4 sm:px-6 py-4 border-b">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                    <span class="bg-green-200 p-2 rounded-full mr-3">📋</span>
                    Detalle de Productos Vendidos
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Semana del {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}
                </p>
            </div>
            
            <!-- Vista móvil (cards) -->
            <div class="block lg:hidden">
                @foreach($pagos as $index => $pago)
                    <div class="p-4 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center min-w-0 flex-1">
                                <div class="bg-green-100 p-2 rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-bold text-green-600">#{{ $index + 1 }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-base font-bold text-gray-900 truncate">
                                        {{ $pago['producto']->nombre }}
                                    </div>
                                    <div class="text-sm text-gray-500 truncate">
                                        {{ $pago['producto']->categoria->nombre ?? 'Sin categoría' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-2">
                                <span class="text-lg font-bold text-green-600">
                                    S/ {{ number_format($pago['monto'], 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2 py-1 rounded-full block">
                                    {{ number_format($pago['cantidad']) }}
                                </span>
                                <span class="text-xs text-gray-500 mt-1 block">Cantidad</span>
                            </div>
                            <div>
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2 py-1 rounded-full block">
                                    S/ {{ number_format($pago['precio_promedio'], 2) }}
                                </span>
                                <span class="text-xs text-gray-500 mt-1 block">Precio prom.</span>
                            </div>
                            <div>
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2 py-1 rounded-full block">
                                    {{ $pago['pedidos_count'] }}
                                </span>
                                <span class="text-xs text-gray-500 mt-1 block">Pedidos</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Total en móvil -->
                <div class="bg-green-50 p-4">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total a Pagar:</span>
                        <span class="text-2xl font-bold text-green-600">
                            S/ {{ number_format($totalPagar, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Vista desktop (tabla) -->
            <div class="hidden lg:block overflow-x-auto">
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
                                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                                        {{ number_format($pago['cantidad']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm text-gray-900">
                                        S/ {{ number_format($pago['precio_promedio'], 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
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

        <!-- Información sobre pagos -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 sm:p-6 mb-6">
            <div class="flex items-start">
                <div class="bg-green-200 p-3 rounded-full mr-4 flex-shrink-0">
                    <span class="text-xl">ℹ️</span>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-green-800 mb-2">Información sobre Pagos</h4>
                    <div class="text-green-700 space-y-2 text-sm sm:text-base">
                        <p>• Los pagos se calculan de <strong>lunes a domingo</strong> de cada semana.</p>
                        <p>• <strong>IMPORTANTE:</strong> Solo se pagan los pedidos en estado <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full font-semibold">✅ ARMADO</span>.</p>
                        <p>• Los pedidos armados indican que fueron verificados y están listos para entrega.</p>
                        <p>• Los montos se pagan típicamente los <strong>sábados</strong> después del cierre semanal.</p>
                        <p>• Puedes usar el filtro de arriba para consultar pagos de <strong>semanas anteriores</strong>.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de estados simplificado -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 p-2 rounded-full mr-3">📊</span>
                Estado de tus Pedidos - Semana Seleccionada
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Pedidos Pagados -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">💰 Pagados</p>
                            <p class="text-lg font-bold text-gray-800">{{ $estadisticas['pagado']['count'] ?? 0 }} pedidos</p>
                            <p class="text-sm text-gray-600">S/ {{ number_format($estadisticas['pagado']['monto'] ?? 0, 2) }}</p>
                        </div>
                        <div class="text-gray-400">
                            <span class="text-2xl">💰</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Esperando ser armados</p>
                </div>

                <!-- Pedidos Armados (destacado) -->
                <div class="bg-green-50 border-2 border-green-400 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-green-600">✅ ARMADOS</p>
                            <p class="text-lg font-bold text-green-800">{{ $estadisticas['armado']['count'] ?? 0 }} pedidos</p>
                            <p class="text-sm font-bold text-green-600">S/ {{ number_format($estadisticas['armado']['monto'] ?? 0, 2) }}</p>
                        </div>
                        <div class="text-green-400">
                            <span class="text-2xl">✅</span>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 mt-2 font-semibold">💸 Esto se te pagará</p>
                </div>

                <!-- Pedidos Entregados -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">🚚 Entregados</p>
                            <p class="text-lg font-bold text-gray-800">{{ $estadisticas['entregado']['count'] ?? 0 }} pedidos</p>
                            <p class="text-sm text-gray-600">S/ {{ number_format($estadisticas['entregado']['monto'] ?? 0, 2) }}</p>
                        </div>
                        <div class="text-gray-400">
                            <span class="text-2xl">🚚</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Ya completados</p>
                </div>
            </div>
        </div>
    @endif

</div>

<script>
// Auto-submit form when week selection changes (igual que en admin)
document.getElementById('semana').addEventListener('change', function() {
    this.form.submit();
});
</script>

@endsection