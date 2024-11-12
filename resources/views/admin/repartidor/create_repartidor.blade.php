@extends('layouts.app2')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <!-- Title -->
        <h2 class="text-3xl font-bold mb-6 text-center text-green-600">Registrar Nuevo Repartidor</h2>

        <!-- Form -->
        <form action="{{ route('admin.repartidor.store_repartidor') }}" method="POST" class="space-y-4">
            @csrf
            <!-- Name Field -->
            <div>
                <label class="block text-gray-700 font-semibold">Nombre</label>
                <input type="text" name="name" class="border border-gray-300 rounded-lg w-full px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Nombre del repartidor" required>
            </div>

            <!-- Email Field -->
            <div>
                <label class="block text-gray-700 font-semibold">Correo Electrónico</label>
                <input type="email" name="email" class="border border-gray-300 rounded-lg w-full px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Correo del repartidor" required>
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-gray-700 font-semibold">Contraseña</label>
                <input type="password" name="password" class="border border-gray-300 rounded-lg w-full px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Contraseña" required>
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label class="block text-gray-700 font-semibold">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" class="border border-gray-300 rounded-lg w-full px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Confirma la contraseña" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">Registrar</button>
        </form>
    </div>
</div>
@endsection
