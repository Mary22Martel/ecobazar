@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Pagos a Repartidores</h1>
            <p class="mt-2 text-sm md:text-base text-gray-600">
                Gestión de pagos por entregas del: 
                <span class="font-semibold text-blue-600">{{ isset($diaEntrega) ? $diaEntrega->format('d/m/Y') : 'No definido' }}</span>
            </p>
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

        <!-- Resumen Total -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 md:p-8 text-white mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold">Total a Pagar</h2>
                    <p class="text-blue-100 mt-1">
                        {{ count($pagosRepartidores) }} repartidores con entregas
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="text-3xl md:text-4xl font-bold">
                        S/ {{ number_format($totalGeneralAPagar, 2) }}
                    </div>
                    <div class="text-blue-100 text-right">Monto total</div>
                </div>
            </div>
        </div>

        <!-- Lista de Repartidores -->
        @if(count($pagosRepartidores) > 0)
            <div class="space-y-6">
                @foreach($pagosRepartidores as $pagoData)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $pagoData['repartidor']->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pagoData['total_entregas'] }} entregas completadas
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4 mt-3 sm:mt-0">
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-blue-600">
                                            S/ {{ number_format($pagoData['total_pago'], 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Total a pagar</div>
                                    </div>
                                    <a href="{{ route('admin.repartidores.detalle', ['repartidor' => $pagoData['repartidor']->id, 'semana' => request('semana', 0)]) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                        Ver Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Desglose por zona -->
                        <div class="p-4 md:p-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Desglose por Zona</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($pagoData['entregas_por_zona'] as $zona => $datos)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $zona }}</div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ $datos['entregas'] }} entregas × S/ {{ number_format($datos['tarifa'], 2) }}
                                        </div>
                                        <div class="text-sm font-semibold text-blue-600 mt-2">
                                            S/ {{ number_format($datos['total'], 2) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay entregas para esta semana</h3>
                <p class="text-gray-600">
                    No se encontraron repartidores con entregas completadas para la semana seleccionada.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
// Auto-submit para filtro de semana
document.addEventListener('DOMContentLoaded', function() {
    const selectSemana = document.getElementById('semana');
    if (selectSemana) {
        selectSemana.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endsection