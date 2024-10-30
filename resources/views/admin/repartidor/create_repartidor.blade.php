@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Repartidor</h2>

    <form action="{{ route('admin.repartidor.store_repartidor') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Nombre</label>
            <input type="text" name="name" class="border rounded w-full px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Correo Electrónico</label>
            <input type="email" name="email" class="border rounded w-full px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Contraseña</label>
            <input type="password" name="password" class="border rounded w-full px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" class="border rounded w-full px-3 py-2">
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Registrar</button>
    </form>
</div>
@endsection
