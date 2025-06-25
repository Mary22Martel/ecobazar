{{-- resources/views/admin/reportes/semanales.blade.php --}}
@php
    // Configurar Carbon para español
    \Carbon\Carbon::setLocale('es');
@endphp
@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📊 Reportes Semanales - Feria Agrícola</h1>
                <p class="text-purple-100 text-base sm:text-lg">
                    Período de ventas: {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}
                </p>
                <p class="text-purple-100 text-sm">
                    🗓️ Entrega en feria: {{ $diaEntrega->format('l, d/m/Y') }}
                </p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.dashboard') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2 no-print">
                    ← Dashboard
                </a>
                <div class="text-2xl font-bold">{{ $estadisticas['total_pedidos'] }}</div>
                <div class="text-purple-100 text-sm">pedidos para el {{ $diaEntrega->format('d/m') }}</div>
            </div>
        </div>
    </div>

    <!-- Filtro de Semanas -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <form method="GET" action="{{ route('admin.reportes.semanales') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="semana" class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Seleccionar Semana de Feria para Analizar
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Las ventas van de domingo a viernes, y se entregan el sábado en la feria
                    </p>
                    <select name="semana" id="semana" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @foreach($opcionesSemanas as $valor => $label)
                            <option value="{{ $valor }}" {{ $semanaSeleccionada == $valor ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" 
                            class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                        🔍 Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="mb-6 grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $estadisticas['total_pedidos'] }}</div>
            <div class="text-sm text-blue-700">Pedidos Completados</div>
            <div class="text-xs text-gray-500">Para entrega {{ $diaEntrega->format('d/m') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">S/ {{ number_format($estadisticas['total_ventas'], 2) }}</div>
            <div class="text-sm text-green-700">Ventas Totales</div>
            <div class="text-xs text-gray-500">Dom-Vie</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $estadisticas['agricultores_activos'] }}</div>
            <div class="text-sm text-purple-700">Agricultores Activos</div>
            <div class="text-xs text-gray-500">Participantes</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $estadisticas['productos_vendidos'] }}</div>
            <div class="text-sm text-orange-700">Productos Vendidos</div>
            <div class="text-xs text-gray-500">Unidades/Kg</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 text-center">
            <div class="text-2xl font-bold text-gray-600">S/ {{ number_format($estadisticas['promedio_por_pedido'], 2) }}</div>
            <div class="text-sm text-gray-700">Promedio/Pedido</div>
            <div class="text-xs text-gray-500">Ticket promedio</div>
        </div>
    </div>

    @if($estadisticas['total_pedidos'] > 0)
    
    <!-- Alerta informativa sobre el flujo -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Flujo de la Feria Agrícola</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>📱 <strong>Domingo a Viernes:</strong> Los clientes realizan pedidos online</p>
                    <p>👨‍🌾 <strong>Viernes noche:</strong> Los agricultores preparan sus productos</p>
                    <p>🚚 <strong>Sábado:</strong> Entrega en la feria - No hay ventas online</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes por Secciones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Reporte de Ventas -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-bold text-blue-800">💰 Análisis de Ventas</h3>
                <p class="text-sm text-blue-600 mt-1">Ventas por día (Dom-Vie) y tipo de entrega</p>
            </div>
            
            <div class="p-4 sm:p-6">
                <!-- Ventas por Día -->
                <h4 class="font-semibold text-gray-800 mb-3">Ventas por Día de la Semana</h4>
                <div class="space-y-2 mb-6">
                    @forelse($reporteVentas['por_dia'] as $dia)
                    @php
                        $fecha = Carbon\Carbon::parse($dia->fecha);
                        $esDomingo = $fecha->dayOfWeek === Carbon\Carbon::SUNDAY;
                        $esViernes = $fecha->dayOfWeek === Carbon\Carbon::FRIDAY;
                        
                        // Traducción manual de días para asegurar español
                        $diasEspanol = [
                            'Sunday' => 'domingo',
                            'Monday' => 'lunes',
                            'Tuesday' => 'martes', 
                            'Wednesday' => 'miércoles',
                            'Thursday' => 'jueves',
                            'Friday' => 'viernes',
                            'Saturday' => 'sábado'
                        ];
                        
                        $diaIngles = $fecha->format('l');
                        $nombreDia = $diasEspanol[$diaIngles] ?? $diaIngles;
                    @endphp
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg {{ $esDomingo ? 'border-l-4 border-green-500' : ($esViernes ? 'border-l-4 border-red-500' : '') }}">
                        <div>
                            <span class="font-medium">
                                {{ ucfirst($nombreDia) }}, {{ $fecha->format('d/m') }}
                                @if($esDomingo) <span class="text-green-600 text-xs">(Inicio)</span> @endif
                                @if($esViernes) <span class="text-red-600 text-xs">(Último día)</span> @endif
                            </span>
                            <div class="text-sm text-gray-600">{{ $dia->pedidos }} pedidos</div>
                        </div>
                        <span class="font-bold text-green-600">S/ {{ number_format($dia->ventas, 2) }}</span>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        Sin ventas registradas en este período
                    </div>
                    @endforelse
                </div>
                
                <!-- Ventas por Tipo -->
                <h4 class="font-semibold text-gray-800 mb-3">Por Tipo de Entrega</h4>
                <div class="space-y-2">
                    @forelse($reporteVentas['por_tipo'] as $tipo)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="mr-2">{{ $tipo->delivery === 'delivery' ? '🚚' : '🏪' }}</span>
                            <div>
                                <span class="font-medium">{{ $tipo->delivery === 'delivery' ? 'Delivery' : 'Recojo en Puesto' }}</span>
                                <div class="text-sm text-gray-600">{{ $tipo->pedidos }} pedidos</div>
                            </div>
                        </div>
                        <span class="font-bold text-green-600">S/ {{ number_format($tipo->ventas, 2) }}</span>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        Sin datos de entrega
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 bg-orange-50">
                <h3 class="text-lg font-bold text-orange-800">🥕 Productos Más Vendidos</h3>
                <p class="text-sm text-orange-600 mt-1">Top 10 productos para entrega del {{ $diaEntrega->format('d/m') }}</p>
            </div>
            
            <div class="p-4 sm:p-6">
                <div class="space-y-3">
                    @forelse($reporteProductos['productos_top']->take(10) as $index => $producto)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-sm mr-3">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $producto->nombre }}</div>
                            <div class="text-sm text-gray-600">
                                Por {{ $producto->user->name ?? 'N/A' }} • 
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-orange-600">{{ $producto->order_items_sum_cantidad }}</div>
                            <div class="text-xs text-gray-500">{{ $producto->medida->nombre ?? 'unidades' }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        Sin productos vendidos esta semana
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Agricultores -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-bold text-green-800">👨‍🌾 Agricultores Destacados</h3>
                <p class="text-sm text-green-600 mt-1">Ranking por ventas de la semana</p>
            </div>
            
            <div class="p-4 sm:p-6">
                <div class="space-y-3">
                    @forelse($reporteAgricultores->take(10) as $index => $agricultor)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-sm mr-3">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $agricultor->name }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $agricultor->total_productos }} productos vendidos
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-600">S/ {{ number_format($agricultor->total_ventas, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $agricultor->pedidos_count }} pedidos</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        Sin agricultores activos esta semana
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Análisis por Categorías -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 bg-purple-50">
                <h3 class="text-lg font-bold text-purple-800">📂 Análisis por Categorías</h3>
                <p class="text-sm text-purple-600 mt-1">Rendimiento de categorías de productos</p>
            </div>
            
            <div class="p-4 sm:p-6">
                <div class="space-y-3">
                    @forelse($reporteProductos['categorias'] as $categoria)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-800">{{ $categoria->nombre }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $categoria->description ?? 'Sin descripción' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-purple-600">{{ $categoria->ventas_count }}</div>
                            <div class="text-xs text-gray-500">ventas</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        Sin categorías con ventas esta semana
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Insights y Recomendaciones -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">💡</span> Insights y Recomendaciones
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Insight 1: Día más productivo -->
            @if($reporteVentas['por_dia']->count() > 0)
            @php
                $diaMasProductivo = $reporteVentas['por_dia']->sortByDesc('ventas')->first();
                $fechaMasProductiva = Carbon\Carbon::parse($diaMasProductivo->fecha);
                
                // Traducción manual para asegurar español
                $diasEspanol = [
                    'Sunday' => 'domingo',
                    'Monday' => 'lunes',
                    'Tuesday' => 'martes', 
                    'Wednesday' => 'miércoles',
                    'Thursday' => 'jueves',
                    'Friday' => 'viernes',
                    'Saturday' => 'sábado'
                ];
                
                $diaIngles = $fechaMasProductiva->format('l');
                $nombreDiaMasProductivo = $diasEspanol[$diaIngles] ?? $diaIngles;
            @endphp
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-2">📈 Día más productivo</h4>
                <p class="text-sm text-blue-700">
                    <strong>{{ ucfirst($nombreDiaMasProductivo) }}</strong> 
                    fue el mejor día con <strong>S/ {{ number_format($diaMasProductivo->ventas, 2) }}</strong> 
                    en {{ $diaMasProductivo->pedidos }} pedidos.
                </p>
            </div>
            @endif

            <!-- Insight 2: Producto estrella -->
            @if($reporteProductos['productos_top']->count() > 0)
            @php
                $productoEstrella = $reporteProductos['productos_top']->first();
            @endphp
            <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                <h4 class="font-semibold text-orange-800 mb-2">⭐ Producto estrella</h4>
                <p class="text-sm text-orange-700">
                    <strong>{{ $productoEstrella->nombre }}</strong> 
                    fue el más vendido con {{ $productoEstrella->order_items_sum_cantidad }} 
                    {{ $productoEstrella->medida->nombre ?? 'unidades' }}.
                </p>
            </div>
            @endif

            <!-- Insight 3: Agricultor destacado -->
            @if($reporteAgricultores->count() > 0)
            @php
                $agricultorDestacado = $reporteAgricultores->first();
            @endphp
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h4 class="font-semibold text-green-800 mb-2">🏆 Agricultor destacado</h4>
                <p class="text-sm text-green-700">
                    <strong>{{ $agricultorDestacado->name }}</strong> 
                    lideró las ventas con <strong>S/ {{ number_format($agricultorDestacado->total_ventas, 2) }}</strong>.
                </p>
            </div>
            @endif

            <!-- Insight 4: Tipo de entrega preferido -->
            @if($reporteVentas['por_tipo']->count() > 0)
            @php
                $tipoPreferido = $reporteVentas['por_tipo']->sortByDesc('pedidos')->first();
                $porcentaje = round(($tipoPreferido->pedidos / $estadisticas['total_pedidos']) * 100);
            @endphp
            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                <h4 class="font-semibold text-purple-800 mb-2">🚚 Entrega preferida</h4>
                <p class="text-sm text-purple-700">
                    <strong>{{ $tipoPreferido->delivery === 'delivery' ? 'Delivery' : 'Recojo en puesto' }}</strong> 
                    fue preferido en {{ $tipoPreferido->pedidos }} pedidos 
                    ({{ $porcentaje }}%).
                </p>
            </div>
            @endif

            <!-- Insight 5: Promedio de ventas -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-800 mb-2">📊 Rendimiento promedio</h4>
                <p class="text-sm text-gray-700">
                    El ticket promedio fue de <strong>S/ {{ number_format($estadisticas['promedio_por_pedido'], 2) }}</strong> 
                    con {{ $estadisticas['total_pedidos'] > 0 ? round($estadisticas['productos_vendidos'] / $estadisticas['total_pedidos']) : 0 }} productos por pedido.
                </p>
            </div>

            <!-- Insight 6: Participación de agricultores -->
            @php
                $totalAgricultores = App\Models\User::where('role', 'agricultor')->count();
                $porcentajeParticipacion = $totalAgricultores > 0 ? round(($estadisticas['agricultores_activos'] / $totalAgricultores) * 100) : 0;
            @endphp
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-yellow-800 mb-2">👥 Participación</h4>
                <p class="text-sm text-yellow-700">
                    {{ $estadisticas['agricultores_activos'] }} de {{ $totalAgricultores }} agricultores participaron 
                    ({{ $porcentajeParticipacion }}% de participación).
                </p>
            </div>
        </div>
    </div>

    <!-- Acciones de Reporte -->
    <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center no-print">
        <a href="{{ route('admin.dashboard') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold text-center">
            ← Volver al Dashboard
        </a>
        
        <button onclick="window.print()" 
                class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
            🖨️ Imprimir Reporte
        </button>
        
        <a href="{{ route('admin.pagos.agricultores', ['semana' => $semanaSeleccionada]) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold text-center">
            💰 Ver Pagos de esta Semana
        </a>
    </div>

    @else
    <!-- Sin datos -->
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="text-6xl mb-4">📈</div>
        <h3 class="text-xl font-semibold mb-2 text-gray-600">Sin actividad esta semana</h3>
        <p class="text-gray-500 mb-4">No hay pedidos completados en el período seleccionado ({{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }})</p>
        <p class="text-sm text-gray-400 mb-6">
            Recuerda: Las ventas van de domingo a viernes, y se entregan el sábado {{ $diaEntrega->format('d/m') }} en la feria
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('admin.pedidos.index') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                📦 Ver Todos los Pedidos
            </a>
            <a href="{{ route('admin.pedidos.pagados') }}" 
               class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                💳 Ver Pedidos Pagados
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

// Print functionality
function imprimirReporte() {
    window.print();
}

// Chart functionality (placeholder for future enhancements)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reporte semanal de feria cargado exitosamente');
    
    // Aquí puedes agregar Chart.js o cualquier librería de gráficos en el futuro
    // Por ejemplo:
    // if (typeof Chart !== 'undefined') {
    //     initializeCharts();
    // }
});

