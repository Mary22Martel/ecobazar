@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    
    <!-- Header principal con información de semana de feria -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg animate-fade-in">
        <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-bounce-slow">
                <span class="text-2xl sm:text-3xl">🌱</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">¡Hola {{ Auth::user()->name }}!</h1>
            <div class="mt-3 bg-white/10 rounded-lg p-3">
                <p class="text-sm text-green-100">
                    <strong>Para esta semana:</strong> {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m') }} 
                    • <strong>Sábado:</strong> {{ $diaEntrega->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- BOTÓN AGREGAR PRODUCTO - NUEVO AL INICIO -->
    <div class="mb-6">
        <a href="{{ route('productos.create') }}" 
           class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center text-base group">
            <span>➕ Agregar Nuevo Producto</span>
            <div class=" bg-white/20 px-5 py-1 ml-4 rounded-full text-xs">
                Tienes {{ $totalProductos }} productos
            </div>
        </a>
    </div>

    <!-- Botón para editar perfil -->
    <div class="mb-6">
        <a href="{{ route('agricultor.perfil') }}" 
        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center text-base">
            <span>👤 Editar mi Perfil</span>
        </a>
    </div>

    <!-- RECORDATORIO SEMANAL -->
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-400 rounded-lg p-4 shadow-sm animate-fade-in-up">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center animate-bounce-slow">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm text-amber-700 leading-relaxed mb-2">
                    <strong>Revisa las cantidades de tus productos cada semana según lo que tendrás disponible para vender. Esto ayuda a los clientes a saber qué pueden comprar.
                </p></strong> 
            </div>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="flex-shrink-0 text-amber-400 hover:text-amber-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- FLUJO PRINCIPAL 1-2-3-4 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6 mb-6 sm:mb-8">

        <!-- PASO 1: Actualizar productos -->
        <a href="{{ route('productos.index') }}"
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-green-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-right relative">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                1
            </div>
            <div class="flex items-center">
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">ACTUALIZA TUS PRODUCTOS</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Revisa la cantidad de stock de cada producto para esta semana</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                        Primer paso
                    </div>
                </div>
            </div>
        </a>
        
        <!-- PASO 2: Pedidos por armar -->
        <a href="{{ route('agricultor.pedidos_pendientes', ['semana' => 0]) }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-left relative {{ $pendientes > 0 ? 'border-green-400 ring-2 ring-red-100 animate-pulse-border' : 'border-green-300' }}">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 {{ $pendientes > 0 ? 'bg-gradient-to-br from-green-500 to-green-600 animate-pulse' : 'bg-gradient-to-br from-green-500 to-green-600' }} text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                2
            </div>
            <div class="flex items-center">
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS POR ARMAR</h2>
                    @if($pendientes > 0)
                        <div class="flex items-center space-x-2">
                            <div class="bg-green-500 text-white rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center animate-pulse shadow-lg">
                                <span class="text-sm sm:text-base font-bold">{{ $pendientes }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-red-600 font-semibold">{{ $pendientes == 1 ? 'pedido esperando' : 'pedidos esperando' }}</p>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">¡De esta semana!</p>
                        <div class="mt-2 inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1 animate-pulse"></span>
                            Segundo paso - ¡Urgente!
                        </div>
                    @else
                        <p class="text-xs sm:text-sm text-gray-400">Aún no hay pedidos esta semana</p>
                        <div class="mt-2 inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                            Segundo paso
                        </div>
                    @endif
                </div>
                @if($pendientes > 0)
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                    </div>
                @endif
            </div>
        </a>

        <!-- PASO 3: Pedidos listos -->
        <a href="{{ route('agricultor.pedidos_listos', ['semana' => 0]) }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-green-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-right relative">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                3
            </div>
            <div class="flex items-center">
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">PEDIDOS LISTOS</h2>
                    <p class="text-sm sm:text-base text-green-600 font-semibold">{{ $listos }} {{ $listos == 1 ? 'pedido preparado' : 'pedidos preparados' }}</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                        Tercer paso - Finalizado
                    </div>
                </div>
            </div>
        </a>

        <!-- PASO 4: Ver pagos -->
        <a href="{{ route('agricultor.pagos', ['semana' => 0]) }}" 
           class="block bg-white rounded-xl shadow-lg border-l-4 sm:border-l-8 border-green-400 p-4 sm:p-6 hover:shadow-2xl transform hover:scale-105 transition-all duration-300 animate-slide-in-left relative">
            <!-- Número de secuencia -->
            <div class="absolute -top-3 -left-3 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg border-2 border-white z-10">
                4
            </div>
            <div class="flex items-center">
                <div class="ml-4 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">REVISA TUS PAGOS</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Ver dinero ganado por semanas</p>
                    <div class="mt-2 inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                        Cuarto paso - Cobro
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Guía rápida mejorada con efectos -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 sm:p-6 animate-fade-in-up">
        <h3 class="text-lg sm:text-xl font-bold text-green-800 mb-4 flex items-center">
            <span class="mr-2 animate-bounce-slow">💡</span> ¿Cómo funciona tu trabajo semanal?
        </h3>
        
        <div class="space-y-3">
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">1</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Actualiza tus productos</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Agrega los productos que deseas vender y actualiza cada semana el stock de los productos que tienes para vender esa semana</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">2</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Domingo a Viernes</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Los clientes hacen pedidos durante la semana. Tú los verás en "PEDIDOS POR ARMAR" si hay números rojos.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.2s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">3</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Prepara los productos exactos</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Revisa las cantidades y marca como "listo" cuando termines cada pedido.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.3s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">4</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Sábado {{ $diaEntrega->format('d/m') }} - Entrega en feria</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Lleva todos tus productos preparados a la feria para la entrega.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 animate-slide-in-left" style="animation-delay: 0.4s;">
                <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center text-sm sm:text-base font-bold shadow-lg">5</div>
                <div>
                    <p class="text-sm sm:text-base text-green-800"><strong>Revisa tus pagos semanales</strong></p>
                    <p class="text-xs sm:text-sm text-green-600">Cada semana se te pagará por todos los pedidos entregados exitosamente.</p>
                </div>
            </div>
        </div>
    </div>

    @if($pendientes > 0)
    <!-- Mensaje de urgencia animado -->
    <div class="mt-4 sm:mt-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-400 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl animate-bounce">🚨</span>
            </div>
            <div class="ml-3">
                <h4 class="text-base sm:text-lg font-bold text-red-800">¡Atención!</h4>
                <p class="text-sm sm:text-base text-red-700">
                    Tienes <strong>{{ $pendientes }}</strong> {{ $pendientes == 1 ? 'pedido que necesita' : 'pedidos que necesitan' }} preparación esta semana.
                    <a href="{{ route('agricultor.pedidos_pendientes', ['semana' => 0]) }}" class="underline font-semibold hover:text-red-900 animate-pulse">
                        Ver ahora →
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
/* Todas las animaciones existentes se mantienen igual */
@keyframes pulse-border {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes slide-in-right {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-pulse-border {
    animation: pulse-border 2s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}

.animate-slide-in-right {
    animation: slide-in-right 0.6s ease-out;
}

.animate-slide-in-left {
    animation: slide-in-left 0.6s ease-out;
}

/* Efectos hover mejorados */
.block:hover .absolute {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Responsive para los números */
@media (max-width: 640px) {
    .absolute.-top-3.-left-3 {
        top: -0.5rem;
        left: -0.5rem;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes slideInLeft {
    from { 
        opacity: 0; 
        transform: translateX(-30px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

@keyframes slideInRight {
    from { 
        opacity: 0; 
        transform: translateX(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

@keyframes bounceSlow {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

.animate-slide-in-left {
    animation: slideInLeft 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slideInRight 0.6s ease-out;
}

.animate-bounce-slow {
    animation: bounceSlow 2s infinite;
}
</style>

@endsection