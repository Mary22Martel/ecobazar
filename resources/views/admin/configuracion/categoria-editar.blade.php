@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">✏️ Editar Categoría</h1>
                <p class="text-purple-100 text-base sm:text-lg">Modificar {{ $categoria->nombre }}</p>
            </div>
            <a href="{{ route('admin.configuracion.categorias') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.categorias.actualizar', $categoria->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nombre de la categoría -->
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                        🏷️ Nombre de la Categoría *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $categoria->nombre) }}"
                           placeholder="Ej: Verduras, Frutas, Cereales, Hierbas..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        📝 Descripción (opcional)
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Describe brevemente esta categoría..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description', $categoria->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Ayuda a los agricultores a entender qué productos van en esta categoría</p>
                </div>

                <!-- Estado activo -->
                <div>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="active" 
                               {{ old('active', $categoria->active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm font-semibold text-gray-700">
                            ✅ Categoría activa (disponible para productos)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-700 mb-3">📊 Información de la categoría</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="font-semibold text-gray-800">#{{ $categoria->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Creada:</span>
                        <span class="font-semibold text-gray-800">{{ $categoria->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Productos asociados:</span>
                        @php
                            $totalProductos = \App\Models\Product::where('categoria_id', $categoria->id)->count();
                        @endphp
                        <span class="font-semibold text-gray-800">{{ $totalProductos }} productos</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Última actualización:</span>
                        <span class="font-semibold text-gray-800">{{ $categoria->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Actualizar Categoría
                </button>
                <a href="{{ route('admin.configuracion.categorias') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Productos en esta categoría -->
    @php
        $productos = \App\Models\Product::where('categoria_id', $categoria->id)->with('user')->take(5)->get();
        $totalProductos = \App\Models\Product::where('categoria_id', $categoria->id)->count();
    @endphp
    
    @if($productos->count() > 0)
    <div class="mt-6 bg-purple-50 border border-purple-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-purple-800 mb-3 flex items-center">
            <span class="mr-2">📦</span> Productos en esta categoría ({{ $totalProductos }})
        </h3>
        <div class="space-y-2">
            @foreach($productos as $producto)
            <div class="bg-white rounded-lg p-3 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-purple-800">{{ $producto->name }}</span>
                        <span class="text-sm text-purple-600 ml-2">por {{ $producto->user->name }}</span>
                    </div>
                    <span class="text-sm bg-purple-100 text-purple-700 px-2 py-1 rounded">
                        S/ {{ number_format($producto->precio, 2) }}
                    </span>
                </div>
            </div>
            @endforeach
            
            @if($totalProductos > 5)
            <div class="text-center text-purple-600 text-sm font-medium">
                ... y {{ $totalProductos - 5 }} productos más
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Zona de peligro -->
    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-red-800 mb-3 flex items-center">
            <span class="mr-2">⚠️</span> Zona de peligro
        </h3>
        <p class="text-sm text-red-700 mb-4">
            Eliminar esta categoría es una acción permanente. Solo podrás hacerlo si no tiene productos asociados.
        </p>
        
        @if($totalProductos > 0)
        <div class="bg-red-100 border border-red-300 rounded-lg p-3 mb-4">
            <p class="text-red-800 text-sm">
                <strong>⚠️ No se puede eliminar:</strong> Esta categoría tiene {{ $totalProductos }} {{ $totalProductos == 1 ? 'producto asociado' : 'productos asociados' }}.
            </p>
        </div>
        @else
        <form method="POST" action="{{ route('admin.configuracion.categorias.eliminar', $categoria->id) }}" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    onclick="return confirm('¿Estás completamente seguro de eliminar la categoría {{ $categoria->nombre }}? Esta acción no se puede deshacer.')"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all font-semibold">
                🗑️ Eliminar Categoría Permanentemente
            </button>
        </form>
        @endif
    </div>

</div>
@endsection