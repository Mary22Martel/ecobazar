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
    // Método para crear la preferencia de pago
    public function createPaymentPreference(Request $request)
    {
        Log::info('Creando preferencia de pago');
        $this->authenticate();
        Log::info('Autenticado con éxito');

        // Paso 1: Obtener la información del producto desde la solicitud JSON
        $product = $request->input('product');

        if (empty($product) || !is_array($product)) {
            return response()->json(['error' => 'Los datos del producto son requeridos.'], 400);
        }

        // Paso 2: Información del comprador 
        $payer = [
            "name" => $request->input('name', 'John'),
            "surname" => $request->input('surname', 'Doe'),
            "email" => $request->input('email', 'user@example.com'),
        ];

        // Paso 3: Crear la solicitud de preferencia 
        $requestData = $this->createPreferenceRequest($product, $payer);

        // Paso 4: Crear la preferencia con el cliente de preferencia 
        $client = new PreferenceClient();

        try {
            $preference = $client->create($requestData);

            return response()->json([
                'id' => $preference->id,
                'init_point' => $preference->init_point,
            ]);
        } catch (MPApiException $error) {
            return response()->json([
                'error' => $error->getApiResponse()->getContent(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para autenticarse con Mercado Pago
    protected function authenticate()
    {
        $mpAccessToken = config('services.mercadopago.token');
        if (!$mpAccessToken) {
            throw new Exception("El token de acceso de Mercado Pago no está configurado.");
        }
        MercadoPagoConfig::setAccessToken($mpAccessToken);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    // Método para crear la estructura de preferencia
    protected function createPreferenceRequest($items, $payer): array
    {
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        $backUrls = [
            'success' => route('mercadopago.success'),
            'failure' => route('mercadopago.failed')
        ];

        $request = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "TIENDA ONLINE",
            "external_reference" => "1234567890",
            "expires" => false,
            "auto_return" => 'approved',
        ];
        return $request;
    }

    // Método para manejar el éxito del pago
    public function success(Request $request)
    {
        return view('order.success', ['payment_info' => $request->all()]);
    }

    // Método para manejar el fallo del pago
    public function failed(Request $request)
    {
        return view('payment.failed', ['payment_info' => $request->all()]);
    }

    // Método para manejar pagos pendientes
    public function pending(Request $request)
    {
        return view('payment.pending', ['payment_info' => $request->all()]);
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        $externalReference = $data['data']['external_reference'];
    
        $orden = Order::where('id', $externalReference)->first();
        if ($orden) {
            $orden->estado = 'pagado';
            $orden->save();
    
            // Vaciar el carrito del usuario
            $carrito = Carrito::where('user_id', $orden->user_id)->first();
            if ($carrito) {
                $carrito->items()->delete();
            }
        }
        return response()->json(['status' => 'success']);
    }

    
    


}
