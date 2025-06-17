@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">📏 Gestión de Medidas</h1>
                <p class="text-teal-100 text-base sm:text-lg">Unidades de medida disponibles</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Botón agregar nueva medida -->
    <div class="mb-6">
        <a href="{{ route('admin.configuracion.medidas.crear') }}" 
           class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold inline-block">
            ➕ Agregar Nueva Medida
        </a>
    </div>

    <!-- Lista de medidas -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">
                Medidas Registradas 
                @if(isset($medidas))
                    ({{ method_exists($medidas, 'total') ? $medidas->total() : $medidas->count() }})
                @endif
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Productos</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($medidas as $medida)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $medida->id }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-100 to-teal-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-lg">📏</span>
                                </div>
                                <div class="font-semibold text-gray-800">{{ $medida->nombre }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $totalProductos = \App\Models\Product::where('medida_id', $medida->id)->count();
                            @endphp
                            <span class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $totalProductos }} {{ $totalProductos == 1 ? 'producto' : 'productos' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @if($medida->active ?? true)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    ✅ Activa
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                    ❌ Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.configuracion.medidas.editar', $medida->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    ✏️ Editar
                                </a>
                                <form method="POST" action="{{ route('admin.configuracion.medidas.eliminar', $medida->id) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('¿Estás seguro de eliminar esta medida?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-6xl mb-4">📏</div>
                            <h3 class="text-lg font-semibold mb-2">No hay medidas registradas</h3>
                            <p class="text-sm">Agrega la primera unidad de medida</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($medidas) && method_exists($medidas, 'hasPages') && $medidas->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $medidas->links() }}
        </div>
        @endif
    </div>

    <!-- Ejemplos de medidas -->
    <div class="mt-6 bg-teal-50 border border-teal-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-teal-800 mb-3 flex items-center">
            <span class="mr-2">💡</span> Ejemplos de medidas comunes
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="bg-white rounded-lg p-3 text-center border border-teal-200">
                <span class="text-lg block mb-1">⚖️</span>
                <span class="text-teal-700 font-medium block">Kilogramo</span>
                <span class="text-teal-600 text-xs">kg</span>
            </div>
            <div class="bg-white rounded-lg p-3 text-center border border-teal-200">
                <span class="text-lg block mb-1">📦</span>
                <span class="text-teal-700 font-medium block">Unidad</span>
                <span class="text-teal-600 text-xs">und</span>
            </div>
            <div class="bg-white rounded-lg p-3 text-center border border-teal-200">
                <span class="text-lg block mb-1">🥤</span>
                <span class="text-teal-700 font-medium block">Litro</span>
                <span class="text-teal-600 text-xs">L</span>
            </div>
            <div class="bg-white rounded-lg p-3 text-center border border-teal-200">
                <span class="text-lg block mb-1">📏</span>
                <span class="text-teal-700 font-medium block">Metro</span>
                <span class="text-teal-600 text-xs">m</span>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('admin.configuracion.zonas') }}" 
           class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">🗺️</span>
            <h4 class="font-semibold text-indigo-800">Zonas</h4>
            <p class="text-sm text-indigo-600">Gestionar zonas</p>
        </a>
        
        <a href="{{ route('admin.configuracion.categorias') }}" 
           class="bg-pink-50 border-2 border-pink-200 rounded-xl p-4 hover:border-pink-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">🏷️</span>
            <h4 class="font-semibold text-pink-800">Categorías</h4>
            <p class="text-sm text-pink-600">Gestionar categorías</p>
        </a>
        
        <!-- <a href="{{ route('admin.configuracion.mercados') }}" 
           class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4 hover:border-amber-300 hover:shadow-lg transition-all text-center group">
            <span class="text-2xl mb-2 block group-hover:animate-bounce">🏪</span>
            <h4 class="font-semibold text-amber-800">Mercados</h4>
            <p class="text-sm text-amber-600">Gestionar mercados</p>
        </a> -->
    </div>

</div>

<style>
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

@endsection