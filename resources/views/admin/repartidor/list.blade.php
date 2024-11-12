@extends('layouts.app2')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-4xl bg-white p-8 rounded-lg shadow-lg">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-center mb-6 text-green-600">Lista de Repartidores</h1>
        
        <!-- Create Button -->
        <div class="flex justify-center mb-6">
            <a href="{{ route('admin.repartidor.create') }}" class="bg-green-500 text-white py-2 px-6 rounded-lg font-semibold hover:bg-green-600 transition duration-200">Crear Nuevo Repartidor</a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Nombre</th>
                        <th class="py-4 px-6 text-left font-semibold text-gray-700">Email</th>
                        <th class="py-4 px-6 text-center font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repartidores as $repartidor)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $repartidor->name }}</td>
                        <td class="py-4 px-6">{{ $repartidor->email }}</td>
                        <td class="py-4 px-6 text-center flex justify-center space-x-2">
                            <a href="{{ route('admin.repartidor.edit', $repartidor->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">Editar</a>
                            <form action="{{ route('admin.repartidor.delete', $repartidor->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200" onclick="return confirm('¿Estás seguro de que quieres eliminar este repartidor?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
