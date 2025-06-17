@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">➕ Nueva Categoría</h1>
                <p class="text-pink-100 text-base sm:text-lg">Agregar categoría de productos</p>
            </div>
            <a href="{{ route('admin.configuracion.categorias') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.categorias.guardar') }}">
            @csrf
            
            <div class="space-y-6">
                <!-- Nombre de la categoría -->
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                        🏷️ Nombre de la Categoría *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Verduras, Frutas, Cereales, Hierbas..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors @error('nombre') border-red-500 @enderror">
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
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Ayuda a los agricultores a entender qué productos van en esta categoría</p>
                </div>

            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Guardar Categoría
                </button>
                <a href="{{ route('admin.configuracion.categorias') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Ejemplos y ayuda -->
    <div class="mt-6 bg-pink-50 border border-pink-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-pink-800 mb-3 flex items-center">
            <span class="mr-2">💡</span> Ejemplos de categorías comunes
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <h4 class="font-semibold text-pink-700 mb-2">🥬 Verduras y Hortalizas</h4>
                <p class="text-pink-600">Lechuga, tomate, cebolla, zanahoria, papa, apio...</p>
            </div>
            <div>
                <h4 class="font-semibold text-pink-700 mb-2">🍎 Frutas</h4>
                <p class="text-pink-600">Manzana, plátano, naranja, fresa, mango, palta...</p>
            </div>
            <div>
                <h4 class="font-semibold text-pink-700 mb-2">🌾 Cereales y Granos</h4>
                <p class="text-pink-600">Quinua, maíz, trigo, cebada, avena, arroz...</p>
            </div>
            <div>
                <h4 class="font-semibold text-pink-700 mb-2">🌿 Hierbas Aromáticas</h4>
                <p class="text-pink-600">Cilantro, perejil, hierbabuena, albahaca, orégano...</p>
            </div>
        </div>
    </div>

    <!-- Consejos -->
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-blue-800 mb-2 flex items-center">
            <span class="mr-2">💡</span> Consejos
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Usa nombres claros y conocidos por los agricultores</li>
            <li>• Evita categorías muy específicas que tengan pocos productos</li>
            <li>• La descripción ayuda a evitar confusiones</li>
        </ul>
    </div>

</div>
@endsection