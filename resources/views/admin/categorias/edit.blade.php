@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-12 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">Editar Categoría</h1>

    <form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nombre" class="block text-lg font-semibold text-gray-700 mb-2">Nombre de la Categoría</label>
            <input type="text" name="nombre" value="{{ $categoria->nombre }}" class="border p-2 w-full" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Actualizar Categoría</button>
    </form>
</div>
@endsection
