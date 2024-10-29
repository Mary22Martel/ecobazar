<!-- Edit View -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">Editar Canasta</h1>

    <form action="{{ route('admin.canastas.update', $canasta->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="nombre" class="block font-semibold">Nombre de la Canasta:</label>
            <input type="text" name="nombre" value="{{ $canasta->nombre }}" class="border p-2 w-full" required>
        </div>
        <div class="mb-4">
            <label for="precio" class="block font-semibold">Precio:</label>
            <input type="number" name="precio" step="0.01" value="{{ $canasta->precio }}" class="border p-2 w-full" required>
        </div>
        <div class="mb-4">
            <label for="descripcion" class="block font-semibold">Descripción:</label>
            <textarea name="descripcion" class="border p-2 w-full">{{ $canasta->descripcion }}</textarea>
        </div>

        <h3 class="font-bold mb-4">Agregar Productos a la Canasta:</h3>
        @foreach($productos as $producto)
            <div class="mb-2 flex items-center">
                <input type="checkbox" name="productos[{{ $producto->id }}][id]" value="{{ $producto->id }}" class="mr-2" @if($canasta->productos->contains($producto->id)) checked @endif>
                <label>{{ $producto->nombre }} (Disponible: {{ $producto->cantidad_disponible }})</label>
                <input type="number" name="productos[{{ $producto->id }}][cantidad]" min="1" value="{{ $canasta->productos->contains($producto->id) ? $canasta->productos->find($producto->id)->pivot->cantidad : 1 }}" class="border p-1 ml-4 w-20">
            </div>
        @endforeach

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-6">Actualizar Canasta</button>
    </form>
</div>
@endsection