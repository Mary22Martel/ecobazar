@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-4 sm:py-6 lg:py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
            <!-- Formulario de Información de Compra -->
            <div class="xl:col-span-2 order-2 xl:order-1">
                <div class="bg-white p-4 sm:p-6 lg:p-8 shadow-xl rounded-2xl">
                    <div class="text-center mb-6 lg:mb-8">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-600 mb-2">Datos personales</h2>
                        <p class="text-sm sm:text-base text-gray-600">Solicitamos únicamente la información esencial para finalizar tu compra</p>
                    </div>
                    
                    <!-- Mensaje de error -->
                    <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>
                    
                    <!-- Mensaje de éxito -->
                    <div id="success-message" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span id="success-text"></span>
                        </div>
                    </div>

                    <form id="checkout-form" class="space-y-6 lg:space-y-8">
                        @csrf
                        
                        <!-- Información Personal -->
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Información personal
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                                <div class="space-y-2">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombres *</label>
                                    <input type="text" 
                                           id="nombre" 
                                           name="nombre" 
                                           placeholder="Ingresa tus nombres" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                           required>
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="apellido" class="block text-sm font-medium text-gray-700">Apellidos *</label>
                                    <input type="text" 
                                           id="apellido" 
                                           name="apellido" 
                                           placeholder="Ingresa tus apellidos" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                           required>
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="empresa" class="block text-sm font-medium text-gray-700">Empresa (opcional)</label>
                                    <input type="text" 
                                           id="empresa" 
                                           name="empresa" 
                                           placeholder="Nombre de tu empresa" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico *</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           placeholder="ejemplo@correo.com" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                           required>
                                </div>
                                
                                <div class="space-y-2 sm:col-span-2 md:col-span-1">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono *</label>
                                    <input type="tel" 
                                        id="telefono" 
                                        name="telefono" 
                                        placeholder="987654321" 
                                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                        maxlength="9"
                                        oninput="validarTelefono(this)"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Script de validación del teléfono -->
                        <script>
                        function validarTelefono(input) {
                            input.value = input.value.replace(/[^0-9]/g, '');
                            
                            if (input.value.length === 9) {
                                input.classList.remove('border-gray-300');
                                input.classList.add('border-green-500');
                            } else {
                                input.classList.remove('border-green-500');
                                input.classList.add('border-gray-300');
                            }
                        }
                        </script>

                        <!-- Opciones de Delivery -->
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Opciones de entrega
                            </h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200">
                                    <input type="radio" 
                                           name="delivery" 
                                           value="puesto" 
                                           id="delivery-puesto"
                                           class="mr-3 delivery-option focus:ring-green-500 text-green-600" 
                                           required> 
                                    <div class="flex items-center">
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">Recoger en puesto</p>
                                            <p class="text-xs text-gray-600">Recoge tu pedido directamente en la feria agricola el día sábado</p>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200">
                                    <input type="radio" 
                                           name="delivery" 
                                           value="delivery" 
                                           id="delivery-delivery"
                                           class="mr-3 delivery-option focus:ring-green-500 text-green-600" 
                                           required> 
                                    <div class="flex items-center">
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">Delivery a domicilio</p>
                                            <p class="text-xs text-gray-600">Enviamos tu pedido directamente a tu dirección el día Sábado</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Campos de Delivery -->
                        <div id="delivery-fields" class="space-y-4 hidden">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Dirección de entrega</h4>
                            
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección *</label>
                                    <input type="text" 
                                           id="direccion" 
                                           name="direccion" 
                                           placeholder="Nombre de la calle y número de casa" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="direccion_opcional" class="block text-sm font-medium text-gray-700">Referencia (opcional)</label>
                                    <input type="text" 
                                           id="direccion_opcional" 
                                           name="direccion_opcional" 
                                           placeholder="Dpto., piso, unidad, bloque, referencia" 
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="distrito" class="block text-sm font-medium text-gray-700">Zona de entrega *</label>
                                    <select id="distrito" 
                                            name="distrito" 
                                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Seleccione una zona</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" data-cost="{{ $zone->delivery_cost }}">
                                                {{ $zone->name }} - S/{{ number_format($zone->delivery_cost, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Método de Pago -->
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Método de pago
                            </h3>
                            
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 lg:p-6">
                                <div class="flex items-center">
                                    <input type="radio" 
                                           name="pago" 
                                           value="sistema" 
                                           id="pago-sistema"
                                           class="mr-4 focus:ring-green-500 text-green-600" 
                                           checked
                                           required> 
                                    <div class="flex items-center">
                                        <div class="bg-white p-3 rounded-lg mr-4 shadow-sm">
                                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">Pagar con MercadoPago</p>
                                            <p class="text-xs text-gray-600">Tarjetas de crédito, débito, transferencias y más métodos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de envío -->
                        <button type="submit" 
                                id="submit-btn"
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-4 rounded-xl font-semibold text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none shadow-lg">
                            <span id="btn-text" class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Proceder al pago
                            </span>
                            <span id="btn-loading" class="hidden flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="order-1 xl:order-2">
                <div class="bg-white p-4 sm:p-6 lg:p-8 shadow-xl rounded-2xl sticky top-4">
                    <h3 class="text-xl sm:text-2xl font-bold mb-6 text-center text-green-700 flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                        Resumen de compra
                    </h3>
                    
                    <div id="carrito-items" class="space-y-4 mb-6 max-h-60 sm:max-h-80 overflow-y-auto">
                        @foreach ($carrito->items as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $item->product->nombre }}</p>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-1">
                                    <p class="text-sm text-gray-600">{{ $item->cantidad }} {{ $item->product->medida ? $item->product->medida->nombre : 'unidad' }}{{ $item->cantidad > 1 && $item->product->medida && $item->product->medida->nombre != 'Unidad' ? 's' : '' }}</p>
                                    <p class="text-sm text-gray-600">S/{{ number_format($item->product->precio, 2) }} c/u</p>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <p class="font-bold text-gray-800">S/{{ number_format($item->product->precio * $item->cantidad, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-3 mx-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span id="subtotal" class="font-semibold">S/{{ number_format($carrito->total(), 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-700">
                            <span>Envío:</span>
                            <span id="envio" class="font-semibold">S/0.00</span>
                        </div>
                        
                        <div class="flex justify-between pt-3 border-t border-gray-300 font-bold text-xl">
                            <span class="text-gray-800">Total:</span>
                            <span id="total" class="text-green-600">S/{{ number_format($carrito->total(), 2) }}</span>
                        </div>
                    </div>
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
        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

        if (!nombre || !apellido || !email || !telefono || !delivery) {
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

        if (telefono.length !== 9) {
            showError('El teléfono debe tener exactamente 9 dígitos');
            return false;
        }

        return true;
    }

    function collectFormData() {
        const delivery = document.querySelector('input[name="delivery"]:checked');
        
        const data = {
            nombre: document.getElementById('nombre').value.trim(),
            apellido: document.getElementById('apellido').value.trim(),
            empresa: document.getElementById('empresa').value.trim() || null,
            email: document.getElementById('email').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            delivery: delivery ? delivery.value : null,
            pago: 'sistema', // Siempre sistema
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