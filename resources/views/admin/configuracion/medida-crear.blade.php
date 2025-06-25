@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">➕ Nueva Medida</h1>
                <p class="text-green-100 text-base sm:text-lg">Agregar unidad de medida</p>
            </div>
            <a href="{{ route('admin.configuracion.medidas') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.medidas.guardar') }}">
            @csrf
            
            <div class="space-y-6">
                <!-- Nombre de la medida -->
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                        📏 Nombre de la Medida *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Kilogramo, Unidad, Litro, Metro..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-teal-500 to-teal-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Guardar Medida
                </button>
                <a href="{{ route('admin.configuracion.medidas') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Ejemplos y ayuda -->
    <div class="mt-6 bg-teal-50 border border-teal-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-teal-800 mb-3 flex items-center">
            <span class="mr-2">💡</span> Ejemplos de medidas comunes
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <h4 class="font-semibold text-teal-700 mb-2">⚖️ Peso</h4>
                <ul class="text-teal-600 space-y-1">
                    <li>• Kilogramo (kg) - Para la mayoría de productos</li>
                    <li>• Gramo (g) - Para productos pequeños</li>
                    <li>• Libra (lb) - Para algunos mercados</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-teal-700 mb-2">📦 Cantidad</h4>
                <ul class="text-teal-600 space-y-1">
                    <li>• Unidad (und) - Productos individuales</li>
                    <li>• Docena (doc) - 12 unidades</li>
                    <li>• Paquete (paq) - Conjunto de productos</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-teal-700 mb-2">🥤 Volumen</h4>
                <ul class="text-teal-600 space-y-1">
                    <li>• Litro (L) - Para líquidos</li>
                    <li>• Mililitro (ml) - Volúmenes pequeños</li>
                    <li>• Galón (gal) - Grandes volúmenes</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-teal-700 mb-2">📏 Longitud</h4>
                <ul class="text-teal-600 space-y-1">
                    <li>• Metro (m) - Para productos largos</li>
                    <li>• Centímetro (cm) - Medidas pequeñas</li>
                    <li>• Pulgada (in) - Sistema inglés</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Consejos -->
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-blue-800 mb-2 flex items-center">
            <span class="mr-2">💡</span> Consejos
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Usa nombres descriptivos que los agricultores entiendan fácilmente</li>
            <li>• Agrupa medidas similares (ej: todas las de peso juntas)</li>
        </ul>
    </div>

</div>
@endsection