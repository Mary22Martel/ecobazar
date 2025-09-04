@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-2 sm:mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Entregas Completadas
                    </h1>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">
                        Historial de entregas realizadas exitosamente
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('repartidor.dashboard') }}" 
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-md bg-green-50 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Entregadas</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalEntregadas }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-md bg-blue-50 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Hoy</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $entregadasHoy }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-md bg-purple-50 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Esta Semana</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $entregadasSemana }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Zona -->
        @if($zonasAsignadas->count() > 0)
        <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Mis Zonas de Entrega</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($zonasAsignadas as $zona)
                    <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Historial de Entregas</h2>
                <p class="text-sm text-gray-600 mt-1">Entregas realizadas exitosamente</p>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($pedidos as $pedido)
                    <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                        <!-- Mobile Layout -->
                        <div class="block sm:hidden">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">Pedido #{{ $pedido->id }}</h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entregado
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-lg font-semibold text-green-600">S/{{ number_format($pedido->total, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $pedido->nombre }} {{ $pedido->apellido }}</p>
                                        <p class="text-sm text-gray-600">{{ $pedido->telefono }}</p>
                                    </div>
                                </div>
                                
                                @if($pedido->delivery === 'delivery')
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-sm text-gray-600 break-words">{{ $pedido->direccion }}</p>
                                            <p class="text-sm font-medium text-blue-600">{{ $pedido->distrito }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0l-2-2M5 21l2-2"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Recojo en feria</span>
                                    </div>
                                @endif
                                
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Pedido: {{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm text-green-600 font-medium">Entregado: {{ $pedido->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Productos móvil -->
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Productos entregados:</p>
                                <div class="space-y-1">
                                    @foreach($pedido->items as $item)
                                        <div class="text-xs bg-gray-100 rounded px-2 py-1">
                                            {{ $item->cantidad }} {{ $item->product->medida ? $item->product->medida->nombre : 'und' }} 
                                            de {{ $item->product->nombre }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <a href="{{ route('repartidor.pedido.detalle', $pedido->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalle
                                </a>
                            </div>
                        </div>

                        <!-- Desktop Layout -->
                        <div class="hidden sm:flex items-center justify-between">
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
                        <p class="text-gray-600 mb-4">Cuando completes entregas aparecerán aquí</p>
                        <a href="{{ route('repartidor.pedidos_pendientes') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Ver Pedidos Pendientes
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($pedidos->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection