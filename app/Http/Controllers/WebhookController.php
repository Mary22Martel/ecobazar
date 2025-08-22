<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Carrito;

class MercadoPagoController extends Controller
{
    /**
     * ELIMINAR TODO EL CONTENIDO ACTUAL Y REEMPLAZAR POR ESTO:
     * 
     * Este controlador ya no es necesario porque:
     * 1. El procesamiento de pagos se hace en OrderController::procesarPagoMercadoPago()
     * 2. Los webhooks se manejan en WebhookController::mercadoPagoWebhook()
     * 3. Las páginas de éxito se manejan en OrderController::success()
     */

    /**
     * Método de respaldo para redirecciones de MercadoPago
     * Solo mantener si tienes rutas que apunten aquí
     */
    public function success(Request $request)
    {
        // Redirigir al método correcto en OrderController
        $paymentId = $request->get('payment_id') ?? $request->get('collection_id');
        $externalReference = $request->get('external_reference');
        
        if ($externalReference) {
            return redirect()->route('order.success', $externalReference);
        }
        
        // Si no hay referencia, redirigir a la tienda
        return redirect()->route('tienda')->with('error', 'No se pudo procesar el pago');
    }

    /**
     * Método de respaldo para pagos fallidos
     */
    public function failed(Request $request)
    {
        return redirect()->route('tienda')->with('error', 'El pago fue cancelado o rechazado');
    }

    /**
     * Método de respaldo para pagos pendientes
     */
    public function pending(Request $request)
    {
        $externalReference = $request->get('external_reference');
        
        if ($externalReference) {
            return redirect()->route('order.success', $externalReference);
        }
        
        return redirect()->route('tienda')->with('info', 'Tu pago está siendo procesado');
    }
}