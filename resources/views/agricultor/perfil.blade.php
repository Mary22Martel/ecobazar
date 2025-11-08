@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-3xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 mb-6 shadow-lg">
        <div class="text-center">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">👤</span>
            </div>
            <h1 class="text-3xl font-bold">Mi Perfil</h1>
            <p class="text-green-100 mt-2">Actualiza tu información personal</p>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <span class="text-2xl mr-3">✓</span>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <span class="text-2xl mr-3">✕</span>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('agricultor.perfil.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $agricultor->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $agricultor->email) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-sm font-bold text-gray-700 mb-2">
                    Teléfono
                </label>
                <input type="text" 
                       id="telefono" 
                       name="telefono" 
                       value="{{ old('telefono', $agricultor->telefono) }}"
                       placeholder="Ej: 987654321"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('telefono') border-red-500 @enderror">
                @error('telefono')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rol (solo lectura) -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tipo de cuenta
                </label>
                <div class="px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                    <span class="inline-flex items-center">
                        <span class="text-lg mr-2">🌱</span>
                        {{ ucfirst($agricultor->role) }}
                    </span>
                </div>
            </div>

            <!-- Información de cuenta -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Cuenta creada:</span>
                        <p class="font-semibold text-gray-800">{{ $agricultor->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">ID de usuario:</span>
                        <p class="font-semibold text-gray-800">#{{ $agricultor->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    💾 Guardar cambios
                </button>
                <a href="{{ route('agricultor.dashboard') }}" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg text-center transition-all duration-300">
                    ← Volver al inicio
                </a>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4">
        <div class="flex items-start">
            <span class="text-2xl mr-3">ℹ️</span>
            <div>
                <p class="text-sm text-blue-800">
                    <strong>Nota:</strong> Si necesitas cambiar tu contraseña o tienes problemas con tu cuenta, contacta al administrador del sistema.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}
</style>
@endsection