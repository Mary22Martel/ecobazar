<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use Exception;
use Carbon\Carbon;

class WebhookController extends Controller
{
    /**
     * Manejar notificaciones de MercadoPago
     */
    public function mercadoPagoWebhook(Request $request)
    {
        try {
            Log::info('=== WEBHOOK MERCADOPAGO RECIBIDO ===');
            Log::info('Datos recibidos:', $request->all());
            
            $data = $request->all();
            
            // Verificar que tiene la estructura correcta
            if (!isset($data['type']) || !isset($data['data']['id'])) {
                Log::warning('Webhook con estructura incorrecta');
                return response()->json(['status' => 'ok'], 200);
            }
            
            // Solo procesar pagos
            if ($data['type'] !== 'payment') {
                Log::info('Evento ignorado: ' . $data['type']);
                return response()->json(['status' => 'ok'], 200);
            }
            
            $paymentId = $data['data']['id'];
            Log::info("Procesando pago ID: {$paymentId}");
            
            // Obtener información del pago desde MercadoPago
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();
            $payment = $client->get($paymentId);
            
            Log::info('Info del pago:', [
                'id' => $payment->id,
                'status' => $payment->status,
                'external_reference' => $payment->external_reference,
                'amount' => $payment->transaction_amount
            ]);
            
            // Buscar la orden por external_reference
            if (!$payment->external_reference) {
                Log::warning('Pago sin external_reference');
                return response()->json(['status' => 'ok'], 200);
            }
            
            $orden = Order::find($payment->external_reference);
            if (!$orden) {
                Log::warning("Orden no encontrada: {$payment->external_reference}");
                return response()->json(['status' => 'ok'], 200);
            }
            
            Log::info("Orden encontrada: {$orden->id}, estado actual: {$orden->estado}");
            
            // Procesar según el estado del pago
            if ($payment->status === 'approved') {
                $this->aprobarPago($orden, $payment);
            } elseif (in_array($payment->status, ['rejected', 'cancelled'])) {
                $this->rechazarPago($orden, $payment);
            } else {
                Log::info("Estado {$payment->status} - no se requiere acción");
            }
            
            return response()->json(['status' => 'ok'], 200);
            
        } catch (Exception $e) {
            Log::error('Error en webhook: ' . $e->getMessage());
            // Siempre devolver 200 para evitar reenvíos
            return response()->json(['status' => 'error'], 200);
        }
    }
    
    /**
     * Aprobar pago
     */
    private function aprobarPago($orden, $payment)
    {
        Log::info("=== APROBANDO PAGO - Orden {$orden->id} ===");
        
        // Solo actualizar si no está ya pagado
        if ($orden->estado !== 'pagado') {
            $orden->update([
                'estado' => 'pagado',
                'mercadopago_payment_id' => $payment->id,
                'paid_at' => Carbon::now()
            ]);
            
            Log::info("✅ Orden {$orden->id} marcada como PAGADA automáticamente");
        } else {
            Log::info("Orden {$orden->id} ya estaba pagada");
        }
    }
    
    /**
     * Rechazar pago - liberar stock
     */
    private function rechazarPago($orden, $payment)
    {
        Log::info("=== RECHAZANDO PAGO - Orden {$orden->id} ===");
        
        // Liberar stock si estaba reservado
        if ($orden->stock_reserved) {
            foreach ($orden->items as $item) {
                $item->product->increment('cantidad_disponible', $item->cantidad);
            }
        }
        
        $orden->update([
            'estado' => 'cancelado',
            'mercadopago_payment_id' => $payment->id,
            'stock_reserved' => false
        ]);
        
        Log::info("❌ Orden {$orden->id} cancelada por pago rechazado");
    }
}