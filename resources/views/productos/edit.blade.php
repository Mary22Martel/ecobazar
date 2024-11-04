@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-12 max-w-3xl">
    <h1 class="text-5xl font-bold text-orange-400 mb-8 text-center">Editar Producto</h1>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto) }}" method="POST" class="bg-white shadow-lg rounded-lg p-8 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nombre del Producto -->
        <div>
            <label for="nombre" class="block text-lg font-semibold text-gray-700 mb-2">Nombre del Producto</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre) }}" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400" required>
        </div>

        <!-- Descripción -->
        <div>
            <label for="descripcion" class="block text-lg font-semibold text-gray-700 mb-2">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <!-- Unidad de medida -->
        <div>
            <label for="medida" class="block text-lg font-semibold text-gray-700 mb-2">Unidad de medida</label>
            <select name="medida_id" id="medida" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400">
                @foreach ($medidas as $medida)
                    <option value="{{ $medida->id }}" {{ $producto->medida_id == $medida->id ? 'selected' : '' }}>{{ $medida->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Precio y Cantidad (Grid para mejor distribución) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Precio -->
            <div>
                <label for="precio" class="block text-lg font-semibold text-gray-700 mb-2">Precio</label>
                <input type="number" name="precio" id="precio" value="{{ old('precio', $producto->precio) }}" step="0.01" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400" required>
            </div>

            <!-- Cantidad Disponible -->
            <div>
                <label for="cantidad_disponible" class="block text-lg font-semibold text-gray-700 mb-2">Cantidad Disponible</label>
                <input type="number" name="cantidad_disponible" id="cantidad_disponible" value="{{ old('cantidad_disponible', $producto->cantidad_disponible) }}" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400" required>
            </div>
        </div>

        <!-- Categoría -->
        <div>
            <label for="categoria" class="block text-lg font-semibold text-gray-700 mb-2">Categoría</label>
            <select name="categoria_id" id="categoria" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400">
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Imagen del Producto -->
        <div>
            <label for="imagen" class="block text-lg font-semibold text-gray-700 mb-2">Imagen del Producto</label>
            <input type="file" name="imagen" id="imagen" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-blue-400 focus:border-blue-400">
            @if($producto->imagen)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-32 h-32 object-cover rounded-lg shadow-md">
                </div>
            @endif
        </div>

        <!-- Botón de Actualizar -->
        <div class="pt-6">
            <button type="submit" class="w-full py-4 bg-orange-400 text-white text-lg font-bold rounded-lg hover:bg-green-500 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-opacity-50">
                Actualizar Producto
            </button>
        </div>
    </form>
</div>
@endsection