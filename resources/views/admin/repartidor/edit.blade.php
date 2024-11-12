@extends('layouts.app2')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold mb-6">Editar Repartidor</h1>

    <form action="{{ route('admin.repartidor.update', $repartidor->id) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Nombre</label>
            <input type="text" name="name" id="name" value="{{ $repartidor->name }}" required class="border rounded-lg px-4 py-2 w-full">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ $repartidor->email }}" required class="border rounded-lg px-4 py-2 w-full">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700">Contraseña (dejar en blanco si no desea cambiar)</label>
            <input type="password" name="password" id="password" class="border rounded-lg px-4 py-2 w-full">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="border rounded-lg px-4 py-2 w-full">
        </div>

        <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg">Actualizar Repartidor</button>
    </form>
</div>
@endsection
