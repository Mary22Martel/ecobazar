@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <!-- Título -->
        <h2 class="text-center text-3xl font-bold text-gray-900">Crear Cuenta   </h2>

        <!-- Formulario -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Nombre -->
            <div class="relative">
                <label for="name" class="sr-only">Nombre</label>
                <input id="name" type="text" 
                    class="{{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nombre">

                @error('name')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div class="relative">
                <label for="email" class="sr-only">Correo</label>
                <input id="email" type="email" 
                    class="{{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                    name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Correo">

                @error('email')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="relative">
                <label for="password" class="sr-only">Contraseña</label>
                <input id="password" type="password" 
                    class="{{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                    name="password" required autocomplete="new-password" placeholder="Contraseña">

                @error('password')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="relative">
                <label for="password-confirm" class="sr-only">Confirmar Contraseña</label>
                <input id="password-confirm" type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" 
                    name="password_confirmation" required autocomplete="new-password" placeholder="Confirmar Contraseña">
            </div>

            <!-- Selección de Rol -->
            <div class="relative">
                <label for="role" class="sr-only">Rol</label>
                <select id="role" 
                    class="{{ $errors->has('role') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                    name="role" required>
                    <option value="" disabled selected>Seleccione un Rol</option>
                    <option value="student">Estudiante</option>
                    <option value="teacher">Profesor</option>
                    <option value="cliente">Cliente</option>
                    <option value="agricultor">Agricultor</option>
                    <option value="repartidor">Repartidor</option>
                </select>

                @error('role')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Botón de registro -->
            <div>
                <button type="submit" 
                    class="w-full px-4 py-3 text-white bg-green-600 rounded-md font-bold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
