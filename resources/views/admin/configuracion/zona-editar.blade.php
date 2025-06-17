@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">✏️ Editar Zona</h1>
                <p class="text-blue-100 text-base sm:text-lg">Modificar {{ $zona->name }}</p>
            </div>
            <a href="{{ route('admin.configuracion.zonas') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.zonas.actualizar', $zona->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nombre de la zona -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        🗺️ Nombre de la Zona *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $zona->name) }}"
                           placeholder="Ej: Centro de Huánuco, Pillco Marca, etc."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Costo de delivery -->
                <div>
                    <label for="delivery_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                        💰 Costo de Delivery *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 font-semibold">S/</span>
                        <input type="number" 
                               id="delivery_cost" 
                               name="delivery_cost" 
                               value="{{ old('delivery_cost', $zona->delivery_cost) }}"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('delivery_cost') border-red-500 @enderror">
                    </div>
                    @error('delivery_cost')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Costo que se cobrará por envío a esta zona</p>
                </div>

                <!-- Estado activo -->
                <div>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="active" 
                               {{ old('active', $zona->active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-semibold text-gray-700">
                            ✅ Zona activa (disponible para entregas)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-700 mb-2">📊 Información de la zona</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="font-semibold text-gray-800">#{{ $zona->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Creada:</span>
                        <span class="font-semibold text-gray-800">{{ $zona->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Actualizar Zona
                </button>
                <a href="{{ route('admin.configuracion.zonas') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Zona de peligro -->
    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-red-800 mb-3 flex items-center">
            <span class="mr-2">⚠️</span> Zona de peligro
        </h3>
        <p class="text-sm text-red-700 mb-4">
            Eliminar esta zona es una acción permanente. Solo podrás hacerlo si no tiene pedidos asociados.
        </p>
        <form method="POST" action="{{ route('admin.configuracion.zonas.eliminar', $zona->id) }}" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    onclick="return confirm('¿Estás completamente seguro de eliminar la zona {{ $zona->name }}? Esta acción no se puede deshacer.')"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all font-semibold">
                🗑️ Eliminar Zona Permanentemente
            </button>
        </form>
    </div>

</div>
@endsection