{{-- resources/views/agricultor/detalle-pagos.blade.php --}}
@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header específico -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📋 Detalle de Mis Ventas</h1>
                <p class="text-green-100 text-base sm:text-lg">
                    {{ $agricultor->name }} - {{ $inicioSemana->format('d/m/Y') }} al {{ $finSemana->format('d/m/Y') }}
                </p>
                <p class="text-green-100 text-sm">
                    🗓️ Para entrega: {{ $diaEntrega->format('l, d/m/Y') }}
                </p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.pagos.agricultores', ['semana' => $semanaSeleccionada]) }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all block mb-2 no-print">
                    ← Volver a Pagos
                </a>
                <div class="text-2xl font-bold">S/ {{ number_format($totalPago, 2) }}</div>
                <div class="text-green-100 text-sm">total a cobrar</div>
            </div>
        </div>
    </div>

    <!-- Información del Agricultor -->
    <div class="mb-6 bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <div class="flex items-center mb-4">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-xl mr-4">
                {{ strtoupper(substr($agricultor->name, 0, 2)) }}
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800">{{ $agricultor->name }}</h3>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                    @if($agricultor->telefono)
                        <div class="flex items-center">
                            <span class="mr-1">📞</span>
                            <span>{{ $agricultor->telefono }}</span>
                        </div>
                    @endif
                    @if($agricultor->email)
                        <div class="flex items-center">
                            <span class="mr-1">📧</span>
                            <span>{{ $agricultor->email }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <div class="text-xl font-bold text-green-600">{{ count($detallesPago) }}</div>
                <div class="text-sm text-green-700">Ventas Realizadas</div>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <div class="text-xl font-bold text-blue-600">{{ collect($detallesPago)->pluck('pedido_id')->unique()->count() }}</div>
                <div class="text-sm text-blue-700">Pedidos Únicos</div>
            </div>
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <div class="text-xl font-bold text-orange-600">{{ collect($detallesPago)->sum('cantidad') }}</div>
                <div class="text-sm text-orange-700">Productos Vendidos</div>
            </div>
            <div class="text-center p-3 bg-purple-50 rounded-lg">
                <div class="text-xl font-bold text-purple-600">{{ collect($detallesPago)->pluck('producto')->unique()->count() }}</div>
                <div class="text-sm text-purple-700">Tipos de Productos</div>
            </div>
        </div>
    </div>

    <!-- Información importante para el agricultor -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Información de Preparación</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>📦 <strong>Etiqueta cada producto</strong> con el número de pedido correspondiente</p>
                    <p>👤 <strong>Incluye el nombre del cliente</strong> para facilitar la entrega</p>
                    <p>📅 <strong>Lleva todo preparado</strong> para el {{ $diaEntrega->format('l d/m') }}</p>
                    <p>💰 <strong>Total a cobrar:</strong> S/ {{ number_format($totalPago, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-bold text-green-800">
                Detalle Completo de Mis Ventas
            </h3>
            <p class="text-sm text-green-600 mt-1">Exactamente lo mismo que ve el administrador</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Pedido</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mi Producto</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Precio Unit.</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mi Ganancia</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($detallesPago as $detalle)
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-green-600 bg-green-100 px-2 py-1 rounded">
                                #{{ $detalle['pedido_id'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-800">{{ $detalle['cliente'] }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-800">{{ $detalle['producto'] }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">
                                {{ $detalle['cantidad'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-gray-600">
                                S/ {{ number_format($detalle['precio_unitario'], 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-green-600">
                                S/ {{ number_format($detalle['subtotal'], 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $detalle['fecha_pedido']->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $detalle['fecha_pedido']->format('H:i') }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">📦</div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-600">Sin ventas esta semana</h3>
                            <p class="text-sm text-gray-500">No tienes ventas en el período seleccionado</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                
                @if(count($detallesPago) > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-right font-semibold text-gray-800">
                            TOTAL QUE DEBO COBRAR:
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xl font-bold text-green-600">
                                S/ {{ number_format($totalPago, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Resumen por Productos -->
    @if(count($detallesPago) > 0)
    <div class="mt-6 bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Resumen por Mis Productos</h3>
        
        @php
            $resumenProductos = collect($detallesPago)->groupBy('producto')->map(function($ventas, $producto) {
                return [
                    'producto' => $producto,
                    'cantidad_total' => $ventas->sum('cantidad'),
                    'total_vendido' => $ventas->sum('subtotal'),
                    'pedidos_count' => $ventas->pluck('pedido_id')->unique()->count(),
                    'precio_promedio' => $ventas->avg('precio_unitario')
                ];
            })->sortByDesc('total_vendido');
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($resumenProductos as $resumen)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <h4 class="font-semibold text-gray-800 mb-3">{{ $resumen['producto'] }}</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Cantidad vendida:</span>
                        <span class="font-medium">{{ $resumen['cantidad_total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Precio promedio:</span>
                        <span class="font-medium">S/ {{ number_format($resumen['precio_promedio'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>En pedidos:</span>
                        <span class="font-medium">{{ $resumen['pedidos_count'] }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span>Mi ganancia:</span>
                        <span class="font-bold text-green-600">S/ {{ number_format($resumen['total_vendido'], 2) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Acciones -->
    <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('admin.pagos.agricultores', ['semana' => $semanaSeleccionada]) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold text-center">
            ← Volver a pagos
        </a>
        
        @if(count($detallesPago) > 0)
        <button onclick="window.print()" 
                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
            🖨️ Imprimir Lista
        </button>
        
       
        @endif
    </div>

</div>

<script>
// Auto-submit form when week selection changes
document.getElementById('semana').addEventListener('change', function() {
    this.form.submit();
});

// Función para marcar/desmarcar todos los checkboxes
function toggleAllCheckboxes() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

// Agregar funcionalidad de lista de preparación
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('.flex');
            if (this.checked) {
                row.classList.add('opacity-75', 'line-through');
            } else {
                row.classList.remove('opacity-75', 'line-through');
            }
        });
    });
});

// Función para mostrar ayuda sobre preparación
function mostrarAyudaPreparacion() {
    alert('Consejos para preparar tus productos:\n\n' +
          '1. Etiqueta cada producto con el número de pedido\n' +
          '2. Incluye el nombre del cliente en la etiqueta\n' +
          '3. Verifica las cantidades antes de empacar\n' +
          '4. Lleva una copia de este detalle impreso\n' +
          '5. Confirma el total a cobrar: S/ {{ number_format($totalPago, 2) }}');
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
        background: #10b981 !important;
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
    .bg-yellow-50,
    .bg-yellow-100 {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    /* Evitar saltos de página en elementos importantes */
    .bg-white {
        page-break-inside: avoid;
    }
    
    .grid {
        page-break-inside: avoid;
    }
    
    /* Ajustar tabla para impresión */
    table {
        font-size: 11px;
    }
    
    .text-2xl { font-size: 1.5rem !important; }
    .text-xl { font-size: 1.25rem !important; }
    .text-lg { font-size: 1.125rem !important; }
}

/* Estilos para la lista de preparación */
.line-through {
    text-decoration: line-through;
}

.opacity-75 {
    opacity: 0.75;
}

/* Estilos para checkboxes */
input[type="checkbox"]:checked {
    background-color: #10b981;
    border-color: #10b981;
}

/* Transiciones suaves */
.transition-colors {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
}

.transition-shadow {
    transition: box-shadow 0.2s ease-in-out;
}

.hover\\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Estilos para alertas */
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

.bg-yellow-50 {
    background-color: #fefce8;
}

.border-yellow-200 {
    border-color: #fde047;
}

.text-yellow-800 {
    color: #92400e;
}

.bg-yellow-100 {
    background-color: #fef3c7;
}

/* Responsive mejoras */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .sm\\:grid-cols-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    /* Tabla responsive */
    .overflow-x-auto {
        font-size: 0.875rem;
    }
}
</style>

@endsection