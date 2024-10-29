<!-- Index View -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-5xl">
    <h1 class="text-3xl font-bold mb-6">Gestionar mis Canastas</h1>

    <!-- Tabla de canastas -->
    <table class="w-full border-collapse mt-6">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Nombre</th>
                <th class="border px-4 py-2">Precio</th>
                <th class="border px-4 py-2">Productos</th>
                <th class="border px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($canastas as $canasta)
            <tr class="hover:bg-gray-100">
                <td class="border px-4 py-2">{{ $canasta->id }}</td>
                <td class="border px-4 py-2">{{ $canasta->nombre }}</td>
                <td class="border px-4 py-2">S/{{ $canasta->precio }}</td>
                <td class="border px-4 py-2">
                    <ul class="list-disc pl-5">
                        @foreach ($canasta->productos as $producto)
                            <li>{{ $producto->nombre }} (Cantidad: {{ $producto->pivot->cantidad }})</li>
                        @endforeach
                    </ul>
                </td>
                <td class="border px-4 py-2">
                    <!-- Botón para editar -->
                    <a href="{{ route('admin.canastas.edit', $canasta->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Editar</a>
                    
                    <!-- Formulario para eliminar -->
                    <form action="{{ route('admin.canastas.destroy', $canasta->id) }}" method="POST" class="inline-block">
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
