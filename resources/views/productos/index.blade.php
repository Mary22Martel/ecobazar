@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-6xl">
    
    <!-- Header responsive -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">🥕 MIS PRODUCTOS</h1>
                <p class="text-green-100 text-base sm:text-lg">{{ $productos->count() }} productos en tu catálogo</p>
            </div>
            <a href="{{ route('productos.create') }}" 
               class="bg-white/20 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-white/30 transition-all text-sm sm:text-base text-center">
                + Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Navegación de regreso -->
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('agricultor.dashboard') }}" 
           class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
    </div>

    @if($productos->isEmpty())
        <!-- Estado vacío optimizado -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-8 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="text-5xl sm:text-6xl mb-4">🛒</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">No tienes productos aún</h2>
                <p class="text-gray-600 mb-6 sm:mb-8 text-base sm:text-lg">Agrega tus primeros productos para empezar a vender</p>
                <a href="{{ route('productos.create') }}" 
                   class="inline-flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-base sm:text-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear mi primer producto
                </a>
            </div>
        </div>
    @else
        <!-- Botón flotante para agregar en móvil -->
        <div class="block sm:hidden mb-4">
            <a href="{{ route('productos.create') }}" 
               class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-xl font-semibold text-center block hover:from-green-600 hover:to-green-700 transition-all shadow-lg">
                + Agregar Producto
            </a>
        </div>

        <!-- Grid de productos para móvil, tabla para desktop -->
        <div class="block lg:hidden space-y-4">
            @foreach($productos as $producto)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-4">
                    <!-- Header del producto -->
                    <div class="flex items-start space-x-3 mb-3">
                        <div class="flex-shrink-0">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                     alt="{{ $producto->nombre }}" 
                                     class="w-16 h-16 object-cover rounded-xl shadow-sm">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                                    <span class="text-2xl">🥬</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-800 truncate">{{ $producto->nombre }}</h3>
                            <p class="text-sm text-gray-500">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-lg font-bold text-green-600">S/ {{ number_format($producto->precio, 2) }}</span>
                                <span class="text-sm text-gray-600">por {{ $producto->medida->nombre ?? 'unidad' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Disponible:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $producto->cantidad_disponible }} {{ $producto->medida->nombre ?? 'und' }}</span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex space-x-2">
                        <a href="{{ route('productos.edit', $producto) }}" 
                           class="flex-1 bg-yellow-400 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-center text-sm font-semibold">
                            ✏️ Editar
                        </a>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm font-semibold" 
                                    onclick="return confirm('¿Eliminar {{ $producto->nombre }}?');">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Tabla para desktop -->
        <div class="hidden lg:block bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr class="text-gray-600 text-left">
                            <th class="py-4 px-6 font-semibold">Imagen</th>
                            <th class="py-4 px-6 font-semibold">Producto</th>
                            <th class="py-4 px-6 font-semibold">Categoría</th>
                            <th class="py-4 px-6 font-semibold">Precio</th>
                            <th class="py-4 px-6 font-semibold">Disponible</th>
                            <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($productos as $producto)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                         alt="{{ $producto->nombre }}" 
                                         class="w-16 h-16 object-cover rounded-xl shadow-sm">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">🥬</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $producto->nombre }}</h3>
                                <p class="text-sm text-gray-500">{{ $producto->medida->nombre ?? 'Sin medida' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-lg text-sm font-medium">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-lg font-bold text-green-600">S/ {{ number_format($producto->precio, 2) }}</span>
                                <div class="text-xs text-gray-500">por {{ $producto->medida->nombre ?? 'unidad' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-lg font-semibold text-gray-800">{{ $producto->cantidad_disponible }}</span>
                                <div class="text-xs text-gray-500">{{ $producto->medida->nombre ?? 'unidades' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('productos.edit', $producto) }}" 
                                       class="bg-yellow-400 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-sm font-semibold">
                                        ✏️ Editar
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm font-semibold" 
                                                onclick="return confirm('¿Eliminar {{ $producto->nombre }}?');">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumen inferior -->
        <div class="mt-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 sm:p-6">
            <h3 class="text-lg font-bold text-green-800 mb-3 flex items-center">
                <span class="mr-2">📊</span> Resumen de tu catálogo
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $productos->count() }}</div>
                    <div class="text-sm text-green-700">Productos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $productos->groupBy('categoria_id')->count() }}</div>
                    <div class="text-sm text-green-700">Categorías</div>
                </div>
                <div class="text-center col-span-2 sm:col-span-1">
                    <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $productos->sum('cantidad_disponible') }}</div>
                    <div class="text-sm text-green-700">Total disponible</div>
                </div>
            </div>
        </div>

        <!-- Botón fijo para agregar en móvil -->
        <div class="block sm:hidden mt-6">
            <a href="{{ route('productos.create') }}" 
               class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-xl font-semibold text-center block hover:from-green-600 hover:to-green-700 transition-all shadow-lg">
                + Agregar Otro Producto
            </a>
        </div>
    @endif

</div>
@endsection