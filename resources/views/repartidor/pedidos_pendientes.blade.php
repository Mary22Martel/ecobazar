@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    
    <!-- Header simple -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('repartidor.dashboard') }}" 
           class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Dashboard
        </a>
        
        <h1 class="text-2xl sm:text-3xl font-bold text-green-600">🚛 Pedidos Pendientes</h1>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($pedidos->isEmpty())
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 max-w-md mx-auto">
                <svg class="mx-auto h-16 w-16 text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m-2 0v5"></path>
                </svg>
                <h3 class="text-lg font-medium text-blue-800 mb-2">No hay entregas pendientes</h3>
                <p class="text-blue-600">Los pedidos aparecerán aquí cuando el administrador te los asigne.</p>
            </div>
        </div>
    @else
        <!-- Lista de pedidos -->
        <div class="space-y-4">
            @foreach ($pedidos as $pedido)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Header del pedido -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-sm font-medium">
                                Pedido #{{ $pedido->id }}
                            </span>
                            <span class="text-gray-500">{{ $pedido->distrito ?? 'N/A' }}</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900">S/ {{ number_format($pedido->total, 2) }}</span>
                    </div>

                    <!-- Info del cliente -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Cliente:</p>
                            <p class="font-medium">{{ $pedido->nombre }} {{ $pedido->apellido }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Dirección:</p>
                            <p class="font-medium">{{ $pedido->direccion ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('repartidor.pedido.detalle', $pedido->id) }}" 
                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-center transition">
                            Ver Detalles
                        </a>
                        
                        @if(trim(strtolower($pedido->estado)) === 'en_entrega')
                            <form action="{{ route('repartidor.pedido.entregado', $pedido->id) }}" method="POST" 
                                  onsubmit="return confirm('¿Confirmas que entregaste este pedido?')" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition">
                                    ✓ Marcar Entregado
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Estadísticas simples -->
        @if(isset($estadisticas))
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <h3 class="font-semibold text-orange-800 mb-2">📦 Para Entregar</h3>
                <div class="text-2xl font-bold text-orange-700">{{ $estadisticas['en_entrega']['count'] }}</div>
                <div class="text-sm text-orange-600">Total: S/ {{ number_format($estadisticas['en_entrega']['monto'], 2) }}</div>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="font-semibold text-green-800 mb-2">✅ Entregados</h3>
                <div class="text-2xl font-bold text-green-700">{{ $estadisticas['entregado']['count'] }}</div>
                <div class="text-sm text-green-600">Total: S/ {{ number_format($estadisticas['entregado']['monto'], 2) }}</div>
            </div>
        </div>
        @endif
    @endif

    <!-- Botones de navegación -->
    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('repartidor.entregas_completadas') }}" 
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-center transition">
            Ver Entregas Completadas
        </a>
        
        @if(Route::has('repartidor.rutas'))
        <a href="{{ route('repartidor.rutas') }}" 
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-center transition">
            Planificar Rutas
        </a>
        @endif
    </div>

    <!-- Info simple -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 rounded p-4">
        <h3 class="font-medium text-blue-800 mb-2">💡 ¿Cómo funciona?</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Los pedidos aparecen aquí cuando el administrador te los asigna</li>
            <li>• Solo verás pedidos en estado "Para Entregar"</li>
            <li>• Marca como "Entregado" una vez que completes la entrega</li>
        </ul>
    </div>
</div>
@endsection