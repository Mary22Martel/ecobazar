@extends('layouts.app')

@section('content')
<!-- Vista Desktop (sin cambios) -->
<div class="min-h-screen  hidden lg:flex">
    
    <!-- Panel izquierdo - Información y branding -->
    <div class="lg:flex lg:w-1/2 relative bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 p-10 flex-col justify-between overflow-hidden">
        
        <!-- Patrón de fondo con bolitas sutiles -->
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="farmPattern" x="0" y="0" width="15" height="15" patternUnits="userSpaceOnUse">
                        <circle cx="7.5" cy="7.5" r="1" fill="currentColor" opacity="0.6"/>
                        <circle cx="3" cy="3" r="0.5" fill="currentColor" opacity="0.4"/>
                        <circle cx="12" cy="12" r="0.5" fill="currentColor" opacity="0.4"/>
                        <circle cx="1" cy="10" r="0.3" fill="currentColor" opacity="0.3"/>
                        <circle cx="14" cy="5" r="0.3" fill="currentColor" opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#farmPattern)"/>
            </svg>
        </div>
        
        <!-- Logo y título principal -->
      <div class="relative z-10 flex flex-col items-center justify-start min-h-screen px-4  text-center">
            <div class="space-y-6 max-w-2xl">
                <h2 class="text-5xl font-bold text-white leading-tight">
                    🌱 Del campo<br>
                    <span class="text-green-200">a tu mesa, directo y fresco</span>
                </h2>

                <div class="flex justify-center">
                    <div class="mt-6 bg-white/20 backdrop-blur-md rounded-xl p-4 text-center">
                        <p class="text-white font-semibold text-lg">📍 Segundo parque de Paucarbambilla</p>
                        <p class="text-green-100 text-sm">Todos los sábados de 7:00 am a 12:00 pm</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-white mb-2">Feria Agrícola Digital</h3>
                    <p class="text-green-100 text-base leading-relaxed mb-4">
                        La finalidad es de promocionar el consumo de productos sanos, a base de los productos agroecológicos que se expenden en el segundo parque de Paucarbambilla.
                    </p>
                    <div class="space-y-3 text-left mx-auto w-fit">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-200 rounded-full"></div>
                            <span class="text-green-100">Productos frescos todos los sábados</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-200 rounded-full"></div>
                            <span class="text-green-100">Directamente del productor</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-200 rounded-full"></div>
                            <span class="text-green-100">Apoyando la economía local</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-200 rounded-full"></div>
                            <span class="text-green-100">Calidad garantizada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Elemento decorativo -->
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/10 rounded-full translate-x-16 translate-y-16"></div>
        <div class="absolute top-20 right-20 w-20 h-20 bg-white/10 rounded-full"></div>
    </div>
    
    <!-- Panel derecho - Formulario Desktop -->
    <div class="w-full lg:w-1/2 flex items-start justify-center p-4 bg-white relative pt-10">
        
        <!-- Elementos decorativos sutiles con animación -->
        <div class="absolute top-8 right-8 w-24 h-24 bg-green-50 rounded-full opacity-50 animate-float"></div>
        <div class="absolute bottom-12 left-8 w-16 h-16 bg-emerald-50 rounded-full opacity-50 animate-float-delayed"></div>
        <div class="absolute top-32 right-20 w-12 h-12 bg-green-100 rounded-full opacity-30 animate-float-slow"></div>
        <div class="absolute bottom-32 right-12 w-8 h-8 bg-emerald-100 rounded-full opacity-40 animate-bounce-slow"></div>
        
        <div class="w-full max-w-sm relative z-10">
            
            <!-- Título del formulario -->
            <div class="mb-4">
                <h2 class="text-3xl font-bold text-green-600 mb-2">Bienvenido</h2>
                <p class="text-gray-600">Inicia sesión en tu cuenta</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Campo Email con diseño moderno -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-3">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus
                               class="block w-full px-0 py-3 text-gray-900 bg-transparent border-0 border-b-2 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200 focus:border-green-500' }} focus:outline-none focus:ring-0 transition-colors duration-200 text-lg"
                               placeholder="tu@correo.com">
                        <div class="absolute bottom-0 left-0 h-0.5 bg-gradient-to-r from-green-500 to-emerald-500 scale-x-0 transition-transform duration-200 origin-left group-focus-within:scale-x-100"></div>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Campo Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-3">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="block w-full px-0 py-3 text-gray-900 bg-transparent border-0 border-b-2 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200 focus:border-green-500' }} focus:outline-none focus:ring-0 transition-colors duration-200 text-lg"
                               placeholder="Tu contraseña">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Opciones adicionales -->
                <div class="flex items-center py-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               {{ old('remember') ? 'checked' : '' }}
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">Mantener sesión</span>
                    </label>
                </div>
                
                <!-- Botón de login -->
                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Iniciar Sesión</span>
                    </button>
                    
                    <!-- Separador -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">¿No tienes cuenta?</span>
                        </div>
                    </div>
                    
                    <!-- Botón de registro -->
                    <a href="{{ route('register') }}" 
                       class="w-full border-2 border-green-600 text-green-600 hover:bg-green-50 font-semibold py-4 px-6 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 inline-flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span>Crear cuenta</span>
                    </a>
                </div>
            </form>
            
            <!-- Footer del formulario -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Al continuar, aceptas nuestros términos de servicio y política de privacidad
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Vista Móvil (estilo register) -->
<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 flex items-center justify-center p-4 lg:hidden">
    <div class="w-full max-w-lg">
        <!-- Card principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header con branding -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 sm:px-8 py-4 text-center">
                <div class="mb-4">
                    <div class="w-24 h-24 flex items-center justify-center mx-auto mb-4 relative group rounded-full bg-white/20">
                        <img src="{{ asset('images/logox.png') }}" alt="Punto Verde Logo" 
                             class="h-16 w-auto transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute -inset-1 bg-gradient-green rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Punto Verde</h1>
                    <p class="text-green-100 text-sm sm:text-base">Bienvenido de vuelta</p>
                </div>
            </div>

            <!-- Formulario móvil --> 
            <div class="px-6 sm:px-8 py-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Iniciar Sesión</h2>
                    <p class="text-gray-600 text-sm sm:text-base">Ingresa a tu cuenta</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Campo de email mejorado -->
                    <div class="space-y-2">
                        <label for="mobile-email" class="block text-sm font-semibold text-gray-700">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input id="mobile-email" type="email" 
                                   class="w-full pl-10 pr-4 py-3 border {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus 
                                   placeholder="tu@correo.com">
                        </div>
                        @error('email')
                            <div class="flex items-center space-x-2 text-red-600 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Campo de contraseña mejorado -->
                    <div class="space-y-2">
                        <label for="mobile-password" class="block text-sm font-semibold text-gray-700">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="mobile-password" type="password" 
                                   class="w-full pl-10 pr-4 py-3 border {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="Tu contraseña">
                        </div>
                        @error('password')
                            <div class="flex items-center space-x-2 text-red-600 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Recordar sesión -->
                    <div class="flex items-center justify-between py-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="remember" 
                                   {{ old('remember') ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                        </label>
                       
                    </div>

                    <!-- Botón de login mejorado -->
                    <div>
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg hover:shadow-xl text-sm sm:text-base">
                            <span class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Iniciar Sesión</span>
                            </span>
                        </button>
                    </div>

                    <!-- Separador -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">o</span>
                        </div>
                    </div>

                    <!-- Enlace para registro -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-3">
                            ¿No tienes una cuenta?
                        </p>
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center w-full bg-white border-2 border-green-600 text-green-600 font-semibold py-3 px-4 rounded-lg hover:bg-green-50 transition-all duration-200 transform hover:scale-105 text-sm sm:text-base">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <span>Crear Cuenta</span>
                            </span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer informativo -->
        <div class="text-center mt-8 px-4">
            <p class="text-sm text-gray-600 mb-2">
                🌱 Accede a productos frescos y apoya el comercio local
            </p>
            <div class="flex justify-center space-x-4 text-xs text-gray-500">
                <span>🔒 Seguro</span>
                <span>🤝 Confiable</span>
                <span>🌍 Sostenible</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Animaciones suaves */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-8px) scale(1.05); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 4s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-float-slow {
    animation: float-slow 5s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-bounce-slow {
    animation: bounce-slow 2.5s ease-in-out infinite;
    animation-delay: 1.5s;
}

.w-full.max-w-sm {
    animation: fadeIn 0.6s ease-out;
}

.bg-white {
    animation: fadeInUp 0.6s ease-out;
}

/* Focus states para inputs underlined (desktop) */
input:focus + div {
    transform: scaleX(1);
}

input:focus {
    border-color: rgb(34 197 94);
}

/* Focus states para inputs con borde (móvil) */
.lg\:hidden input:focus, .lg\:hidden select:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
}

/* Hover effects */
button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.lg\:hidden button:hover {
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
}

/* Estados activos */
button:active {
    transform: translateY(0);
}

.lg\:hidden button:active {
    transform: scale(0.98);
}

/* Estados de error mejorados */
.border-red-300 {
    animation: shake 0.5s ease-in-out;
}

/* Mejoras en accesibilidad */
input:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Transiciones globales */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects para el botón de registro */
a:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
}

/* Responsive - Ocultar vista desktop en móvil */
@media (max-width: 1023px) {
    .hidden.lg\:flex {
        display: none !important;
    }
}
</style>
@endsection