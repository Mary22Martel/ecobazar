{{-- resources/views/admin/pagos/agricultores.blade.php --}}
@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">💰 Pagos a Agricultores</h1>
                <p class="text-blue-100 text-base sm:text-lg">
                    Período de ventas: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}
                </p>
                <p class="text-blue-100 text-sm">
                    🗓️ Entrega en feria: {{ $diaEntrega->format('l, d/m/Y') }}
                </p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.dashboard') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2 no-print">
                    ← Dashboard
                </a>
                <div class="text-2xl font-bold">S/ {{ number_format($totalPagar, 2) }}</div>
                <div class="text-blue-100 text-sm">Total a pagar el {{ $diaEntrega->format('d/m') }}</div>
            </div>
        </div>
    </div>

    <!-- Filtro de Semanas -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" action="{{ route('admin.pagos.agricultores') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="semana" class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Seleccionar Semana de Feria
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Las ventas van de domingo a viernes, y se pagan el sábado en la feria
                    </p>
                    <select name="semana" id="semana" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($opcionesSemanas as $valor => $label)
                            <option value="{{ $valor }}" {{ $semanaSeleccionada == $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors font-semibold">
                        🔍 Consultar
                    </button>
                    @if($totalPagar > 0)
                        <a href="{{ route('admin.pagos.exportar', ['semana' => $semanaSeleccionada]) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors font-semibold">
                            📥 Exportar CSV
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($pagosAgricultores->count() > 0)
    
    <!-- Alerta informativa sobre el flujo -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Pagos de la Semana de Feria</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>✅ <strong>Período de ventas:</strong> {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }}</p>
                    <p>📦 <strong>Agricultores a pagar:</strong> {{ $pagosAgricultores->count() }} agricultores</p>
                    <p>💰 <strong>Total a pagar:</strong> S/ {{ number_format($totalPagar, 2) }} el {{ $diaEntrega->format('l d/m') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $pagosAgricultores->count() }}</div>
            <div class="text-sm text-green-700">Agricultores</div>
            <div class="text-xs text-gray-500">Con ventas</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">S/ {{ number_format($totalPagar, 2) }}</div>
            <div class="text-sm text-blue-700">Total a Pagar</div>
            <div class="text-xs text-gray-500">El {{ $diaEntrega->format('d/m') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $totalPedidos }}</div>
            <div class="text-sm text-orange-700">Pedidos Armados</div>
            <div class="text-xs text-gray-500">Completados</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $totalCantidad }}</div>
            <div class="text-sm text-purple-700">Productos Vendidos</div>
            <div class="text-xs text-gray-500">Total unidades</div>
        </div>
    </div>

    <!-- Lista de Agricultores a Pagar -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">👥 Lista de Pagos por Agricultor</h3>
            <p class="text-sm text-gray-600 mt-1">Agricultores ordenados por monto a cobrar</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agricultor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total a Pagar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pagosAgricultores as $index => $pago)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 font-semibold text-sm">
                                            {{ substr($pago['agricultor']->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $pago['agricultor']->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $pago['agricultor']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $pago['agricultor']->telefono ?? 'Sin teléfono' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $pago['pedidos_atendidos'] }} pedidos
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ $pago['total_productos'] }} productos
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-600">S/ {{ number_format($pago['total_pago'], 2) }}</div>
                        </td>
                        
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ url('/admin/pagos/agricultor/' . $pago['agricultor']->id . '?semana=' . $semanaSeleccionada) }}" 
                               class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                📋 Ver Detalle
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            TOTAL GENERAL A PAGAR:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xl font-bold text-green-600">S/ {{ number_format($totalPagar, 2) }}</div>
                        </td>
                        <td class="px-6 py-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Estadísticas por Estado de Pedidos -->
    <div class="mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach(['pagado' => ['color' => 'yellow', 'icono' => '💳', 'nombre' => 'Pagados'], 
                 'listo' => ['color' => 'blue', 'icono' => '📦', 'nombre' => 'Listos'], 
                 'armado' => ['color' => 'green', 'icono' => '✅', 'nombre' => 'Armados'], 
                 'entregado' => ['color' => 'purple', 'icono' => '🚚', 'nombre' => 'Entregados']] as $estado => $config)
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-{{ $config['color'] }}-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-{{ $config['color'] }}-700 font-medium">{{ $config['nombre'] }}</div>
                    <div class="text-xl font-bold text-{{ $config['color'] }}-600">{{ $estadisticasEstado[$estado]['count'] ?? 0 }}</div>
                </div>
                <div class="text-2xl">{{ $config['icono'] }}</div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                S/ {{ number_format($estadisticasEstado[$estado]['monto'] ?? 0, 2) }}
            </div>
        </div>
        @endforeach
    </div>

    <!-- Top Productos Vendidos -->
    @if($topProductos->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">🏆 Top 5 - Productos Más Vendidos</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach($topProductos as $index => $producto)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center font-bold text-sm mr-2">
                        {{ $index + 1 }}
                    </div>
                    <h4 class="font-semibold text-gray-800 text-sm">{{ $producto['producto']->nombre }}</h4>
                </div>
                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Vendido:</span>
                        <span class="font-medium">{{ $producto['cantidad'] }} {{ $producto['producto']->medida->nombre ?? 'un.' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pedidos:</span>
                        <span class="font-medium">{{ $producto['pedidos_count'] }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-1">
                        <span>Ventas:</span>
                        <span class="font-bold text-green-600">S/ {{ number_format($producto['monto'], 2) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @else
    <!-- Sin datos -->
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="text-6xl mb-4">💰</div>
        <h3 class="text-xl font-semibold mb-2 text-gray-600">Sin pagos esta semana</h3>
        <p class="text-gray-500 mb-4">
            No hay agricultores con pedidos armados en el período del {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }}
        </p>
        <p class="text-sm text-gray-400 mb-6">
            Los pagos se calculan solo para pedidos en estado "armado"
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('admin.pedidos.pagados') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                📦 Ver Pedidos Pagados
            </a>
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                📊 Ir al Dashboard
            </a>
        </div>
    </div>
    @endif

</div>

<script>
// Auto-submit form when week selection changes
document.getElementById('semana').addEventListener('change', function() {
    this.form.submit();
});
</script>

<style>
@media print {
    .no-print { 
        display: none !important; 
    }
    
    body { 
        font-size: 12px; 
        line-height: 1.4;
    }
    
    .container { 
        max-width: 100%; 
        padding: 0; 
        margin: 0;
    }
    
    .bg-gradient-to-r {
        background: #3b82f6 !important;
        -webkit-print-color-adjust: exact;
        color: white !important;
    }
    
    .shadow-lg {
        box-shadow: none !important;
    }
}

/* Estilos para los indicadores de estado */
.border-yellow-500 { border-color: #eab308; }
.text-yellow-600 { color: #ca8a04; }
.text-yellow-700 { color: #a16207; }

.border-blue-500 { border-color: #3b82f6; }
.text-blue-600 { color: #2563eb; }
.text-blue-700 { color: #1d4ed8; }

.border-green-500 { border-color: #10b981; }
.text-green-600 { color: #059669; }
.text-green-700 { color: #047857; }

.border-purple-500 { border-color: #8b5cf6; }
.text-purple-600 { color: #7c3aed; }
.text-purple-700 { color: #6d28d9; }

/* Transiciones suaves */
.transition-colors {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
}

.hover\\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>

@endsection