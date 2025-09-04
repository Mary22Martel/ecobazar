@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        
        <!-- Header con botón de regreso -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 sm:mb-8 gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('repartidor.pedidos_pendientes') }}" 
                   class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-600">
                        Pedido #{{ $pedido->id }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Estado: 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($pedido->estado === 'entregado') bg-green-100 text-green-800
                            @elseif($pedido->estado === 'en_entrega') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Acciones del pedido -->
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                @if($pedido->estado === 'armado')
                    <form method="POST" action="{{ route('repartidor.pedido.proceso', $pedido->id) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Iniciar Entrega
                        </button>
                    </form>
                @elseif($pedido->estado === 'en_entrega')
                    <form method="POST" action="{{ route('repartidor.pedido.entregado', $pedido->id) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Marcar como Entregado
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Grid de información -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Información del Cliente -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Información del Cliente
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500">Nombre completo</p>
                                        <p class="font-medium text-gray-900">{{ $pedido->nombre }} {{ $pedido->apellido }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500">Correo electrónico</p>
                                        <p class="font-medium text-gray-900 break-all">{{ $pedido->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500">Teléfono</p>
                                        <p class="font-medium text-gray-900">{{ $pedido->telefono ?: 'No disponible' }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-500">Dirección de entrega</p>
                                        <p class="font-medium text-gray-900">{{ $pedido->direccion }}</p>
                                        @if($pedido->distrito)
                                            <p class="text-sm text-green-600 font-medium mt-1">{{ $pedido->distrito }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen del Pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Resumen
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Fecha del pedido</span>
                            <span class="font-medium">{{ $pedido->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Hora</span>
                            <span class="font-medium">{{ $pedido->created_at->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tipo de entrega</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($pedido->delivery === 'delivery') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $pedido->delivery === 'delivery' ? 'Delivery' : 'Recojo en puesto' }}
                            </span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center text-lg font-semibold">
                                <span>Total del pedido</span>
                                <span class="text-green-600">S/ {{ number_format($pedido->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos del Pedido -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Productos del Pedido ({{ $pedido->items->count() }} items)
                </h2>
            </div>
            
            <!-- Tabla en desktop -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agricultor</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pedido->items as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->nombre }}</div>
                                            @if($item->product->categoria)
                                                <div class="text-sm text-gray-500">{{ $item->product->categoria->nombre }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $item->cantidad }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    S/ {{ number_format($item->precio, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="ml-2">
                                            @if ($item->product->user)
                                                {{ $item->product->user->name }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                    S/ {{ number_format($item->precio * $item->cantidad, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards en mobile -->
            <div class="sm:hidden divide-y divide-gray-200">
                @foreach($pedido->items as $item)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->nombre }}</h3>
                                    <span class="text-lg font-semibold text-green-600 ml-2">S/ {{ number_format($item->precio * $item->cantidad, 2) }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-sm text-gray-500">
                                    <span>Cantidad: <span class="font-medium text-gray-900">{{ $item->cantidad }}</span></span>
                                    <span>Precio: <span class="font-medium">S/ {{ number_format($item->precio, 2) }}</span></span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 text-yellow-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="truncate">
                                        @if ($item->product->user)
                                            {{ $item->product->user->name }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Total -->
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex justify-between items-center text-lg font-semibold">
                    <span class="text-gray-700">Total del pedido</span>
                    <span class="text-green-600">S/ {{ number_format($pedido->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection