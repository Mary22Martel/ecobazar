@extends('layouts.app2')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50">
    <div class="container mx-auto px-3 py-4 max-w-6xl">
        
        <!-- Header responsive optimizado con colores suavizados -->
        <div class="bg-gradient-to-r from-green-400 to-green-500 text-white rounded-2xl p-4 sm:p-6 mb-4 shadow-lg">
            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 flex items-center">
                        🥕 <span class="ml-2">TUS PRODUCTOS</span>
                    </h1>
                    <p class="text-green-100 text-sm sm:text-base lg:text-lg">
                        {{ $productos->count() }} productos en tu catálogo
                    </p>
                </div>
                <!-- Botón agregar desktop -->
                <div class="hidden sm:block">
                    <a href="{{ route('productos.create') }}" 
                       class="bg-white/20 backdrop-blur-sm text-white px-4 lg:px-6 py-2 lg:py-3 rounded-xl font-semibold hover:bg-white/30 transition-all text-sm lg:text-base text-center shadow-md hover:shadow-lg transform hover:scale-105">
                        ➕ Nuevo Producto
                    </a>
                </div>
            </div>
        </div>

        <!-- RECORDATORIO SEMANAL - NUEVO -->
        <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-400 rounded-lg p-4 shadow-sm">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-amber-800 mb-1">💡 Recordatorio Semanal</h3>
                    <p class="text-sm text-amber-700 leading-relaxed">
                        <strong>¡Recuerda actualizar tu stock!</strong> 
                    </p>
                    <div class="mt-2 text-xs text-amber-600 bg-amber-100 px-2 py-1 rounded-md inline-block">
                        📅 Se recomienda actualizar cada domingo o lunes
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="flex-shrink-0 text-amber-400 hover:text-amber-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navegación de regreso -->
        <div class="mb-4">
            <a href="{{ route('agricultor.dashboard') }}" 
               class="inline-flex items-center text-gray-600 hover:text-green-500 transition-colors font-medium text-sm sm:text-base bg-white px-3 py-2 rounded-lg shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al inicio
            </a>
        </div>

        @if($productos->isEmpty())
            <!-- Estado vacío optimizado -->
            <div class="bg-white border-2 border-dashed border-green-200 rounded-2xl p-6 sm:p-12 text-center shadow-lg">
                <div class="max-w-md mx-auto">
                    <div class="text-4xl sm:text-6xl mb-4 animate-bounce">🛒</div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">No tienes productos aún</h2>
                    <p class="text-gray-600 mb-6 text-sm sm:text-lg leading-relaxed">
                        Agrega tus primeros productos para empezar a vender en la feria
                    </p>
                    <a href="{{ route('productos.create') }}" 
                       class="inline-flex items-center bg-gradient-to-r from-green-400 to-green-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl text-sm sm:text-lg font-bold hover:from-green-500 hover:to-green-600 transform hover:scale-105 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear mi primer producto
                    </a>
                </div>
            </div>
        @else
            <!-- Botón flotante para agregar en móvil (colores suavizados) -->
            <div class="block sm:hidden mb-4">
                <a href="{{ route('productos.create') }}" 
                   class="w-full bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-3 rounded-xl font-bold text-center block hover:from-green-500 hover:to-green-600 transition-all shadow-md hover:shadow-lg transform active:scale-95 text-lg">
                    ➕ Agregar Nuevo Producto
                </a>
            </div>

            <!-- Vista móvil optimizada -->
            <div class="block lg:hidden space-y-4">
                @foreach($productos as $producto)
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-4">
                        <!-- Header del producto -->
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-shrink-0">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                         alt="{{ $producto->nombre }}" 
                                         class="w-16 h-16 object-cover rounded-xl shadow-md">
                                @else
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center shadow-inner">
                                        <span class="text-2xl">🥬</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-800 truncate">{{ $producto->nombre }}</h3>
                                <p class="text-sm text-gray-500 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xl font-bold text-green-500">S/ {{ number_format($producto->precio, 2) }}</span>
                                    <span class="text-xs text-gray-600 bg-green-50 px-2 py-1 rounded-full">
                                        por {{ $producto->medida->nombre ?? 'unidad' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3 mb-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 font-medium">Disponible:</span>
                                <span class="text-sm font-bold text-gray-800 bg-white px-2 py-1 rounded-lg">
                                    {{ $producto->cantidad_disponible }} {{ $producto->medida->nombre ?? 'und' }}
                                </span>
                            </div>
                        </div>

                        <!-- Acciones con iconos minimalistas -->
                        <div class="flex space-x-2">
                            <a href="{{ route('productos.edit', $producto) }}" 
                               class="flex-1 bg-orange-400 text-white px-3 py-2.5 rounded-xl hover:from-amber-500 hover:to-orange-500 transition-all text-center text-sm font-bold shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full bg-red-500 text-white px-3 py-2.5 rounded-xl hover:from-red-500 hover:to-red-600 transition-all text-sm font-bold shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center" 
                                        onclick="return confirm('¿Eliminar {{ $producto->nombre }}?');">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tabla para desktop (colores suavizados) -->
            <div class="hidden lg:block bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr class="text-gray-700">
                                <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Imagen</th>
                                <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Producto</th>
                                <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Categoría</th>
                                <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Precio</th>
                                <th class="py-4 px-6 font-bold text-left border-b border-gray-200">Disponible</th>
                                <th class="py-4 px-6 font-bold text-center border-b border-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($productos as $producto)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="py-4 px-6">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                             alt="{{ $producto->nombre }}" 
                                             class="w-16 h-16 object-cover rounded-xl shadow-md">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center shadow-inner">
                                            <span class="text-2xl">🥬</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <h3 class="font-bold text-gray-800 mb-1 text-lg">{{ $producto->nombre }}</h3>
                                    <p class="text-sm text-gray-500 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                        {{ $producto->medida->nombre ?? 'Sin medida' }}
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block bg-gradient-to-r from-green-100 to-green-200 text-green-700 px-3 py-1 rounded-xl text-sm font-semibold shadow-sm">
                                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xl font-bold text-green-500">S/ {{ number_format($producto->precio, 2) }}</span>
                                    <div class="text-xs text-gray-500 bg-green-50 px-2 py-1 rounded-md inline-block mt-1">
                                        por {{ $producto->medida->nombre ?? 'unidad' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-lg font-bold text-gray-800">{{ $producto->cantidad_disponible }}</span>
                                    <div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-md inline-block mt-1">
                                        {{ $producto->medida->nombre ?? 'unidades' }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex justify-center space-x-3">
                                        <a href="{{ route('productos.edit', $producto) }}" 
                                           class="bg-orange-400 text-white px-4 py-2 rounded-xl hover:from-amber-500 hover:to-orange-500 transition-all text-sm font-bold shadow-md hover:shadow-lg transform hover:scale-105 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-500 text-white px-4 py-2 rounded-xl hover:from-red-500 hover:to-red-600 transition-all text-sm font-bold shadow-md hover:shadow-lg transform hover:scale-105 flex items-center" 
                                                    onclick="return confirm('¿Eliminar {{ $producto->nombre }}?');">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
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

            <!-- Resumen inferior con colores suavizados -->
            <div class="mt-6 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-4 sm:p-6 shadow-md">
                <h3 class="text-lg sm:text-xl font-bold text-green-700 mb-4 flex items-center">
                    <span class="mr-3 text-xl">📊</span> 
                    <span>Resumen de tu catálogo</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center bg-white rounded-xl p-4 shadow-md">
                        <div class="text-2xl sm:text-3xl font-bold text-green-500 mb-1">{{ $productos->count() }}</div>
                        <div class="text-sm text-green-600 font-medium">Productos totales</div>
                    </div>
                    <div class="text-center bg-white rounded-xl p-4 shadow-md">
                        <div class="text-2xl sm:text-3xl font-bold text-blue-500 mb-1">{{ $productos->groupBy('categoria_id')->count() }}</div>
                        <div class="text-sm text-blue-600 font-medium">Categorías diferentes</div>
                    </div>
                    <div class="text-center bg-white rounded-xl p-4 shadow-md sm:col-span-1">
                        <div class="text-2xl sm:text-3xl font-bold text-purple-500 mb-1">{{ $productos->sum('cantidad_disponible') }}</div>
                        <div class="text-sm text-purple-600 font-medium">Total disponible</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection