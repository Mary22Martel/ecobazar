@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold text-gray-900">
                        <svg class="w-8 h-8 inline mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Entregas Completadas
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Historial de entregas realizadas exitosamente
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('repartidor.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-md bg-green-50">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Entregadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalEntregadas }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-md bg-blue-50">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hoy</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $entregadasHoy }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-md bg-purple-50">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Esta Semana</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $entregadasSemana }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Zona (si tiene zonas asignadas) -->
        @if($zonasAsignadas->count() > 0)
        <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Mis Zonas de Entrega</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($zonasAsignadas as $zona)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $zona->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Lista de Entregas Completadas -->
        <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Historial de Entregas</h2>
                <p class="text-sm text-gray-600 mt-1">Entregas realizadas exitosamente</p>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($pedidos as $pedido)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            Pedido #{{ $pedido->id }}
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entregado
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Cliente:</span> 
                                                {{ $pedido->nombre }} {{ $pedido->apellido }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Teléfono:</span> 
                                                {{ $pedido->telefono }}
                                            </p>
                                            @if($pedido->delivery === 'delivery')
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Dirección:</span> 
                                                    {{ $pedido->direccion }}, {{ $pedido->distrito }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Tipo:</span> 
                                                    Recojo en feria
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Fecha Pedido:</span> 
                                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Fecha Entrega:</span> 
                                                {{ $pedido->updated_at->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-sm font-medium text-green-600">
                                                Total: S/{{ number_format($pedido->total, 2) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Productos del pedido -->
                                    <div class="mt-3">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Productos entregados:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($pedido->items as $item)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $item->cantidad }} {{ $item->product->medida ? $item->product->medida->nombre : 'und' }} 
                                                    de {{ $item->product->nombre }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-2">
                                <a href="{{ route('repartidor.pedido.detalle', $pedido->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay entregas completadas</h3>
                        <p class="text-gray-600">Cuando completes entregas aparecerán aquí</p>
                        <a href="{{ route('repartidor.pedidos_pendientes') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Ver Pedidos Pendientes
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($pedidos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection