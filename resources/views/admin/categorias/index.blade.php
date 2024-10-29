@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">Gestionar Categorías</h1>

    <!-- Formulario para crear nueva categoría -->
    <form action="{{ route('admin.categorias.store') }}" method="POST" class="mb-6">
        @csrf
        <div class="mb-4">
            <input type="text" name="nombre" placeholder="Nombre de la Categoría" class="border p-2 w-full" required>
        </div>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Crear Categoría</button>
    </form>

    <!-- Tabla de categorías -->
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Nombre</th>
                <th class="border px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $categoria)
            <tr>
                <td class="border px-4 py-2">{{ $categoria->id }}</td>
                <td class="border px-4 py-2">{{ $categoria->nombre }}</td>
                <td class="border px-4 py-2">
                    <!-- Botón para editar -->
                    <a href="{{ route('admin.categorias.edit', $categoria->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Editar</a>
                    
                    <!-- Formulario para eliminar -->
                    <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg ml-2">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
