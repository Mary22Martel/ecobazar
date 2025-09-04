{{-- resources/views/admin/usuarios/crear.blade.php --}}
@extends('layouts.app2')

@section('title', 'Crear Usuario')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navegación de migas -->
        <nav class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('admin.usuarios.index') }}" class="hover:text-gray-700">Usuarios</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 font-medium">Crear Usuario</span>
            </div>
        </nav>

        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Crear Nuevo Usuario</h1>
                    <p class="mt-2 text-sm md:text-base text-gray-600">Registra una nueva cuenta de agricultor o repartidor</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.usuarios.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver a la Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200 bg-green-50">
                <h2 class="text-lg md:text-xl font-semibold text-green-900 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Datos del Nuevo Usuario
                </h2>
            </div>
            
            <div class="p-4 md:p-6">
                <form action="{{ route('admin.usuarios.guardar') }}" method="POST" id="crearUsuarioForm">
                    @csrf
                    
                    <!-- Alertas de error -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <h6 class="font-semibold">Errores encontrados:</h6>
                                    <ul class="mt-1 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información básica -->
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Nombre Completo *
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-300 @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ej: Juan Pérez García"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Correo Electrónico *
                                </label>
                                <input type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-300 @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="usuario@puntoverde.com"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('telefono') border-red-300 @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}" 
                                       placeholder="Ej: 962123456">
                                @error('telefono')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Campo opcional</p>
                            </div>
                        </div>
                        
                        <!-- Rol y contraseña -->
                        <div class="space-y-4">
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Rol del Usuario *
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('role') border-red-300 @enderror appearance-none bg-white" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="agricultor" {{ old('role') == 'agricultor' ? 'selected' : '' }}>
                                        Agricultor - Puede vender productos
                                    </option>
                                    <option value="repartidor" {{ old('role') == 'repartidor' ? 'selected' : '' }}>
                                        Repartidor - Realiza entregas
                                    </option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Contraseña *
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-300 @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Mínimo 8 caracteres"
                                           required>
                                    <button type="button" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center" 
                                            id="togglePassword">
                                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">La contraseña debe tener al menos 8 caracteres</p>
                            </div>
                            
                            <!-- Botón para generar contraseña -->
                            <div>
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100" 
                                        id="generarPassword">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Generar Contraseña Segura
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información sobre el rol -->
                    <div id="roleInfo" class="mt-6 hidden">
                        <div id="agricultorInfo" class="role-info hidden">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h6 class="flex items-center text-green-800 font-semibold">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Agricultor
                                </h6>
                                <p class="text-green-700 mt-1">Este usuario podrá:</p>
                                <ul class="mt-2 text-sm text-green-600 list-disc list-inside space-y-1">
                                    <li>Crear y gestionar productos para la venta</li>
                                    <li>Ver pedidos de sus productos</li>
                                    <li>Confirmar cuando los pedidos están listos</li>
                                    <li>Ver reportes de sus ventas y ganancias</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div id="repartidorInfo" class="role-info hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h6 class="flex items-center text-blue-800 font-semibold">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                    Repartidor
                                </h6>
                                <p class="text-blue-700 mt-1">Este usuario podrá:</p>
                                <ul class="mt-2 text-sm text-blue-600 list-disc list-inside space-y-1">
                                    <li>Ver pedidos asignados para entrega</li>
                                    <li>Marcar pedidos como en proceso de entrega</li>
                                    <li>Confirmar entregas completadas</li>
                                    <li>Ver reportes de sus entregas realizadas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.usuarios.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors" 
                                id="submitBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span id="submitText">Crear Usuario</span>
                            <svg class="animate-spin w-4 h-4 ml-2 hidden" id="submitSpinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        
        const svg = togglePassword.querySelector('svg');
        if (type === 'password') {
            svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        } else {
            svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            `;
        }
    });
    
    // Generar contraseña segura
    const generarPasswordBtn = document.getElementById('generarPassword');
    generarPasswordBtn.addEventListener('click', function() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        passwordField.value = password;
        passwordField.type = 'text';
        
        const svg = togglePassword.querySelector('svg');
        svg.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
        `;
        
        // Crear alerta
        const existingAlert = document.querySelector('.password-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'mt-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg password-alert';
        alertDiv.innerHTML = `
            <div class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-grow">
                    <strong>Contraseña generada:</strong> ${password}
                    <br><small>Asegúrate de compartir esta contraseña con el usuario de forma segura</small>
                </div>
                <button type="button" class="ml-auto flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        generarPasswordBtn.parentNode.insertBefore(alertDiv, generarPasswordBtn.nextSibling);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 10000);
    });
    
    // Mostrar información del rol
    const roleSelect = document.getElementById('role');
    const roleInfo = document.getElementById('roleInfo');
    const agricultorInfo = document.getElementById('agricultorInfo');
    const repartidorInfo = document.getElementById('repartidorInfo');
    
    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        
        roleInfo.classList.add('hidden');
        agricultorInfo.classList.add('hidden');
        repartidorInfo.classList.add('hidden');
        
        if (selectedRole) {
            roleInfo.classList.remove('hidden');
            
            if (selectedRole === 'agricultor') {
                agricultorInfo.classList.remove('hidden');
            } else if (selectedRole === 'repartidor') {
                repartidorInfo.classList.remove('hidden');
            }
        }
    });
    
    // Validación del formulario
    const form = document.getElementById('crearUsuarioForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitSpinner.classList.remove('hidden');
        
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const role = document.getElementById('role').value;
        
        if (!name || !email || !password || !role) {
            e.preventDefault();
            alert('Por favor, completa todos los campos obligatorios');
            
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitSpinner.classList.add('hidden');
        } else if (password.length < 8) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 8 caracteres');
            
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitSpinner.classList.add('hidden');
        }
    });
    
    // Auto-completar email basado en el nombre
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    
    nameField.addEventListener('blur', function() {
        if (this.value && !emailField.value) {
            const cleanName = this.value.toLowerCase()
                                     .replace(/[^a-z0-9\s]/g, '')
                                     .replace(/\s+/g, '.')
                                     .substring(0, 20);
            if (cleanName) {
                emailField.value = cleanName + '@puntoverde.com';
            }
        }
    });
});
</script>
@endsection