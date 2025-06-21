@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    <!-- Header responsive mejorado -->
    <div class="bg-gradient-to-r from-orange-400 to-amber-500 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-xl sm:text-2xl font-bold mb-1">📦PEDIDOS POR ARMAR</h1>
                <p class="text-orange-100 text-sm sm:text-base">
                    📅 {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }} <br>
                    (Entrega: {{ $diaEntrega->format('d/m/Y') }})
                </p>
            </div>
            <a href="{{ route('agricultor.dashboard') }}" 
               class="bg-white/20 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white/30 transition-all text-sm sm:text-base text-center">
                ← Volver al inicio
            </a>
        </div>
    </div>

    <!-- FILTRO DE SEMANAS - Responsive mejorado -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 filtro-container">
            <form method="GET" action="{{ route('agricultor.pedidos_pendientes') }}" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                
                <!-- Label y select en móvil -->
                <div class="flex-1 space-y-2 sm:space-y-0 min-w-0">
                    <label for="semana" class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span class="hidden sm:inline">Filtrar por Semana de Feria</span>
                        <span class="sm:hidden truncate">Semana de Feria</span>
                    </label>
                    
                    <!-- Select mejorado para móvil -->
                    <div class="relative">
                        <select name="semana" id="semana" 
                                class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2.5 sm:py-2 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm bg-white shadow-sm overflow-hidden text-ellipsis">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ $semanaSeleccionada === $valor ? 'selected' : '' }}>
                                    @if(strlen($label) > 25)
                                        {{ substr($label, 0, 22) }}...
                                    @else
                                        {{ $label }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <!-- Icono de dropdown personalizado -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Botón responsive -->
                <div class="sm:flex-shrink-0">
                    <button type="submit" 
                            class="w-full sm:w-auto bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all duration-200 font-semibold text-sm flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sm:hidden">Filtrar Semana</span>
                        <span class="hidden sm:inline">Ver Semana</span>
                    </button>
                </div>
            </form>
            
            <!-- Indicador de semana actual en móvil -->
            <div class="mt-3 sm:hidden">
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-2">
                    <div class="flex items-center text-xs text-orange-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">Mostrando:</span>
                        <span class="ml-1">{{ $opcionesSemanas[$semanaSeleccionada] ?? 'Esta semana' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pedidos->whereIn('estado', ['pendiente', 'pagado'])->isEmpty())
        <!-- Estado vacío responsive -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-6 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">¡Aún no hay pedidos pendientes!</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base">
                    Del {{ $fechaInicio->format('d/m') }} al {{ $fechaFin->format('d/m') }} no tienes pedidos por preparar
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('agricultor.pedidos_listos', ['semana' => $semanaSeleccionada]) }}"
                        class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Ver Listos
                        </a>

                        <a href="{{ route('agricultor.dashboard') }}"
                        class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Inicio
                        </a>
                </div>
            </div>
        </div>
    @else
        <!-- Lista de pedidos responsive -->
        <div class="space-y-4 sm:space-y-6">
            @foreach($pedidos->whereIn('estado', ['pendiente', 'pagado']) as $pedido)
                @php
                    $misProductos = $pedido->items->where('product.user_id', Auth::id());
                @endphp
                
                @if($misProductos->count() > 0)
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                    
                    <!-- Header del pedido responsive -->
                    <div class="bg-gradient-to-r from-orange-400 to-amber-500 p-4 sm:p-6 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2 sm:p-3 flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold">PEDIDO #{{ $pedido->id }}</h2>
                                    <p class="text-orange-100 text-sm sm:text-base">
                                        @if($pedido->estado === 'pagado')
                                            ✅ Listo para preparar
                                        @else
                                            ⏳ Esperando pago
                                        @endif
                                    </p>
                                    <p class="text-orange-200 text-xs sm:text-sm">
                                        📅 {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del pedido responsive -->
                    <div class="p-4 sm:p-6">
                        <!-- Etiqueta del cliente -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border border-blue-100">
                            <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-2 text-center">🏷️ ETIQUETA PARA ESTE PEDIDO</h3>
                            <div class="bg-white border-2 border-dashed border-gray-300 p-3 sm:p-4 rounded-lg text-center">
                                <div class="text-lg sm:text-xl font-bold text-blue-600 mb-1">
                                    {{ strtoupper($pedido->nombre . ' ' . $pedido->apellido) }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600">
                                    Pedido #{{ $pedido->id}}
                                </div>
                            </div>
                            <p class="text-center text-xs sm:text-sm text-blue-700 mt-2">
                                ☝️ Copia esta información en una etiqueta
                            </p>
                        </div>

                        <!-- Productos a preparar -->
                        <div class="mb-4 sm:mb-6">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🥕</span> Lo que debes preparar:
                            </h3>
                            <div class="space-y-2 sm:space-y-3">
                                @foreach($misProductos as $item)
                                <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <h4 class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $item->product->nombre }}</h4>
                                            <p class="text-gray-500 text-xs sm:text-sm">
                                                {{ $item->product->categoria->nombre ?? 'Producto agrícola' }}
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $item->cantidad }}</div>
                                            <div class="text-gray-600 font-medium text-xs sm:text-sm">
                                                {{ $item->product->medida->nombre ?? 'unidades' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Botones de acción responsive -->
                        <div class="space-y-3">
                            @if($pedido->estado === 'pagado')
                            <button type="button" 
                                    data-pedido-id="{{ $pedido->id }}" 
                                    data-cliente-nombre="{{ $pedido->nombre }}" 
                                    data-cliente-apellido="{{ $pedido->apellido }}"
                                    class="btn-confirmar-pedido w-full bg-gradient-to-r from-green-500 to-green-600 text-white text-base sm:text-lg font-bold py-3 sm:py-4 rounded-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg">
                                ✅ YA ESTÁ LISTO
                            </button>
                            @elseif($pedido->estado === 'pendiente')
                            <div class="w-full bg-gradient-to-r from-yellow-100 to-amber-100 border-2 border-yellow-300 text-yellow-800 text-xs sm:text-lg font-bold py-3 sm:py-4 rounded-lg text-center">
                                ⏳ ESPERANDO PAGO DEL CLIENTE
                            </div>
                            @endif

                            <!-- Botón para ver más detalles -->
                            <a href="{{ route('agricultor.pedido_detalle', $pedido->id) }}" 
                               class="w-full bg-gradient-to-r from-gray-600 to-gray-700 text-white text-sm sm:text-base font-semibold py-3 rounded-lg hover:from-gray-700 hover:to-gray-800 text-center transition-all flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Ver todos los detalles
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Instrucciones simples -->
        <div class="mt-8 sm:mt-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4 text-center">📋 QUÉ HACER:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">📦</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">1. Prepara</p>
                    <p class="text-xs text-gray-600">Cada producto con la cantidad exacta</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">🏷️</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">2. Etiqueta</p>
                    <p class="text-xs text-gray-600">Pega la etiqueta en cada pedido</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">✅</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">3. Confirma</p>
                    <p class="text-xs text-gray-600">Presiona "YA ESTÁ LISTO"</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                    <div class="text-2xl mb-2">🚚</div>
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 mb-1">4. Lleva</p>
                    <p class="text-xs text-gray-600">Todo a la feria el {{ $diaEntrega->format('d/m') }}</p>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Formulario oculto para confirmar pedido -->
<form id="confirmarPedidoForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<!-- Pasar la URL base a JavaScript -->
<script>
    window.confirmarPedidoUrl = "{{ route('agricultor.confirmar_pedido_listo', ':id') }}";
</script>

<style>
/* Mejoras específicas para el select en móvil */
@media (max-width: 640px) {
    select {
        font-size: 16px; /* Evita zoom en iOS */
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    /* Mejora del botón en móvil */
    .filter-button {
        min-height: 44px; /* Área de toque recomendada */
    }
    
    /* Contenedor del filtro más estrecho en móvil */
    .filtro-container {
        overflow: hidden;
    }
    
    /* Opciones del select más cortas */
    select option {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}

/* Animación suave para el cambio de semana */
.week-transition {
    transition: all 0.3s ease-in-out;
}

/* Estado focus mejorado */
select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
}

/* Hover states para desktop */
@media (min-width: 641px) {
    select:hover {
        border-color: #f97316;
    }
}

/* ===== ESTILOS PERSONALIZADOS PARA SWEETALERT2 MODAL COMPACTO ===== */
.swal-custom-popup {
    border-radius: 12px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.swal-custom-title {
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    color: #1f2937 !important;
    margin-bottom: 10px !important;
}

.swal-custom-content {
    margin: 10px 0 !important;
    font-size: 0.9rem !important;
    color: #4b5563 !important;
    line-height: 1.5 !important;
}

/* Alineación horizontal de botones */
.swal-actions-horizontal {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center !important;
    gap: 15px !important;
    margin-top: 20px !important;
}

.swal-actions-horizontal .swal2-confirm,
.swal-actions-horizontal .swal2-cancel {
    flex: 0 0 auto !important;
    margin: 0 !important;
    min-width: 120px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
}

/* Estilos específicos para cada botón */
.swal2-confirm {
    background: linear-gradient(to right, #10b981, #059669) !important;
    border: none !important;
}

.swal2-cancel {
    background: linear-gradient(to right, #6b7280, #4b5563) !important;
    border: none !important;
}

/* Efectos hover */
.swal2-confirm:hover {
    background: linear-gradient(to right, #059669, #047857) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
}

.swal2-cancel:hover {
    background: linear-gradient(to right, #4b5563, #374151) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
}

/* Icono del modal */
.swal2-icon.swal2-question {
    border-color: #f59e0b !important;
    color: #f59e0b !important;
}

/* Responsive para móvil */
@media (max-width: 480px) {
    .swal-actions-horizontal {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .swal-actions-horizontal .swal2-confirm,
    .swal-actions-horizontal .swal2-cancel {
        width: 100% !important;
        min-width: auto !important;
    }
}
</style>

<script>
// Auto-submit mejorado con indicador de carga
document.getElementById('semana').addEventListener('change', function() {
    const button = this.form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    // Mostrar estado de carga
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="sm:hidden">Cargando...</span>
        <span class="hidden sm:inline">Filtrando...</span>
    `;
    
    button.disabled = true;
    
    // Submit el formulario
    this.form.submit();
});

// Restaurar estado si hay error
window.addEventListener('pageshow', function() {
    const button = document.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = false;
    }
});

// Función para confirmar pedido listo con SweetAlert2 - Modal compacto
function confirmarPedidoListo(pedidoId, nombre, apellido) {
    Swal.fire({
        title: '¿Pedido Completado?',
        html: 
            `<div class="text-left space-y-2">
                <p class="font-medium text-gray-700 text-sm">✅ Confirma que tienes listo:</p>
                <ul class="text-sm text-gray-600 space-y-1 ml-4">
                    <li>• Todos los productos preparados</li>
                    <li>• Las cantidades correctas</li>
                    <li>• La etiqueta pegada</li>
                </ul>
            </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '✅ Sí, está listo',
        cancelButtonText: '❌ Aún no',
        reverseButtons: true,
        focusConfirm: false,
        allowOutsideClick: false,
        // Configuración para modal más pequeño
        width: '400px',
        padding: '20px',
        customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            htmlContainer: 'swal-custom-content',
            actions: 'swal-actions-horizontal'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Confirmando pedido...',
                html: 'Por favor espera un momento',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                width: '350px',
                padding: '20px',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Configurar y enviar formulario
            const form = document.getElementById('confirmarPedidoForm');
            const url = window.confirmarPedidoUrl.replace(':id', pedidoId);
            form.action = url;
            form.submit();
        }
    });
}

// Event listeners para los botones
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listeners a todos los botones de confirmar
    const botonesConfirmar = document.querySelectorAll('.btn-confirmar-pedido');
    
    botonesConfirmar.forEach(boton => {
        boton.addEventListener('click', function() {
            const pedidoId = this.dataset.pedidoId;
            const nombre = this.dataset.clienteNombre;
            const apellido = this.dataset.clienteApellido;
            
            if (typeof Swal !== 'undefined') {
                confirmarPedidoListo(pedidoId, nombre, apellido);
            } else {
                // Fallback si SweetAlert2 no está disponible
                if (confirm(`¿Ya tienes todo listo para ${nombre} ${apellido}?\n\n✅ Al confirmar se marcará como LISTO`)) {
                    const form = document.getElementById('confirmarPedidoForm');
                    const url = window.confirmarPedidoUrl.replace(':id', pedidoId);
                    form.action = url;
                    form.submit();
                }
            }
        });
    });
    
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado. Asegúrate de incluir la librería en tu layout.');
    }
});
</script>

@endsection