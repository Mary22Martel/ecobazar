@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-6xl">
    <h1 class="text-6xl font-bold text-green-600 mb-8 text-center">Mis Productos</h1>

    <div class="flex justify-center mb-8">
        <a href="{{ route('productos.create') }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300 ease-in-out">
            Crear nuevo producto
        </a>
    </div>

    @if(session('success'))
        <div class="flex justify-center mt-4">
            <div class="p-4 bg-green-200 text-green-800 rounded-lg w-full max-w-4xl text-center">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="overflow-x-auto mt-8">
        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg mx-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-left">
                    <th class="py-4 px-6">Imagen</th>
                    <th class="py-4 px-6">Nombre</th>
                    <th class="py-4 px-6">Medida</th>
                    <th class="py-4 px-6">Categoría</th>
                    <th class="py-4 px-6">Precio</th>
                    <th class="py-4 px-6">Cantidad</th>
                    <th class="py-4 px-6">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($productos as $producto)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-16 h-16 object-cover rounded-full shadow">
                            @else
                                <span class="text-gray-500">Sin imagen</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">{{ $producto->nombre }}</td>
                        <td class="py-4 px-6">{{ $producto->medida->nombre ?? 'N/A' }}</td> <!-- Mostrar nombre de la medida -->
                        <td class="py-4 px-6">{{ $producto->categoria->nombre ?? 'N/A' }}</td> <!-- Mostrar nombre de la categoría -->
                        <td class="py-4 px-6">S/{{ number_format($producto->precio, 2) }}</td>
                        <td class="py-4 px-6">{{ $producto->cantidad_disponible }}</td>
                        <td class="py-4 px-6 flex space-x-4">
                            <a href="{{ route('productos.edit', $producto) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-300 ease-in-out">
                                Editar
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 ease-in-out" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($productos->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-600">
                            No tienes productos aún. <a href="{{ route('productos.create') }}" class="text-blue-500 underline">Crea uno aquí</a>.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<br>
<br>
@endsection
