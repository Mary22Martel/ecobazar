@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold mb-6">Lista de Repartidores</h1>
    
    <a href="{{ route('admin.repartidor.create') }}" class="bg-green-500 text-white py-2 px-4 rounded-lg mb-4 inline-block">Crear Nuevo Repartidor</a>

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="py-4 px-6">Nombre</th>
                <th class="py-4 px-6">Email</th>
                <th class="py-4 px-6">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($repartidores as $repartidor)
            <tr class="border-b">
                <td class="py-4 px-6">{{ $repartidor->name }}</td>
                <td class="py-4 px-6">{{ $repartidor->email }}</td>
                <td class="py-4 px-6">
                    <a href="{{ route('admin.repartidor.edit', $repartidor->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Editar</a>
                    <form action="{{ route('admin.repartidor.delete', $repartidor->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg" onclick="return confirm('¿Estás seguro de que quieres eliminar este repartidor?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
