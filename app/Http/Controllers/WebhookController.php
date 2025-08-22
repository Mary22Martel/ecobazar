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
     * Manejar webhook de MercadoPago
     */
    public function mercadoPagoWebhook(Request $request)
    {
        try {
            Log::info('=== WEBHOOK RECIBIDO ===');
            Log::info('Datos:', $request->all());
            
            $data = $request->all();
            
            // Verificar estructura básica
            if (!isset($data['type']) || !isset($data['data']['id'])) {
                Log::warning('Webhook sin estructura correcta');
                return response()->json(['status' => 'ok'], 200);
            }
            
            // Solo procesar pagos
            if ($data['type'] !== 'payment') {
                Log::info('Evento ignorado: ' . $data['type']);
                return response()->json(['status' => 'ok'], 200);
            }
            
            $paymentId = $data['data']['id'];
            Log::info("Procesando pago: {$paymentId}");
            
            // Obtener información del pago
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PaymentClient();
            $payment = $client->get($paymentId);
            
            Log::info('Pago obtenido:', [
                'id' => $payment->id,
                'status' => $payment->status,
                'external_reference' => $payment->external_reference
            ]);
            
            // Buscar orden
            if (!$payment->external_reference) {
                Log::warning('Sin external_reference');
                return response()->json(['status' => 'ok'], 200);
            }
            
            $orden = Order::find($payment->external_reference);
            if (!$orden) {
                Log::warning("Orden no encontrada: {$payment->external_reference}");
                return response()->json(['status' => 'ok'], 200);
            }
            
            // Procesar según estado
            if ($payment->status === 'approved' && $orden->estado !== 'pagado') {
                $orden->update([
                    'estado' => 'pagado',
                    'mercadopago_payment_id' => $payment->id,
                    'paid_at' => Carbon::now()
                ]);
                
                Log::info("✅ Orden {$orden->id} marcada como PAGADA automáticamente");
            }
            
            return response()->json(['status' => 'ok'], 200);
            
        } catch (Exception $e) {
            Log::error('Error en webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 200);
        }
    }
}