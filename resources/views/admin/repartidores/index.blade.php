@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestión de Repartidores</h1>
            <p class="mt-2 text-sm md:text-base text-gray-600">
                Asignar zonas a repartidores para entrega del: 
                <span class="font-semibold text-green-600 block sm:inline">{{ isset($diaEntrega) ? $diaEntrega->format('d/m/Y') : 'No definido' }}</span>
            </p>
        </div>

        <!-- Filtro de Semanas (si existe) -->
        @if(isset($opcionesSemanas))
        <div class="mb-4 md:mb-6">
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="mb-3">
                    <label for="semana" class="block text-sm font-medium text-gray-700 mb-2">
                        Filtrar por Semana de Feria
                    </label>
                </div>
                <form method="GET" action="{{ route('admin.repartidores.index') }}" class="space-y-3">
                    <div class="w-full">
                        <select name="semana" id="semana" 
                                class="w-full border border-gray-300 rounded-md px-2 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white appearance-none">
                            @foreach($opcionesSemanas as $valor => $label)
                                <option value="{{ $valor }}" {{ request('semana', 0) == $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full">
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors text-sm font-medium">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Estadísticas -->
        @if(isset($estadisticas))
        <div class="mb-4 md:mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-green-800">📋 Resumen de Asignaciones</h3>
                    <p class="text-sm text-green-600">
                        Gestión para entrega del {{ isset($diaEntrega) ? $diaEntrega->format('d/m/Y') : 'No definido' }}
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-2xl font-bold text-green-700">{{ $estadisticas['total_pedidos'] ?? 0 }}</div>
                    <div class="text-xs text-green-600">Pedidos para entregar</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Mensajes -->
        @if(session('success'))
            <div class="mb-4 md:mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 md:mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Estadísticas Rápidas -->
        @if(isset($estadisticas))
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-blue-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Pedidos</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $estadisticas['total_pedidos'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-green-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Repartidores</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $estadisticas['repartidores_activos'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-purple-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Zonas</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $estadisticas['zonas_cubiertas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-blue-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">En Sistema</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $estadisticas['pedidos_sistema'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Lista de Repartidores -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Repartidores Disponibles</h2>
                <p class="text-sm text-gray-600 mt-1">Asigna zonas a cada repartidor</p>
            </div>
            
            <div class="divide-y divide-gray-200">
                @if(isset($repartidores) && count($repartidores) > 0)
                    @foreach($repartidores as $repartidor)
                        <div class="p-4 md:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-green-600 font-semibold text-sm">
                                                {{ substr($repartidor->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base md:text-lg font-medium text-gray-900 truncate">{{ $repartidor->name }}</h3>
                                        <p class="text-sm text-gray-600 truncate">{{ $repartidor->email }}</p>
                                        @if($repartidor->zones->count() > 0)
                                            <div class="mt-2">
                                                <span class="text-xs text-gray-500">Zonas asignadas:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($repartidor->zones as $zona)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <span class="max-w-20 sm:max-w-none truncate">{{ $zona->name }}</span>
                                                            <button onclick="quitarZona('{{ $repartidor->id }}', '{{ $zona->id }}', '{{ $zona->name }}')"
                                                                    class="ml-1 text-blue-600 hover:text-blue-800 flex-shrink-0">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <button 
                                        onclick="abrirModal('{{ $repartidor->id }}', '{{ $repartidor->name }}')"
                                        class="inline-flex items-center justify-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Asignar Zonas
                                    </button>
                                    
                                    <a href="{{ route('admin.repartidores.detalle', $repartidor->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Ver Detalle</span>
                                        <span class="sm:hidden">Detalle</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay repartidores disponibles</h3>
                        <p class="text-gray-600">Crea repartidores desde la gestión de usuarios</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información del Sistema -->
        @if(isset($repartidorSistema))
            <div class="mt-6 md:mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <svg class="w-8 h-8 text-blue-600 mb-3 sm:mb-0 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">{{ $repartidorSistema->name }}</h3>
                        <p class="text-blue-700 text-sm md:text-base">Repartidor base del sistema - Recibe todos los pedidos inicialmente</p>
                        <p class="text-sm text-blue-600 mt-1">
                            Pedidos actuales en sistema: {{ $estadisticas['pedidos_sistema'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 p-4">
    <div class="flex items-center justify-center min-h-full">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Asignar Zonas</h3>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="formAsignar" method="POST" action="{{ route('admin.repartidores.asignar') }}">
                    @csrf
                    <input type="hidden" id="repartidorId" name="repartidor_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Selecciona las zonas para el {{ isset($diaEntrega) ? $diaEntrega->format('d/m/Y') : '' }}:
                        </label>
                        <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                            @if(isset($zonasDisponibles))
                                @foreach($zonasDisponibles as $zona)
                                    <label class="flex items-start mb-2 cursor-pointer">
                                        <input type="checkbox" name="zonas[]" value="{{ $zona->id }}" class="mt-1 mr-3 text-green-600 focus:ring-green-500 flex-shrink-0">
                                        <span class="text-sm text-gray-700">
                                            {{ $zona->name }} - S/{{ number_format($zona->delivery_cost, 2) }}
                                        </span>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="cerrarModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            Asignar Zonas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form para quitar zona -->
<form id="formQuitar" method="POST" action="{{ route('admin.repartidores.quitar') }}" class="hidden">
    @csrf
    <input type="hidden" id="quitRepartidorId" name="repartidor_id">
    <input type="hidden" id="quitZonaId" name="zona_id">
</form>

<script>
function abrirModal(repartidorId, nombre) {
    console.log('Abriendo modal para:', repartidorId, nombre);
    
    document.getElementById('repartidorId').value = repartidorId;
    document.getElementById('modalTitle').textContent = 'Asignar Zonas a ' + nombre;
    
    // Limpiar checkboxes
    const checkboxes = document.querySelectorAll('input[name="zonas[]"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    document.getElementById('modal').classList.remove('hidden');
    
    // Evitar scroll del body cuando el modal está abierto
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    console.log('Cerrando modal');
    document.getElementById('modal').classList.add('hidden');
    
    // Restaurar scroll del body
    document.body.style.overflow = '';
}

function quitarZona(repartidorId, zonaId, zonaNombre) {
    console.log('Quitando zona:', repartidorId, zonaId, zonaNombre);
    
    if (confirm('¿Estás seguro de quitar la zona "' + zonaNombre + '"? Los pedidos de esta zona volverán al sistema.')) {
        document.getElementById('quitRepartidorId').value = repartidorId;
        document.getElementById('quitZonaId').value = zonaId;
        document.getElementById('formQuitar').submit();
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

// Auto-submit para filtro de semana
document.addEventListener('DOMContentLoaded', function() {
    const selectSemana = document.getElementById('semana');
    if (selectSemana) {
        selectSemana.addEventListener('change', function() {
            this.form.submit();
        });
    }
});

// Prevenir cierre accidental del modal en móvil
document.addEventListener('touchmove', function(e) {
    if (document.getElementById('modal').classList.contains('hidden') === false) {
        e.preventDefault();
    }
}, { passive: false });
</script>
@endsection