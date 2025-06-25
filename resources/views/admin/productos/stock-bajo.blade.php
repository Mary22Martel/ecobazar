@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-7xl">
    
    <!-- Header Responsivo -->
    <div class="bg-red-200 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2">⚠️ Productos con Stock Crítico</h1>
                <p class="text-red-600 text-sm sm:text-base lg:text-lg">Inventario que requiere atención inmediata</p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-2xl sm:text-3xl font-bold">{{ $productosSinStock->count() + $productosStockBajo->count() }}</div>
                <div class="text-red-600 text-xs sm:text-sm">productos críticos</div>
            </div>
        </div>
    </div>
    
    <!-- Botón de regreso -->
    <a href="{{ route('admin.dashboard') }}" 
       class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium mb-4 sm:mb-6 text-sm sm:text-base">
        ← Volver al Dashboard
    </a>

    <!-- Resumen en Cards (Solo móvil) -->
    <div class="grid grid-cols-2 gap-3 mb-6 sm:hidden">
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-red-600">{{ $productosSinStock->count() }}</div>
            <div class="text-xs text-red-500">Sin Stock</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-yellow-600">{{ $productosStockBajo->count() }}</div>
            <div class="text-xs text-yellow-500">Stock Bajo</div>
        </div>
    </div>

    <!-- Productos Sin Stock -->
    @if($productosSinStock->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-4 sm:mb-6">
        <div class="p-3 sm:p-4 lg:p-6 border-b border-gray-200 bg-red-50">
            <h3 class="text-base sm:text-lg font-bold text-red-800 flex items-center">
                <span class="mr-2">🚫</span> Sin Stock ({{ $productosSinStock->count() }})
            </h3>
            <p class="text-xs sm:text-sm text-red-600 mt-1">Productos no disponibles para venta</p>
        </div>
        
        <!-- Vista de Tabla (Desktop) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Producto</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Agricultor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Categoría</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Precio</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($productosSinStock as $producto)
                    <tr class="hover:bg-red-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                         alt="{{ $producto->nombre }}" 
                                         class="w-12 h-12 rounded-lg object-cover mr-3 flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-gray-400 text-xs">📦</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $producto->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ $producto->medida->nombre ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900 truncate">{{ $producto->user->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $producto->user->email }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-semibold text-green-600">
                                S/ {{ number_format($producto->precio, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                0 unidades
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Vista de Cards (Móvil y Tablet) -->
        <div class="lg:hidden">
            @foreach($productosSinStock as $producto)
            <div class="border-b border-gray-200 last:border-b-0">
                <div class="p-3 sm:p-4 hover:bg-red-50">
                    <!-- Header del producto -->
                    <div class="flex items-start gap-3 mb-3">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-gray-400 text-lg">📦</span>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 leading-tight">
                                {{ $producto->nombre }}
                            </h4>
                            <p class="text-xs sm:text-sm text-gray-500 mb-2">
                                {{ $producto->medida->nombre ?? 'N/A' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg sm:text-xl font-bold text-green-600">
                                    S/ {{ number_format($producto->precio, 2) }}
                                </span>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    SIN STOCK
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info adicional -->
                    <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                        <div>
                            <span class="text-gray-500">Agricultor:</span>
                            <div class="font-medium text-gray-900 truncate">{{ $producto->user->name }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Categoría:</span>
                            <div class="font-medium text-gray-900">
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Productos Stock Bajo -->
    @if($productosStockBajo->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-base sm:text-lg font-bold text-yellow-800 flex items-center">
                <span class="mr-2">⚠️</span> Stock Bajo ({{ $productosStockBajo->count() }})
            </h3>
            <p class="text-xs sm:text-sm text-yellow-600 mt-1">Menos de 5 unidades disponibles</p>
        </div>
        
        <!-- Vista de Tabla (Desktop) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Producto</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Agricultor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Categoría</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Precio</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($productosStockBajo as $producto)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                         alt="{{ $producto->nombre }}" 
                                         class="w-12 h-12 rounded-lg object-cover mr-3 flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-gray-400 text-xs">📦</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $producto->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ $producto->medida->nombre ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900 truncate">{{ $producto->user->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $producto->user->email }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-lg font-semibold text-green-600">
                                S/ {{ number_format($producto->precio, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $producto->cantidad_disponible }} unidades
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Vista de Cards (Móvil y Tablet) -->
        <div class="lg:hidden">
            @foreach($productosStockBajo as $producto)
            <div class="border-b border-gray-200 last:border-b-0">
                <div class="p-3 sm:p-4 hover:bg-yellow-50">
                    <!-- Header del producto -->
                    <div class="flex items-start gap-3 mb-3">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-gray-400 text-lg">📦</span>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 leading-tight">
                                {{ $producto->nombre }}
                            </h4>
                            <p class="text-xs sm:text-sm text-gray-500 mb-2">
                                {{ $producto->medida->nombre ?? 'N/A' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg sm:text-xl font-bold text-green-600">
                                    S/ {{ number_format($producto->precio, 2) }}
                                </span>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ $producto->cantidad_disponible }} unidades
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info adicional -->
                    <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                        <div>
                            <span class="text-gray-500">Agricultor:</span>
                            <div class="font-medium text-gray-900 truncate">{{ $producto->user->name }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Categoría:</span>
                            <div class="font-medium text-gray-900">
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Estado vacío -->
    @if($productosSinStock->count() == 0 && $productosStockBajo->count() == 0)
    <div class="text-center py-8 sm:py-12">
        <div class="text-4xl sm:text-6xl mb-4">✅</div>
        <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2">¡Todo el inventario está bien!</h3>
        <p class="text-sm sm:text-base text-gray-500 px-4">No hay productos con stock crítico en este momento.</p>
    </div>
    @endif

</div>

<style>
/* Estilos adicionales para mejorar la responsividad */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* Animaciones suaves */
.hover\:bg-red-50:hover,
.hover\:bg-yellow-50:hover {
    transition: background-color 0.15s ease-in-out;
}

/* Mejora del truncate en móvil */
@media (max-width: 768px) {
    .truncate {
        max-width: 200px;
    }
}
</style>
@endsection