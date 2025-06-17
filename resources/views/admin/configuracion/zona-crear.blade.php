@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">➕ Nueva Zona</h1>
                <p class="text-indigo-100 text-base sm:text-lg">Agregar zona de entrega</p>
            </div>
            <a href="{{ route('admin.configuracion.zonas') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.zonas.guardar') }}">
            @csrf
            
            <div class="space-y-6">
                <!-- Nombre de la zona -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        🗺️ Nombre de la Zona *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           placeholder="Ej: Centro de Huánuco, Pillco Marca, etc."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('name') border-red-500 @enderror">
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
                               value="{{ old('delivery_cost') }}"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('delivery_cost') border-red-500 @enderror">
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
                               checked="{{ old('active', true) ? 'checked' : '' }}"
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-gray-700">
                            ✅ Zona activa (disponible para entregas)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Guardar Zona
                </button>
                <a href="{{ route('admin.configuracion.zonas') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Ayuda -->
    <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-indigo-800 mb-2 flex items-center">
            <span class="mr-2">💡</span> Consejos
        </h3>
        <ul class="text-sm text-indigo-700 space-y-1">
            <li>• Usa nombres claros y reconocibles para las zonas</li>
            <li>• El costo de delivery se suma al total del pedido</li>
            <li>• Puedes desactivar una zona temporalmente sin eliminarla</li>
            <li>• Las zonas inactivas no aparecen en el checkout</li>
        </ul>
    </div>

</div>
@endsection