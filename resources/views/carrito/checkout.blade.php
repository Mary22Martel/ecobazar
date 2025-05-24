@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10 px-4 lg:px-0 bg-gray-50 rounded-lg shadow-md">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario de Información de Compra -->
        <div class="col-span-2 bg-white p-8 shadow-lg rounded-lg ml-8">
            <h2 class="text-5xl font-bold text-center mb-5 text-green-600">Datos personales</h2>
            <p class="text-center mb-5">Solicitamos únicamente la información esencial para la finalización de la compra.</p>
            <h3 class="text-xl font-bold my-6 text-gray-700">Información de compra</h3>  
            
            <!-- Mensaje de error -->
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <span id="error-text"></span>
            </div>
            
            <!-- Mensaje de éxito -->
            <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <span id="success-text"></span>
            </div>

            <form id="checkout-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           placeholder="Nombres" 
                           class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" 
                           required>
                    
                    <input type="text" 
                           id="apellido" 
                           name="apellido" 
                           placeholder="Apellidos" 
                           class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" 
                           required>
                    
                    <input type="text" 
                           id="empresa" 
                           name="empresa" 
                           placeholder="Nombre de empresa (opcional)" 
                           class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300">
                    
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="Correo Electrónico" 
                           class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" 
                           required>
                    
                    <input type="tel" 
                           id="telefono" 
                           name="telefono" 
                           placeholder="Teléfono" 
                           class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-300" 
                           required>
                </div>

                <h3 class="text-xl font-bold my-6 text-gray-700">Opciones de Delivery</h3>
                <div class="flex flex-col space-y-4 mb-6">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="radio" 
                               name="delivery" 
                               value="puesto" 
                               id="delivery-puesto"
                               class="mr-2 delivery-option focus:ring-green-500" 
                               required> 
                        Recoger en Puesto
                    </label>
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="radio" 
                               name="delivery" 
                               value="delivery" 
                               id="delivery-delivery"
                               class="mr-2 delivery-option focus:ring-green-500" 
                               required> 
                        Delivery
                    </label>
                </div>

                <div id="delivery-fields" class="space-y-4 mb-6 hidden">
                    <input type="text" 
                           id="direccion" 
                           name="direccion" 
                           placeholder="Nombre de la calle y número de casa" 
                           class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
                    
                    <input type="text" 
                           id="direccion_opcional" 
                           name="direccion_opcional" 
                           placeholder="Dpto., piso, unidad, bloque (opcional)" 
                           class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
                    
                    <select id="distrito" 
                            name="distrito" 
                            class="border border-gray-300 rounded-lg w-full px-4 py-3 focus:ring-2 focus:ring-green-300">
                        <option value="">Seleccione una zona</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" data-cost="{{ $zone->delivery_cost }}">
                                {{ $zone->name }} - S/{{ number_format($zone->delivery_cost, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <h3 class="text-xl font-bold my-6 text-gray-700">Opción de Pago</h3>
                <div class="flex flex-col space-y-4 mb-6">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="radio" 
                               name="pago" 
                               value="puesto" 
                               id="pago-puesto"
                               class="mr-2 focus:ring-green-500" 
                               required> 
                        Pagar en Puesto
                    </label>
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="radio" 
                               name="pago" 
                               value="sistema" 
                               id="pago-sistema"
                               class="mr-2 focus:ring-green-500" 
                               required> 
                        Pagar en el Sistema (MercadoPago)
                    </label>
                </div>

                <button type="submit" 
                        id="submit-btn"
                        class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span id="btn-text">Realizar pedido</span>
                    <span id="btn-loading" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="bg-white p-8 shadow-lg rounded-lg mx-6">
            <h3 class="text-2xl font-bold mb-6 text-center text-green-700">Resumen de tu compra</h3>
            
            <div id="carrito-items">
                @foreach ($carrito->items as $item)
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">{{ $item->product->nombre }}</p>
                        <p class="text-sm text-gray-600">Cantidad: {{ $item->cantidad }}</p>
                        <p class="text-sm text-gray-600">Precio unitario: S/{{ number_format($item->product->precio, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-700">Subtotal:</span>
                    <span id="subtotal" class="text-gray-800 font-semibold">S/{{ number_format($carrito->total(), 2) }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-700">Envío:</span>
                    <span id="envio" class="text-gray-800 font-semibold">S/0.00</span>
                </div>
                
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-300 font-bold text-xl">
                    <span class="text-gray-800">Total:</span>
                    <span id="total" class="text-green-600">S/{{ number_format($carrito->total(), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const deliveryFields = document.getElementById('delivery-fields');
    const deliveryOptions = document.querySelectorAll('input[name="delivery"]');
    const zoneSelect = document.getElementById('distrito');
    const direccionInput = document.getElementById('direccion');
    const envioSpan = document.getElementById('envio');
    const totalSpan = document.getElementById('total');
    const subtotalSpan = document.getElementById('subtotal');
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');

    // Variables de estado
    let subtotalValue = parseFloat('{{ $carrito->total() }}');
    let currentDeliveryCost = 0;

    // Funciones de utilidad
    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
        console.error('Error mostrado:', message);
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function showSuccess(message) {
        successText.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }

    function updateTotal() {
        const total = subtotalValue + currentDeliveryCost;
        totalSpan.textContent = `S/${total.toFixed(2)}`;
        envioSpan.textContent = `S/${currentDeliveryCost.toFixed(2)}`;
    }

    function setLoading(loading) {
        if (loading) {
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
        } else {
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    }

    function validateForm() {
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const delivery = document.querySelector('input[name="delivery"]:checked');
        const pago = document.querySelector('input[name="pago"]:checked');

        if (!nombre || !apellido || !email || !telefono || !delivery || !pago) {
            showError('Por favor, completa todos los campos obligatorios');
            return false;
        }

        if (delivery.value === 'delivery') {
            const direccion = document.getElementById('direccion').value.trim();
            const distrito = document.getElementById('distrito').value;

            if (!direccion || !distrito) {
                showError('Por favor, completa la dirección y selecciona una zona para el delivery');
                return false;
            }
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('Por favor, ingresa un email válido');
            return false;
        }

        return true;
    }

    function collectFormData() {
        const delivery = document.querySelector('input[name="delivery"]:checked');
        const pago = document.querySelector('input[name="pago"]:checked');
        
        const data = {
            nombre: document.getElementById('nombre').value.trim(),
            apellido: document.getElementById('apellido').value.trim(),
            empresa: document.getElementById('empresa').value.trim() || null,
            email: document.getElementById('email').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            delivery: delivery ? delivery.value : null,
            pago: pago ? pago.value : null,
            direccion: null,
            distrito: null
        };

        if (delivery && delivery.value === 'delivery') {
            data.direccion = document.getElementById('direccion').value.trim();
            data.distrito = document.getElementById('distrito').value;
        }

        return data;
    }

    // Event Listeners
    deliveryOptions.forEach(option => {
        option.addEventListener('change', function() {
            hideError();
            
            if (this.value === 'delivery') {
                deliveryFields.classList.remove('hidden');
                direccionInput.required = true;
                zoneSelect.required = true;
            } else {
                deliveryFields.classList.add('hidden');
                direccionInput.required = false;
                zoneSelect.required = false;
                zoneSelect.value = '';
                
                // Reset delivery cost
                currentDeliveryCost = 0;
                updateTotal();
            }
        });
    });

    zoneSelect.addEventListener('change', function() {
        hideError();
        
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            currentDeliveryCost = parseFloat(selectedOption.getAttribute('data-cost') || 0);
        } else {
            currentDeliveryCost = 0;
        }
        
        updateTotal();
    });

    // Form submission
    checkoutForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        console.log('=== INICIO ENVÍO FORMULARIO ===');
        
        hideError();
        
        if (!validateForm()) {
            return;
        }

        setLoading(true);

        try {
            const formData = collectFormData();
            console.log('Datos del formulario:', formData);

            const response = await fetch('{{ route("order.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(formData)
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            let responseData;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                responseData = await response.json();
            } else {
                const textResponse = await response.text();
                console.error('Response no JSON:', textResponse);
                throw new Error('La respuesta del servidor no es válida');
            }

            console.log('Response data:', responseData);

            if (!response.ok) {
                throw new Error(responseData.error || `Error ${response.status}: ${response.statusText}`);
            }

            if (responseData.success === false) {
                throw new Error(responseData.error || 'Error desconocido en el servidor');
            }

            // Procesar respuesta exitosa
            if (responseData.init_point) {
                console.log('Redirigiendo a MercadoPago:', responseData.init_point);
                showSuccess('Redirigiendo a MercadoPago...');
                setTimeout(() => {
                    window.location.href = responseData.init_point;
                }, 1000);
            } else if (responseData.redirect_url) {
                console.log('Redirigiendo a página de éxito:', responseData.redirect_url);
                showSuccess('Pedido creado exitosamente. Redirigiendo...');
                setTimeout(() => {
                    window.location.href = responseData.redirect_url;
                }, 1000);
            } else {
                throw new Error('No se recibió una URL de redirección válida');
            }

        } catch (error) {
            console.error('Error en la petición:', error);
            showError(error.message || 'Hubo un problema al procesar tu pedido. Por favor, inténtalo de nuevo.');
        } finally {
            setLoading(false);
        }
    });

    // Inicialización
    deliveryFields.classList.add('hidden');
    console.log('Checkout inicializado correctamente');
});
</script>
@endsection