@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4 px-3 md:py-8 md:px-4">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
        <!-- Columnas izquierda para la imagen principal y miniaturas -->
        <div class="lg:col-span-5">
            <!-- Imagen principal del producto -->
            <div class="mb-4 px-2 md:ml-4">
                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                     alt="{{ $producto->nombre }}" 
                     class="w-full h-64 sm:h-80 md:h-96 object-cover rounded-lg shadow-md">
            </div>

            <!-- Galería de miniaturas (si es que tienes más imágenes) -->
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 px-2 md:ml-4">
                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                     class="w-full h-16 sm:h-20 object-cover rounded-lg border border-gray-200" 
                     alt="{{ $producto->nombre }}">
                <!-- Añadir más imágenes aquí si es necesario -->
            </div>
        </div>

        <!-- Columna derecha para los detalles del producto -->
        <div class="lg:col-span-7 space-y-4 px-2 md:px-0">
            <!-- Título del producto -->
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 leading-tight">
                {{ $producto->nombre }}
            </h2>

            <!-- Precio y oferta (si existe un descuento) -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <p class="text-2xl sm:text-3xl text-green-600 font-semibold">
                    S/{{ number_format($producto->precio, 2) }}
                </p>
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
            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    {{ $producto->descripcion }}
                </p>
            </div>

            <!-- Opciones de cantidad disponibles -->
            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <p class="font-semibold text-gray-700 text-sm sm:text-base">
                        Disponibles: 
                        <span class="text-green-600" id="stock-display">{{ $producto->cantidad_disponible }}</span>
                    </p>
                    
                    <!-- Selector de cantidad -->
                    <div class="flex items-center justify-center sm:justify-start">
                        <button id="decrease" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 sm:px-4 py-2 rounded-l-lg transition-colors duration-200 text-lg font-bold">
                            -
                        </button>
                        <input id="quantity" 
                               name="cantidad" 
                               type="number" 
                               value="1" 
                               min="1" 
                               max="{{ $producto->cantidad_disponible }}" 
                               class="text-center w-16 sm:w-20 border-t border-b border-gray-300 px-2 py-2 text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button id="increase" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 sm:px-4 py-2 rounded-r-lg transition-colors duration-200 text-lg font-bold">
                            +
                        </button>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 mt-6">
                <a href="{{ route('tienda') }}" 
                   class="inline-block bg-transparent text-green-600 border-2 border-green-600 px-4 py-3 rounded-lg hover:bg-green-50 transition-colors duration-200 text-center font-medium text-sm sm:text-base">
                    ← Volver a la tienda
                </a>

                <!-- Formulario para agregar al carrito -->
                <form id="addToCartForm" class="add-to-cart-form flex-1" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="cantidad" value="1" id="cantidadInput">
                    <button type="submit" id="addToCartBtn"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg w-full transition-colors duration-200 font-medium text-sm sm:text-base shadow-md hover:shadow-lg">
                        🛒 Agregar al carrito
                    </button>
                </form>
            </div>

            <!-- Información adicional del agricultor (si está disponible) -->
            @if(isset($producto->agricultor))
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg mt-4">
                <h3 class="font-semibold text-green-800 mb-2 text-sm sm:text-base">
                    Información del Productor
                </h3>
                <p class="text-green-700 text-sm">
                    Cultivado por: <span class="font-medium">{{ $producto->agricultor->nombre ?? 'Agricultor local' }}</span>
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- 🔥 NOTIFICACIÓN PERSONALIZADA -->
<div id="notification" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <span id="notification-message">Producto agregado al carrito</span>
        <button onclick="hideNotification()" class="ml-4 text-white hover:text-gray-200">
            ✕
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const decreaseButton = document.getElementById('decrease');
        const increaseButton = document.getElementById('increase');
        const quantityInput = document.getElementById('quantity');
        const cantidadInput = document.getElementById('cantidadInput');
        const addToCartForm = document.getElementById('addToCartForm');
        const addToCartBtn = document.getElementById('addToCartBtn');

        // Convertir el valor máximo del input en un número a través de JavaScript
        const maxQuantity = parseInt("{{ $producto->cantidad_disponible }}");

        // Función para actualizar el input oculto
        function updateHiddenInput() {
            cantidadInput.value = quantityInput.value;
        }

        // Evento para disminuir la cantidad
        decreaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                updateHiddenInput();
            }
        });

        // Evento para aumentar la cantidad
        increaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
                updateHiddenInput();
            }
        });

        // Asegurar que la cantidad en el input oculto esté sincronizada
        quantityInput.addEventListener('input', function() {
            // Validar que el valor esté dentro del rango permitido
            let value = parseInt(this.value);
            if (value < 1) {
                this.value = 1;
            } else if (value > maxQuantity) {
                this.value = maxQuantity;
            }
            updateHiddenInput();
        });

        // Inicializar el input oculto
        updateHiddenInput();

        // 🔥 MANEJAR FORMULARIO DE AGREGAR AL CARRITO CON AJAX
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío normal

            const formData = new FormData(this);
            const url = this.action;

            // Mostrar estado de carga
            const originalText = addToCartBtn.textContent;
            addToCartBtn.textContent = '⏳ Agregando...';
            addToCartBtn.disabled = true;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ MOSTRAR NOTIFICACIÓN DE ÉXITO
                    showNotification(data.message, 'success');
                    
                    // 🔄 ACTUALIZAR CONTADOR DEL CARRITO (si tienes)
                    updateCartCounter(data.totalItems);
                    
                } else {
                    // ❌ MOSTRAR ERROR
                    showNotification(data.error || 'Error al agregar producto', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión. Intenta nuevamente.', 'error');
            })
            .finally(() => {
                // Restaurar botón
                addToCartBtn.textContent = originalText;
                addToCartBtn.disabled = false;
            });
        });
    });

    // 🎨 FUNCIÓN PARA MOSTRAR NOTIFICACIONES
    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        const messageElement = document.getElementById('notification-message');
        
        messageElement.textContent = message;
        
        // Cambiar color según el tipo
        const notificationDiv = notification.querySelector('div');
        if (type === 'success') {
            notificationDiv.className = 'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2';
        } else {
            notificationDiv.className = 'bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2';
        }
        
        // Mostrar notificación
        notification.classList.remove('hidden');
        
        // Ocultar automáticamente después de 3 segundos
        setTimeout(() => {
            hideNotification();
        }, 3000);
    }

    // Función para ocultar notificación
    function hideNotification() {
        document.getElementById('notification').classList.add('hidden');
    }

    // 🔄 FUNCIÓN PARA ACTUALIZAR CONTADOR DEL CARRITO
    function updateCartCounter(totalItems) {
        // Buscar elementos del contador de carrito (ajusta los selectores según tu layout)
        const cartCounters = document.querySelectorAll('.cart-count, .carrito-count, #cart-counter, [data-cart-count]');
        
        cartCounters.forEach(counter => {
            counter.textContent = totalItems;
            
            // Opcional: Animación de rebote
            counter.style.transform = 'scale(1.2)';
            setTimeout(() => {
                counter.style.transform = 'scale(1)';
            }, 200);
        });
    }

    // FUNCIÓN EXISTENTE (mantener)
    function updateProductStock(productId) {
        fetch('/producto/' + productId)
            .then(response => response.json())
            .then(data => {
                // Actualizar la cantidad disponible en la vista
                const stockElement = document.querySelector('#producto-' + productId + ' .cantidad-disponible');
                if (stockElement) {
                    stockElement.textContent = 'Disponibles: ' + data.cantidad_disponible;
                }
            })
            .catch(error => {
                console.error('Error al actualizar stock:', error);
            });
    }
</script>

@endsection