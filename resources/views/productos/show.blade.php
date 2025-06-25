@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4 px-3 md:py-8 md:px-4">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
        <!-- Columnas izquierda para la imagen principal y miniaturas -->
        <div class="lg:col-span-5">
            <!-- Imagen principal del producto -->
            <div class="mb-4 px-2 md:ml-4">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                         alt="{{ $producto->nombre }}" 
                         class="w-full h-64 sm:h-80 md:h-96 object-cover rounded-lg shadow-md">
                @else
                    <div class="w-full h-64 sm:h-80 md:h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center rounded-lg shadow-md">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Galería de miniaturas (si es que tienes más imágenes) -->
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 px-2 md:ml-4">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                         class="w-full h-16 sm:h-20 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75 transition-opacity" 
                         alt="{{ $producto->nombre }}">
                @endif
                <!-- Placeholder para más imágenes -->
                <div class="w-full h-16 sm:h-20 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Columna derecha para los detalles del producto -->
        <div class="lg:col-span-7 space-y-4 px-2 md:px-0">
            <!-- Breadcrumb -->
            <nav class="flex text-sm text-gray-500 mb-4">
                <a href="{{ route('tienda') }}" class="hover:text-green-600 transition-colors">Tienda</a>
                <span class="mx-2">/</span>
                @if($producto->categoria)
                    <a href="{{ route('productos.filtrarPorCategoria', $producto->categoria->id) }}" class="hover:text-green-600 transition-colors">{{ $producto->categoria->nombre }}</a>
                    <span class="mx-2">/</span>
                @endif
                <span class="text-gray-800">{{ $producto->nombre }}</span>
            </nav>

            <!-- Título del producto -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 leading-tight">
                {{ $producto->nombre }}
            </h1>

            <!-- Info del productor -->
            @if($producto->user)
                <div class="flex items-center text-gray-600 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Producido por: <strong>{{ $producto->user->name }}</strong></span>
                </div>
            @endif

            <!-- Badge de disponibilidad -->
            <div class="flex items-center gap-2">
                @if($producto->cantidad_disponible > 0)
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                        ✅ Disponible
                    </span>
                    @if($producto->cantidad_disponible <= 5)
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">
                            ⚠️ ¡Últimas {{ $producto->cantidad_disponible }} unidades!
                        </span>
                    @endif
                @else
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                        ❌ Agotado
                    </span>
                @endif
            </div>

            <!-- Precio y oferta -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <p class="text-3xl sm:text-4xl text-green-600 font-bold">
                    S/{{ number_format($producto->precio, 2) }}
                </p>
                @if($producto->medida)
                    <span class="text-gray-500 text-sm">por {{ $producto->medida->nombre }}</span>
                @endif
                @if($producto->descuento)
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-red-500 bg-red-100 px-2 py-1 rounded-lg text-sm font-bold">
                            {{ $producto->descuento }}% OFF
                        </span>
                        <p class="text-gray-500 line-through text-sm sm:text-base">
                            S/{{ number_format($producto->precio * (1 + $producto->descuento / 100), 2) }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Descripción del producto -->
            @if($producto->descripcion)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-2">Descripción</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                        {{ $producto->descripcion }}
                    </p>
                </div>
            @endif

            <!-- Información adicional -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($producto->categoria)
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-blue-800 text-sm">Categoría</h4>
                        <p class="text-blue-700 text-sm">{{ $producto->categoria->nombre }}</p>
                    </div>
                @endif
                <div class="bg-green-50 p-3 rounded-lg">
                    <h4 class="font-semibold text-green-800 text-sm">Stock disponible</h4>
                    <p class="text-green-700 text-sm font-bold">{{ $producto->cantidad_disponible }} unidades</p>
                </div>
            </div>

            @if($producto->cantidad_disponible > 0)
                <!-- Selector de cantidad -->
                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <p class="font-semibold text-gray-700 text-sm sm:text-base">
                            Cantidad:
                        </p>
                        
                        <!-- Selector de cantidad mejorado -->
                        <div class="flex items-center justify-center sm:justify-start">
                            <button id="decrease" 
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-l-lg transition-colors duration-200 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-500">
                                -
                            </button>
                            <input id="quantity" 
                                   name="cantidad" 
                                   type="number" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $producto->cantidad_disponible }}" 
                                   class="text-center w-20 border-t border-b border-gray-300 px-2 py-2 text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button id="increase" 
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-r-lg transition-colors duration-200 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-500">
                                +
                            </button>
                        </div>

                        <!-- Precio total dinámico -->
                        <div class="text-center sm:text-left">
                            <span class="text-sm text-gray-600">Total: </span>
                            <span id="total-price" class="text-lg font-bold text-green-600">
                                S/{{ number_format($producto->precio, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 mt-6">
                    <a href="{{ route('tienda') }}" 
                       class="inline-block bg-transparent text-green-600 border-2 border-green-600 px-6 py-3 rounded-lg hover:bg-green-50 transition-colors duration-200 text-center font-medium text-sm sm:text-base">
                        ← Volver a la tienda
                    </a>

                    <!-- Formulario para agregar al carrito -->
                    <form id="addToCartForm" class="add-to-cart-form flex-1" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="cantidad" value="1" id="cantidadInput">
                        <button type="submit" id="addToCartBtn"
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg w-full transition-all duration-200 font-medium text-sm sm:text-base shadow-md hover:shadow-lg transform hover:scale-105 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            Agregar al carrito
                        </button>
                    </form>
                </div>
            @else
                <!-- Producto agotado -->
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-red-800">Producto agotado</h3>
                            <p class="text-red-600 text-sm">Este producto no está disponible en este momento.</p>
                        </div>
                        <a href="{{ route('tienda') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                            Ver otros productos
                        </a>
                    </div>
                </div>
            @endif

            <!-- Información adicional del agricultor -->
            @if($producto->user)
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg mt-6">
                    <h3 class="font-semibold text-green-800 mb-3 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información del Productor
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-green-700">
                                <strong>Nombre:</strong> {{ $producto->user->name }}
                            </p>
                            @if($producto->user->email)
                                <p class="text-green-700 mt-1">
                                    <strong>Contacto:</strong> {{ $producto->user->email }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                🌱 Producto agroecológico
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const decreaseButton = document.getElementById('decrease');
    const increaseButton = document.getElementById('increase');
    const quantityInput = document.getElementById('quantity');
    const cantidadInput = document.getElementById('cantidadInput');
    const totalPriceElement = document.getElementById('total-price');
    const addToCartForm = document.getElementById('addToCartForm');
    const addToCartBtn = document.getElementById('addToCartBtn');

    // Convertir valores necesarios
    const maxQuantity = parseInt("{{ $producto->cantidad_disponible }}");
    const unitPrice = parseFloat("{{ $producto->precio }}");

    // Función para actualizar el input oculto y precio total
    function updateValues() {
        const quantity = parseInt(quantityInput.value);
        cantidadInput.value = quantity;
        
        // Actualizar precio total
        const totalPrice = unitPrice * quantity;
        totalPriceElement.textContent = 'S/' + totalPrice.toFixed(2);
        
        // Actualizar estado de botones
        decreaseButton.disabled = quantity <= 1;
        increaseButton.disabled = quantity >= maxQuantity;
    }

    // Evento para disminuir la cantidad
    decreaseButton.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateValues();
        }
    });

    // Evento para aumentar la cantidad
    increaseButton.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < maxQuantity) {
            quantityInput.value = currentValue + 1;
            updateValues();
        }
    });

    // Evento para cambios manuales en el input
    quantityInput.addEventListener('input', function() {
        let value = parseInt(this.value);
        if (isNaN(value) || value < 1) {
            this.value = 1;
        } else if (value > maxQuantity) {
            this.value = maxQuantity;
        }
        updateValues();
    });

    // Inicializar valores
    updateValues();

    // 🔥 MANEJAR FORMULARIO DE AGREGAR AL CARRITO CON JQUERY AJAX
    let isAddingToCart = false;
    
    $('#addToCartForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        if (isAddingToCart) return false;

        let form = $(this);
        let button = $('#addToCartBtn');
        let originalText = button.html();
        let cantidadAgregada = parseInt($('#quantity').val()) || 1;
        let nombreProducto = "{{ $producto->nombre }}";

        // Estado de carga
        isAddingToCart = true;
        button.prop('disabled', true).html(`
            <svg class="animate-spin w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Agregando...
        `);

        // AJAX con jQuery
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    // Actualizar TODOS los badges del carrito
                    $('#cart-badge').text(response.totalItems || 0);
                    $('#floating-cart-badge').text(response.totalItems || 0);
                    $('#cart-badge-mobile').text(response.totalItems || 0);

                    // Resetear cantidad del formulario
                    $('#quantity').val(1);
                    updateValues();

                    // Animación en el botón flotante (si existe)
                    if ($('#floating-cart-badge').length) {
                        $('#floating-cart-badge').addClass('cart-bounce');
                        setTimeout(() => {
                            $('#floating-cart-badge').removeClass('cart-bounce');
                        }, 600);
                    }

                    // Mostrar notificación de éxito
                    Swal.fire({
                        title: '¡Producto añadido!',
                        text: `${cantidadAgregada} unidad${cantidadAgregada > 1 ? 'es' : ''} de ${nombreProducto} agregado al carrito`,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2500,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true,
                        background: '#10b981',
                        color: '#ffffff',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Error al agregar el producto';
                
                if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado';
                } else if (xhr.status === 0) {
                    errorMessage = 'Sin conexión a internet';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 401) {
                    // ✅ MANEJO DEL ERROR 401 (NO AUTENTICADO)
                    Swal.fire({
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para agregar productos al carrito',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3B82F6',
                        confirmButtonText: 'Ir a login',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#3B82F6',
                });
            },
            complete: function() {
                // Restaurar el botón SIEMPRE
                button.prop('disabled', false).html(originalText);
                isAddingToCart = false;
            }
        });
    });
});

// Agregar animación CSS si no existe
if (!document.querySelector('#cart-animation-styles')) {
    const style = document.createElement('style');
    style.id = 'cart-animation-styles';
    style.textContent = `
        .cart-bounce {
            animation: cartBounce 0.6s ease;
        }
        
        @keyframes cartBounce {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(0.9); }
            75% { transform: scale(1.1); }
        }
    `;
    document.head.appendChild(style);
}
</script>
@endsection