@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center min-h-screen bg-gray-100 px-4 py-10">
    
    <!-- Mensaje estilo MercadoPago - visible cuando viene del pago o siempre -->
    <div id="mp-message" class="bg-white border border-gray-200 rounded-lg p-8 mb-6 max-w-lg w-full text-center shadow-lg"
         @if(!isset($desdeMP)) style="display: block;" @endif>
        <!-- Icono de éxito -->
        <div class="flex justify-center mb-6">
            <div class="bg-green-100 rounded-full p-4 w-20 h-20 flex items-center justify-center">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <!-- Mensaje principal -->
        <h1 class="text-2xl font-bold mb-4 text-gray-800">¡Listo! Tu pago ya se acreditó</h1>
        <p class="text-gray-600 mb-6">Operación #{{ $orden->id ?? '000000' }}</p>
        
        <!-- Información del pago -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-red-500 rounded mr-3 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">MC</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Pagaste S/{{ number_format($orden->total ?? 0, 2) }}</p>
                    <p class="text-sm text-gray-600">Mastercard •••• {{ substr(str_pad($orden->id ?? 0, 4, '0', STR_PAD_LEFT), -4) }} Mastercard Débito</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">En el resumen de tu tarjeta verás el cargo a nombre de Mercadopago*simulacion</p>
        </div>
        
        <!-- Botón Ver comprobante -->
        <a href="{{ route('order.voucher', $orden->id) }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-200 inline-block mb-4">
            Ver comprobante
        </a>
        
        <!-- Mensaje de redirección -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span id="redirect-text">En <span id="countdown">5</span> segundos te llevaremos a los detalles de tu pedido.</span>
            </p>
        </div>
    </div>

    <!-- Success Message (oculto inicialmente) -->
    <div id="success-content" class="bg-white p-8 shadow-lg rounded-lg text-center max-w-lg w-full hidden">
        <div class="flex justify-center mb-6">
            <div class="bg-green-100 rounded-full p-4">
                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <h1 class="text-4xl font-bold mb-6 text-green-600">¡Orden Exitosa!</h1>
        <p class="text-lg mb-3 text-gray-700">Gracias por tu compra. Hemos recibido tu orden correctamente.</p>
        
        <!-- Estado de la orden -->
        <div class="mt-4 p-3 rounded-lg {{ $orden->estado === 'pagado' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
            <p class="font-semibold {{ $orden->estado === 'pagado' ? 'text-green-700' : 'text-yellow-700' }}">
                Estado: 
                @if($orden->estado === 'pagado')
                    ✅ Pago Confirmado
                @elseif($orden->estado === 'pendiente')
                    ⏳ Pago Pendiente
                @else
                    {{ ucfirst($orden->estado) }}
                @endif
            </p>
            @if($orden->estado === 'pagado')
                <p class="text-sm text-green-600 mt-1">Tu pago ha sido procesado exitosamente</p>
            @endif
        </div>

        <!-- Información de la orden -->
        <div class="mt-6 text-left bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600"><strong>Orden ID:</strong> #{{ $orden->id }}</p>
            <p class="text-sm text-gray-600"><strong>Fecha:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}</p>
            <p class="text-sm text-gray-600"><strong>Tipo:</strong> {{ $orden->delivery === 'puesto' ? 'Recoger en Puesto' : 'Delivery' }}</p>
            @if($orden->delivery === 'delivery')
                <p class="text-sm text-gray-600"><strong>Dirección:</strong> {{ $orden->direccion }}, {{ $orden->distrito }}</p>
            @endif
        </div>
    </div>

    <!-- Order Details (oculto inicialmente) -->
    <div id="order-details" class="bg-white p-6 shadow-lg rounded-lg mt-8 w-full max-w-md hidden">
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Detalles del Pedido</h3>
        
        <div class="space-y-3">
            @foreach ($orden->items as $item)
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $item->product->nombre }}</p>
                    <p class="text-sm text-gray-500">Cantidad: {{ $item->cantidad }}</p>
                    <p class="text-sm text-gray-500">Precio unitario: S/{{ number_format($item->precio, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-800">S/{{ number_format($item->precio * $item->cantidad, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Totales -->
    <div class="mt-6 space-y-2">
    <div class="flex justify-between text-lg">
        <span class="text-gray-600">Subtotal de Productos:</span>
        <span class="font-semibold">S/{{ number_format($subtotal, 2) }}</span>
    </div>
    @if($costoEnvio > 0)
    <div class="flex justify-between text-lg">
        <span class="text-gray-600">Costo de Envío:</span>
        <span class="font-semibold">S/{{ number_format($costoEnvio, 2) }}</span>
    </div>
    @endif
    
    <!-- Subtotal antes de comisión -->
    <div class="flex justify-between text-lg border-b border-gray-200 pb-2">
        <span class="text-gray-600">Subtotal:</span>
        <span class="font-semibold">S/{{ number_format($montoNeto, 2) }}</span>
    </div>
    
    <!-- NUEVA LÍNEA: Comisión MercadoPago -->
    <div class="flex justify-between text-lg">
        <span class="text-gray-600">Comisión Pago Seguro:</span>
        <span class="font-semibold">S/{{ number_format($comisionCobrada, 2) }}</span>
    </div>
    
    <!-- Total final con comisión -->
    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200 font-bold text-xl">
        <span class="text-gray-800">TOTAL PAGADO:</span>
        <span class="text-green-600">S/{{ number_format($totalConComision, 2) }}</span>
    </div>
    </div>
    </div>

    <!-- Buttons (oculto inicialmente) -->
    <div id="action-buttons" class="flex flex-col sm:flex-row sm:space-x-4 mt-6 w-full max-w-md space-y-4 sm:space-y-0 hidden">
        <!-- Botón de voucher -->
        <a href="{{ route('order.voucher', $orden->id) }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition duration-200 w-full flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Descargar Voucher
        </a>
        
        <!-- Botón volver a tienda -->
        <a href="{{ route('tienda') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition duration-200 w-full flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Volver a la Tienda
        </a>
    </div>

    <!-- Manual refresh button (solo si la orden sigue pendiente) -->
    @if($orden->estado === 'pendiente')
    <div class="mt-6 w-full max-w-md hidden" id="refresh-section">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <p class="text-yellow-700 mb-3">¿Tu pago no se refleja? Puedes verificar manualmente:</p>
            <button onclick="window.location.reload()" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                🔄 Verificar Estado del Pago
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Iniciando detección de origen...');
    
    // Detectar si viene desde MercadoPago
    const urlParams = new URLSearchParams(window.location.search);
    const vieneDeMercadoPago = urlParams.get('mp') === '1' || 
                               urlParams.get('collection_status') || 
                               urlParams.get('payment_id') ||
                               urlParams.get('status') === 'approved';
    
    // Elementos del DOM
    const mpMessage = document.getElementById('mp-message');
    const successContent = document.getElementById('success-content');
    const orderDetails = document.getElementById('order-details');
    const actionButtons = document.getElementById('action-buttons');
    const refreshSection = document.getElementById('refresh-section');
    const countdownElement = document.getElementById('countdown');
    
    if (vieneDeMercadoPago) {
        console.log('🎉 Usuario viene desde MercadoPago - iniciando redirección automática');
        
        // Mostrar mensaje estilo MercadoPago inmediatamente
        if (mpMessage) {
            mpMessage.style.display = 'block';
        }
        
        // Ocultar el resto hasta que termine el contador
        if (successContent) successContent.style.display = 'none';
        if (orderDetails) orderDetails.style.display = 'none';
        if (actionButtons) actionButtons.style.display = 'none';
        
        // Iniciar contador de 5 segundos
        let countdown = 5;
        
        const timer = setInterval(function() {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                
                // Ocultar mensaje de MercadoPago y mostrar contenido completo
                if (mpMessage) mpMessage.style.display = 'none';
                if (successContent) {
                    successContent.classList.remove('hidden');
                    successContent.style.display = 'block';
                }
                if (orderDetails) {
                    orderDetails.classList.remove('hidden');
                    orderDetails.style.display = 'block';
                }
                if (actionButtons) {
                    actionButtons.classList.remove('hidden');
                    actionButtons.style.display = 'flex';
                }
                if (refreshSection) {
                    refreshSection.classList.remove('hidden');
                    refreshSection.style.display = 'block';
                }
                
                console.log('✅ Redirección completada - mostrando vista completa');
            }
        }, 1000);
        
    } else {
        console.log('👤 Acceso directo - mostrando vista normal inmediatamente');
        
        // Si NO viene desde MercadoPago, mostrar todo inmediatamente
        if (mpMessage) mpMessage.style.display = 'none';
        if (successContent) {
            successContent.classList.remove('hidden');
            successContent.style.display = 'block';
        }
        if (orderDetails) {
            orderDetails.classList.remove('hidden'); 
            orderDetails.style.display = 'block';
        }
        if (actionButtons) {
            actionButtons.classList.remove('hidden');
            actionButtons.style.display = 'flex';
        }
        if (refreshSection) {
            refreshSection.classList.remove('hidden');
            refreshSection.style.display = 'block';
        }
    }

    // Actualizar carrito después de orden exitosa
    if (window.location.href.includes('/orden-exito/')) {
        fetch('{{ route("carrito.getDetails") }}')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                const cartTotalItems = document.getElementById('cart-total-items');
                const cartTotalPrice = document.getElementById('cart-total-price');
                const cartItemsList = document.getElementById('cart-items-list');
                const cartPopupTotal = document.getElementById('cart-popup-total-price');
                
                if (cartTotalItems) cartTotalItems.innerText = data.totalItems || 0;
                if (cartTotalPrice) cartTotalPrice.innerText = (data.totalPrice || 0).toFixed(2);
                if (cartItemsList) cartItemsList.innerHTML = '';
                if (cartPopupTotal) cartPopupTotal.innerText = '0.00';
            })
            .catch(function(error) {
                console.error('Error al actualizar el carrito:', error);
            });
    }
});
</script>
@endsection