// Función para destacar insights importantes
function destacarInsights() {
    const insights = document.querySelectorAll('.insight-card');
    insights.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease-in-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 200);
    });
}

// Llamar a la función cuando la página esté cargada
window.addEventListener('load', destacarInsights);

// Función para mostrar información sobre el flujo de la feria
function mostrarInfoFlujo() {
    alert('Flujo de la Feria Agrícola:\n\n' +
          '📱 Domingo a Viernes: Ventas online\n' +
          '👨‍🌾 Viernes noche: Agricultores preparan pedidos\n' +
          '🚚 Sábado: Entrega en la feria (sin ventas online)\n' +
          '🔄 Domingo: Inicia nueva semana de ventas');
}
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
        background: #7c3aed !important;
        -webkit-print-color-adjust: exact;
        color: white !important;
    }
    
    .shadow-lg {
        box-shadow: none !important;
    }
    
    .rounded-xl {
        border-radius: 8px !important;
    }
    
    /* Asegurar que los colores de fondo se impriman */
    .bg-blue-50,
    .bg-green-50,
    .bg-orange-50,
    .bg-purple-50,
    .bg-gray-50,
    .bg-yellow-50 {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    /* Ajustar tamaños para impresión */
    .text-2xl { font-size: 1.5rem !important; }
    .text-xl { font-size: 1.25rem !important; }
    .text-lg { font-size: 1.125rem !important; }
    
    /* Evitar saltos de página en elementos importantes */
    .bg-white {
        page-break-inside: avoid;
    }
    
    .grid {
        page-break-inside: avoid;
    }
    
    /* Ajustar grillas para impresión */
    .lg\\:grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
    }
    
    .lg\\:grid-cols-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    
    /* Estilos específicos para la información de la feria */
    .border-l-4 {
        border-left: 4px solid !important;
    }
}

/* Estilos adicionales para mejor presentación */
.transition-colors {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
}

.hover\\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Animaciones suaves para los insights */
.insight-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.insight-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.1);
}

/* Mejorar la legibilidad de las tablas de estadísticas */
.bg-white.rounded-xl.shadow-lg {
    transition: transform 0.2s ease-in-out;
}

.bg-white.rounded-xl.shadow-lg:hover {
    transform: translateY(-1px);
}

/* Estilo para los badges de números */
.text-2xl.font-bold {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Estilos específicos para indicadores de días */
.border-l-4.border-green-500 {
    border-left-color: #10b981 !important;
}

.border-l-4.border-red-500 {
    border-left-color: #ef4444 !important;
}

/* Responsive mejoras */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .sm\\:grid-cols-5 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* Estilos para la alerta informativa */
.bg-blue-50 {
    background-color: #eff6ff;
}

.border-blue-200 {
    border-color: #bfdbfe;
}

.text-blue-800 {
    color: #1e40af;
}

.text-blue-700 {
    color: #1d4ed8;
}

.text-blue-400 {
    color: #60a5fa;
}
</style>

@endsection