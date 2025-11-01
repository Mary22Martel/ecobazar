@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-2xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">✏️ Editar Medida</h1>
                <p class="text-cyan-100 text-base sm:text-lg">Modificar {{ $medida->nombre }}</p>
            </div>
            <a href="{{ route('admin.configuracion.medidas') }}" 
               class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admin.configuracion.medidas.actualizar', $medida->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nombre de la medida -->
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                        📏 Nombre de la Medida *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $medida->nombre) }}"
                           placeholder="Ej: Kilogramo, Unidad, Litro, Metro..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-colors @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado activo -->
                <div>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="active" 
                               {{ old('active', $medida->active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                        <span class="text-sm font-semibold text-gray-700">
                            ✅ Medida activa (disponible para productos)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-700 mb-3">📊 Información de la medida</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="font-semibold text-gray-800">#{{ $medida->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Creada:</span>
                        <span class="font-semibold text-gray-800">{{ $medida->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Productos asociados:</span>
                        @php
                            $totalProductos = \App\Models\Product::where('medida_id', $medida->id)->count();
                        @endphp
                        <span class="font-semibold text-gray-800">{{ $totalProductos }} productos</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Última actualización:</span>
                        <span class="font-semibold text-gray-800">{{ $medida->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 font-semibold">
                    💾 Actualizar Medida
                </button>
                <a href="{{ route('admin.configuracion.medidas') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-all font-semibold text-center">
                    ❌ Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Productos en esta medida -->
    @php
        $productos = \App\Models\Product::where('medida_id', $medida->id)->with('user')->take(5)->get();
        $totalProductos = \App\Models\Product::where('medida_id', $medida->id)->count();
    @endphp
    
    @if($productos->count() > 0)
    <div class="mt-6 bg-cyan-50 border border-cyan-200 rounded-xl p-4">
        <h3 class="text-lg font-bold text-cyan-800 mb-3 flex items-center">
            <span class="mr-2">📦</span> Productos usando esta medida ({{ $totalProductos }})
        </h3>
        <div class="space-y-2">
            @foreach($productos as $producto)
            <div class="bg-white rounded-lg p-3 border border-cyan-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-cyan-800">{{ $producto->name }}</span>
                        <span class="text-sm text-cyan-600 ml-2">por {{ $producto->user->name }}</span>
                    </div>
                    <span class="text-sm bg-cyan-100 text-cyan-700 px-2 py-1 rounded">
                        S/ {{ number_format($producto->precio, 2) }} / {{ $medida->simbolo ?? $medida->nombre }}
                    </span>
                </div>
            </div>
            @endforeach
            
            @if($totalProductos > 5)
            <div class="text-center text-cyan-600 text-sm font-medium">
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
            Eliminar esta medida es una acción permanente. Solo podrás hacerlo si no tiene productos asociados.
        </p>
        
        @if($totalProductos > 0)
        <div class="bg-red-100 border border-red-300 rounded-lg p-3 mb-4">
            <p class="text-red-800 text-sm">
                <strong>⚠️ No se puede eliminar:</strong> Esta medida tiene {{ $totalProductos }} {{ $totalProductos == 1 ? 'producto asociado' : 'productos asociados' }}.
            </p>
        </div>
        @else
        <form method="POST" action="{{ route('admin.configuracion.medidas.eliminar', $medida->id) }}" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    onclick="return confirm('¿Estás completamente seguro de eliminar la medida {{ $medida->nombre }}? Esta acción no se puede deshacer.')"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all font-semibold">
                🗑️ Eliminar Medida Permanentemente
            </button>
        </form>
        @endif
    </div>

</div>
@endsection