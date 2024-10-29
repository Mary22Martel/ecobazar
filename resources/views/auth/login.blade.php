@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <!-- Título -->
        <h2 class="text-center text-3xl font-bold text-gray-900">Iniciar Sesion</h2>

        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Campo de correo -->
            <div class="relative">
                <label for="email" class="sr-only">Correo</label>
                <input id="email" type="email" 
                       class="{{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="correo">

                @error('email')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Campo de contraseña -->
            <div class="relative">
                <label for="password" class="sr-only">Contraseña</label>
                <input id="password" type="password" 
                       class="{{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} w-full px-4 py-3 border rounded-md focus:ring-green-500 focus:border-green-500" 
                       name="password" required autocomplete="current-password" placeholder="contraseña">
                
                @error('password')
                    <span class="text-red-500 text-sm mt-2 block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                    Mantener Sesion Activa
                </label>
            </div>

            <!-- Botón de inicio de sesión -->
            <div>
                <button type="submit" class="w-full px-4 py-3 text-white bg-green-600 rounded-md font-bold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('Login') }}
                </button>
            </div>

            <!-- Enlace para registrar -->
            <div class="text-center text-sm text-gray-600">
                No tienes una cuenta? <a href="{{ route('register') }}" class="text-green-600 font-medium hover:text-green-500">Registrarse</a>
            </div>
        </form>
    </div>
</div>
@endsection
