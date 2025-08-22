<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Carrito;
use App\Models\Zone;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Carbon\Carbon; 

class OrderController extends Controller
{
      // Constantes para MercadoPago
    const MERCADOPAGO_COMMISSION_RATE = 0.047082; // 4.7082%
    const MERCADOPAGO_FIXED_FEE = 1.18; // S/1.18
    const SECURITY_MARGIN = 0.10; // S/0.10
     /**
     * Calcular el monto que debe pagar el cliente
     */
    private function calcularMontoConComision($montoNeto)
    {
        $montoConComision = (($montoNeto + self::MERCADOPAGO_FIXED_FEE) / (1 - self::MERCADOPAGO_COMMISSION_RATE)) + self::SECURITY_MARGIN;
        return round($montoConComision, 2);
    }

    /**
     * Calcular la comisión que se llevará MercadoPago
     */
   private function calcularComisionMercadoPago($montoAPagar)
    {
        return round(($montoAPagar * self::MERCADOPAGO_COMMISSION_RATE) + self::MERCADOPAGO_FIXED_FEE, 2);
    }

    public function store(Request $request)
    {
        try {
            Log::info('=== INICIO CREACIÓN DE ORDEN ===');
            Log::info('Usuario ID: ' . Auth::id());
            
            // Validar campos básicos
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'empresa' => 'nullable|string|max:255',
                'email' => 'required|email',
                'telefono' => 'required|string',
                'delivery' => 'required|in:puesto,delivery',
                'direccion' => 'required_if:delivery,delivery|nullable|string',
                'distrito' => 'required_if:delivery,delivery|nullable|exists:zones,id',
                'pago' => 'required|in:sistema'
            ]);

            Log::info('Datos validados:', $validatedData);

            // Obtener el carrito del usuario
            $carrito = Carrito::with(['items.product.user'])
                            ->where('user_id', Auth::id())
                            ->first();

            if (!$carrito || $carrito->items->isEmpty()) {
                Log::warning('Carrito vacío para usuario: ' . Auth::id());
                return response()->json([
                    'success' => false,
                    'error' => 'Tu carrito está vacío'
                ], 400);
            }

            Log::info('Carrito encontrado con ' . $carrito->items->count() . ' items');

            // TEMPORAL: Forzar mercado 1 hasta que se implemente la selección de mercados
            $mercadoActual = 1;
            Log::info('Mercado forzado a: ' . $mercadoActual);

            // Calcular totales - Subtotal de productos sin comisiones
            $subtotalProductos = 0;
            foreach ($carrito->items as $item) {
                $subtotalProductos += ($item->product->precio * $item->cantidad);
            }

            $costoEnvio = 0;
            $zonaInfo = null;
            $repartidorId = null;

            // Procesar información de delivery
            if ($validatedData['delivery'] === 'delivery') {
                $zona = Zone::find($validatedData['distrito']);
                if (!$zona) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Zona de delivery no válida'
                    ], 400);
                }

                $costoEnvio = $zona->delivery_cost;
                $zonaInfo = $zona->name;

                // Buscar repartidor asignado para esta zona HOY
                $repartidor = User::whereHas('zones', function($query) use ($zona) {
                    $query->where('zones.id', $zona->id)
                        ->where('fecha_asignacion', now()->toDateString())
                        ->where('activa', true);
                })->where('role', 'repartidor')
                ->where('email', '!=', 'sistema.repartidor@puntoVerde.com')
                ->first();

                if ($repartidor) {
                    $repartidorId = $repartidor->id;
                    Log::info('Repartidor específico asignado para zona ' . $zonaInfo . ': ' . $repartidor->name . ' (ID: ' . $repartidor->id . ')');
                } else {
                    $repartidorId = Order::getRepartidorSistemaId();
                    Log::info('No hay repartidor asignado para zona ' . $zonaInfo . ', usando repartidor del sistema (ID: ' . $repartidorId . ')');
                }
            } else {
                $repartidorId = Order::getRepartidorSistemaId();
                Log::info('Pedido pickup - usando repartidor del sistema (ID: ' . $repartidorId . ')');
            }

            // Validación final del repartidor
            if (!$repartidorId) {
                $repartidorId = Order::getRepartidorSistemaId();
                Log::warning('No se pudo determinar repartidor, usando sistema como fallback (ID: ' . $repartidorId . ')');
            }

            // CALCULAR MONTOS CON COMISIÓN MERCADOPAGO
            $montoNeto = $subtotalProductos + $costoEnvio; // Lo que debe llegar íntegro
            $montoConComision = $this->calcularMontoConComision($montoNeto); // Lo que paga el cliente
            $comisionMP = $this->calcularComisionMercadoPago($montoConComision); // Lo que se lleva MP

            Log::info("=== CÁLCULO DE TOTALES CON MERCADOPAGO ===");
            Log::info("Subtotal productos: S/{$subtotalProductos}");
            Log::info("Costo envío: S/{$costoEnvio}");
            Log::info("Monto neto (debe llegar íntegro): S/{$montoNeto}");
            Log::info("Comisión MercadoPago: S/{$comisionMP}");
            Log::info("Total que paga cliente: S/{$montoConComision}");
            Log::info("Repartidor final asignado: {$repartidorId}");

            // INICIAR TRANSACCIÓN PARA ASEGURAR CONSISTENCIA
            DB::beginTransaction();

            try {
                // Crear la orden con todos los montos calculados
                $orden = new Order();
                $orden->user_id = Auth::id();
                $orden->nombre = $validatedData['nombre'];
                $orden->apellido = $validatedData['apellido'];
                $orden->empresa = $validatedData['empresa'];
                $orden->email = $validatedData['email'];
                $orden->telefono = $validatedData['telefono'];
                $orden->delivery = $validatedData['delivery'];
                $orden->direccion = $validatedData['direccion'] ?? null;
                $orden->distrito = $zonaInfo;
                $orden->pago = 'sistema';
                
                // GUARDAR TODOS LOS MONTOS DETALLADOS
                $orden->subtotal_productos = $subtotalProductos;   // Productos sin comisión
                $orden->costo_envio = $costoEnvio;                // Costo del delivery
                $orden->monto_neto = $montoNeto;                  // Total neto (productos + envío)
                $orden->comision_mercadopago = $comisionMP;       // Comisión que se lleva MP
                $orden->total = $montoConComision;                // Total que paga el cliente
                
                $orden->estado = 'pendiente';
                $orden->repartidor_id = $repartidorId;
                $orden->expires_at = Carbon::now('America/Lima')->addMinutes(20);
                $orden->stock_reserved = true;
                $orden->save();

                Log::info('Orden creada con ID: ' . $orden->id . ' - Repartidor asignado: ' . $repartidorId);

                // Crear los items de la orden y actualizar inventario
                foreach ($carrito->items as $item) {
                    // Verificar stock disponible
                    if ($item->product->cantidad_disponible < $item->cantidad) {
                        DB::rollback();
                        return response()->json([
                            'success' => false,
                            'error' => "Stock insuficiente para: {$item->product->nombre}"
                        ], 400);
                    }

                    // Crear item de orden
                    $orden->items()->create([
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio' => $item->product->precio // Precio original del producto
                    ]);

                    // Actualizar stock
                    $item->product->decrement('cantidad_disponible', $item->cantidad);
                    Log::info("Stock actualizado para producto {$item->product->nombre}");
                }

                // Limpiar carrito
                $carrito->items()->delete();
                $carrito->delete();
                Log::info('Carrito limpiado');

                // Confirmar transacción hasta aquí
                DB::commit();
                Log::info('Transacción confirmada - Orden y stock actualizados');

                // Procesar pago con MercadoPago usando los montos calculados
                return $this->procesarPagoMercadoPago($orden, $subtotalProductos, $costoEnvio);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error en transacción de orden: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'error' => 'Datos inválidos: ' . implode(', ', Arr::flatten($e->errors()))
            ], 422);
        } catch (Exception $e) {
            Log::error('Error general en store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Transferir pedidos del sistema a repartidores reales
     * Este método puede ser llamado por el AdminRepartidorController
     */
    public function transferirPedidosDelSistema($zonaId, $nuevoRepartidorId)
    {
        try {
            $repartidorSistemaId = Order::getRepartidorSistemaId();
            $zona = Zone::findOrFail($zonaId);
            
            $pedidosTransferidos = Order::where('repartidor_id', $repartidorSistemaId)
                                    ->where('distrito', $zona->name)
                                    ->whereIn('estado', ['armado', 'en_entrega'])
                                    ->update(['repartidor_id' => $nuevoRepartidorId]);
            
            Log::info("Transferidos {$pedidosTransferidos} pedidos de zona {$zona->name} al repartidor {$nuevoRepartidorId}");
            
            return $pedidosTransferidos;
            
        } catch (\Exception $e) {
            Log::error('Error transfiriendo pedidos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Devolver pedidos de repartidor al sistema
     */
    public function devolverPedidosAlSistema($zonaId, $repartidorId)
    {
        try {
            $repartidorSistemaId = Order::getRepartidorSistemaId();
            $zona = Zone::findOrFail($zonaId);
            
            $pedidosDevueltos = Order::where('repartidor_id', $repartidorId)
                                    ->where('distrito', $zona->name)
                                    ->whereIn('estado', ['armado', 'en_entrega'])
                                    ->update(['repartidor_id' => $repartidorSistemaId]);
            
            Log::info("Devueltos {$pedidosDevueltos} pedidos de zona {$zona->name} al sistema");
            
            return $pedidosDevueltos;
            
        } catch (\Exception $e) {
            Log::error('Error devolviendo pedidos al sistema: ' . $e->getMessage());
            throw $e;
        }
    }
    // En tu OrderController.php, agrega este método __construct:

    public function __construct()
    {
        // Verificar y expirar pedidos automáticamente en cada carga
        $this->checkExpiredOrders();
    }

    /**
     * Verificar y expirar pedidos vencidos automáticamente
     */
    private function checkExpiredOrders()
    {
        try {
            // REDUCIR el cache a 30 segundos para ser más responsivo
            $lastCheck = cache('last_expire_check');
            if ($lastCheck && now()->diffInSeconds($lastCheck) < 30) {
                return; 
            }
            
            // Usar la misma zona horaria
            $expiredOrders = Order::where('estado', 'pendiente')
                ->where('expires_at', '<=', Carbon::now('America/Lima'))
                ->where('stock_reserved', true)
                ->get();

            foreach ($expiredOrders as $order) {
                // Liberar stock
                foreach ($order->items as $item) {
                    $item->product->increment('cantidad_disponible', $item->cantidad);
                }
                
                // Marcar como expirado
                $order->update([
                    'estado' => 'expirado',
                    'stock_reserved' => false
                ]);
                
                Log::info("Pedido #{$order->id} expirado automáticamente");
            }
            
            // Cache por solo 30 segundos
            cache(['last_expire_check' => now()], 30);
            
        } catch (\Exception $e) {
            Log::error('Error verificando pedidos expirados: ' . $e->getMessage());
        }
    }

    private function procesarPagoMercadoPago($orden, $subtotalProductos, $costoEnvio)
    {
        try {
            Log::info('=== INICIANDO MERCADO PAGO ===');
            // Verificar configuración
$token = config('services.mercadopago.token');
Log::info('Verificando token MercadoPago:', [
    'token_exists' => !empty($token),
    'token_starts_with_test' => strpos($token, 'TEST-') === 0,
    'token_length' => strlen($token)
]);

if (empty($token)) {
    Log::error('Token de MercadoPago no configurado correctamente');
    return response()->json([
        'success' => false,
        'error' => 'Configuración de MercadoPago incorrecta'
    ], 500);
}
            
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $client = new PreferenceClient();
            
            // USAR EL TOTAL QUE YA INCLUYE LA COMISIÓN
            $montoAPagar = $orden->total;

            // Item para MercadoPago
            $items = [];
            $items[] = [
                "title" => "Pedido #" . $orden->id . " - Punto Verde",
                "description" => "Productos organicos frescos", // Descripción simple
                "quantity" => 1,
                "currency_id" => "PEN",
                "unit_price" => floatval($montoAPagar)
            ];

            // ✅ TÍTULO CORREGIDO - Una sola lógica, completa
            
            $items[0]["title"] = "Pedido #" . $orden->id . " - Punto Verde";

            // Configuración de preferencia
            $preferenceData = [
                "items" => $items,
                "back_urls" => [
                    "success" => url("/orden-exito/{$orden->id}?mp=1"),
                    "failure" => url("/order/failed"),
                    "pending" => url("/orden-exito/{$orden->id}?mp=1")
                ],
                "external_reference" => strval($orden->id),
                "statement_descriptor" => "Punto Verde",
                "auto_return" => "approved"
            ];

            // Webhook solo en producción
            if (config('app.env') === 'production') {
                $preferenceData["notification_url"] = url("/mercadopago/webhook");
                Log::info('Webhook configurado para producción: ' . url("/mercadopago/webhook"));
            } else {
                Log::info('Webhook omitido en desarrollo local');
            }

            Log::info('Datos para MercadoPago:', $preferenceData);

            $preference = $client->create($preferenceData);
            
            Log::info('Preferencia creada exitosamente');
            Log::info('Init point: ' . $preference->init_point);

            return response()->json([
                'success' => true,
                'init_point' => $preference->init_point
            ]);

        } catch (MPApiException $e) {
            Log::error('Error MercadoPago API: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago con MercadoPago. Verifica tu configuración.'
            ], 500);
        } catch (MPApiException $e) {
    Log::error('Error MercadoPago API detallado:', [
        'message' => $e->getMessage(),
        'status_code' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 'Unknown',
        'error_details' => $e->__toString()
    ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago'
            ], 500);
        }
    }

    public function success($orderId)
    {
        try {
            Log::info("=== ACCESO A PÁGINA DE ÉXITO ===");
            Log::info("Orden ID: {$orderId}");
            
            $orden = Order::with(['items.product', 'user'])->findOrFail($orderId);
            
            // Determinar si viene desde MercadoPago
            $desdeMP = request()->has('payment_id') || 
                    request()->has('collection_id') || 
                    request()->has('collection_status') || 
                    request()->has('from') ||
                    str_contains(request()->header('referer', ''), 'mercadopago') ||
                    str_contains(request()->header('referer', ''), 'mercadolibre');
            
            Log::info("Detectado desde MercadoPago: " . ($desdeMP ? 'SI' : 'NO'));
            
            // Verificar y actualizar pago si es necesario
            if ($orden->estado === 'pendiente') {
                Log::info("Orden pendiente, verificando pago con MercadoPago...");
                $pagoVerificado = $this->verificarYActualizarPago($orden);
                
                if (!$pagoVerificado && $orden->estado === 'pendiente') {
                    Log::info("Fallback: Marcando orden como pagada automáticamente");
                    $orden->estado = 'pagado';
                    $orden->paid_at = now();
                    $orden->save();
                    Log::info("Orden {$orden->id} actualizada a estado pagado");
                }
            }

            // ✅ USAR LOS CAMPOS CON FALLBACK ROBUSTO
            $subtotalProductos = $orden->subtotal_productos;
            $costoEnvio = $orden->costo_envio ?? 0;
            
            // Si los campos están vacíos (órdenes antiguas), calcular manualmente
            if (is_null($subtotalProductos) || $subtotalProductos == 0) {
                $subtotalProductos = $orden->items->sum(function($item) {
                    return $item->precio * $item->cantidad;
                });
                
                Log::info("Calculando subtotal manualmente: S/{$subtotalProductos}");
            }
            
            if (is_null($orden->costo_envio) && $orden->delivery === 'delivery' && $orden->distrito) {
                $zona = Zone::where('name', $orden->distrito)->first();
                if ($zona) {
                    $costoEnvio = $zona->delivery_cost;
                    Log::info("Calculando envío manualmente: S/{$costoEnvio}");
                }
            }

            return view('order.success', compact('orden', 'subtotalProductos', 'costoEnvio', 'desdeMP'));
            
        } catch (Exception $e) {
            Log::error('Error en success: ' . $e->getMessage());
            return redirect()->route('tienda')->with('error', 'Orden no encontrada');
        }
    }

    /**
     * Verificar el estado del pago con MercadoPago y actualizar la orden
     */
   private function verificarYActualizarPago($orden)
    {
        try {
            // Para desarrollo local - marcar automáticamente como pagado
            if (config('app.env') === 'local' && $orden->estado === 'pendiente') {
                $orden->estado = 'pagado';
                $orden->paid_at = now();
                $orden->save();
                Log::info("Orden {$orden->id} marcada como pagada (desarrollo local)");
                return true;
            }

            $paymentId = request()->get('payment_id') ?? request()->get('collection_id');
            
            if (!$paymentId) {
                Log::warning("No se encontró payment_id para verificar la orden {$orden->id}");
                return false; // Indica que necesita fallback
            }

            Log::info("Verificando pago con ID: {$paymentId}");

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);
            
            Log::info("Estado del pago: {$payment->status}");
            Log::info("External reference: {$payment->external_reference}");
            
            if ($payment->external_reference == $orden->id) {
                switch ($payment->status) {
                    case 'approved':
                        $orden->estado = 'pagado';
                        $orden->mercadopago_payment_id = $payment->id;
                        $orden->paid_at = now();
                        $orden->save();
                        Log::info("Orden {$orden->id} marcada como pagada automáticamente");
                        return true;
                        
                    case 'rejected':
                    case 'cancelled':
                        $orden->estado = 'cancelado';
                        $orden->save();
                        Log::info("Orden {$orden->id} marcada como cancelada");
                        return true;
                        
                    default:
                        Log::info("Pago aún en proceso, estado: {$payment->status}");
                        return false;
                }
            } else {
                Log::warning("El external_reference del pago ({$payment->external_reference}) no coincide con la orden ({$orden->id})");
                return false;
            }
            
        } catch (Exception $e) {
            Log::error("Error verificando pago para orden {$orden->id}: " . $e->getMessage());
            return false; // Indica que necesita fallback
        }
    }

    public function failed()
    {
        return view('order.failed');
    }

    public function downloadVoucher($orderId)
    {
        try {
            $orden = Order::with(['items.product', 'user'])->findOrFail($orderId);
            
            if (Auth::id() !== $orden->user_id && Auth::user()->role !== 'admin') {
                abort(403, 'No tienes permisos para ver este voucher');
            }

            // ✅ USAR CAMPOS CON FALLBACK ROBUSTO
            $subtotalProductos = $orden->subtotal_productos;
            $costoEnvio = $orden->costo_envio ?? 0;
            
            // Fallback para órdenes antiguas
            if (is_null($subtotalProductos) || $subtotalProductos == 0) {
                $subtotalProductos = $orden->items->sum(function($item) {
                    return $item->precio * $item->cantidad;
                });
            }
            
            if (is_null($orden->costo_envio) && $orden->delivery === 'delivery' && $orden->distrito) {
                $zona = \App\Models\Zone::where('name', $orden->distrito)->first();
                if ($zona) {
                    $costoEnvio = $zona->delivery_cost;
                }
            }

            $total = $orden->total; // Total que pagó el cliente (con comisión)

            $pdf = PDF::loadView('order.voucher', compact('orden', 'subtotalProductos', 'costoEnvio', 'total'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions([
                        'dpi' => 150,
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                    ]);
            
            $filename = "voucher_orden_" . str_pad($orderId, 6, '0', STR_PAD_LEFT) . ".pdf";
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error generando voucher: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el voucher.');
        }
    }

    // Métodos para agricultores
   public function pedidosPendientes()
    {
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No autorizado');
        }

        // Solo mostrar pedidos que necesitan preparación
        $pedidos = Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->whereIn('estado', ['pendiente', 'pagado']) // Solo estos necesitan preparación
        ->with([
            'items.product.categoria',
            'items.product.medida', 
            'items.product.user',
            'user'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('agricultor.pedidos_pendientes', compact('pedidos'));
    }

    public function detallePedido($id)
    {
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No autorizado');
        }

        $pedido = Order::with(['items.product', 'user'])->findOrFail($id);
        
        $productosAgricultor = $pedido->items->filter(function($item) {
            return $item->product->user_id == Auth::id();
        });

        if ($productosAgricultor->isEmpty()) {
            abort(403, 'No autorizado para ver este pedido');
        }

        return view('agricultor.pedido_detalle', compact('pedido', 'productosAgricultor'));
    }

   public function pedidosListos()
    {
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No autorizado');
        }

        // Mostrar pedidos que ya están listos (armados/entregados)
        $pedidos = Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->whereIn('estado', ['listo', 'armado', 'entregado']) // Ya listos = generan pago
        ->with([
            'items.product.categoria',
            'items.product.medida', 
            'items.product.user',
            'user'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('agricultor.pedidos_listos', compact('pedidos'));
    }

    public function pedidosExpirados()
    {
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No autorizado');
        }

        // Mostrar pedidos expirados del agricultor
        $pedidos = Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->where('estado', 'expirado')
        ->with([
            'items.product.categoria',
            'items.product.medida', 
            'items.product.user',
            'user'
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('agricultor.pedidos_expirados', compact('pedidos'));
    }

    // Métodos para admin
    public function todosLosPedidos()
    {
        $pedidos = Order::with(['items.product', 'user'])
                        ->where('estado', '!=', 'expirado')
                       ->orderBy('created_at', 'desc')
                       ->get();
                       
        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function detallePedidoAdmin($id)
    {
        $pedido = Order::with(['items.product.user', 'user'])->findOrFail($id);
        return view('admin.pedidos.detalle', compact('pedido'));
    }

    public function actualizarEstado(Request $request, $id)
    {
        $pedido = Order::findOrFail($id);
        $pedido->estado = $request->input('estado');
        $pedido->save();

        return redirect()->route('admin.pedidos.index')
                        ->with('success', 'Estado actualizado correctamente');
    }

    public function pagosProductor()
    {
        // USAR LA MISMA LÓGICA QUE EL ADMIN - startOfWeek() normal
        $fechaInicio = Carbon::now()->startOfWeek();
        $fechaFin = Carbon::now()->endOfWeek();

        // IMPORTANTE: Usar exactamente la misma lógica que el admin - solo pedidos ARMADOS
        $ventasDetalladas = OrderItem::whereHas('product', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereHas('order', function($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado', 'armado') // SOLO ESTADO ARMADO como en el admin
                  ->whereBetween('created_at', [
                      $fechaInicio->startOfDay(),
                      $fechaFin->endOfDay()
                  ]);
            })
            ->with(['product', 'order'])
            ->get();

        // Agrupar por producto para el resumen (misma lógica que admin)
        $pagos = $ventasDetalladas->groupBy('producto_id')
            ->map(function($items) {
                $producto = $items->first()->product;
                $cantidad = $items->sum('cantidad');
                $monto = $items->sum(function($item) {
                    return $item->cantidad * $item->precio;
                });
                
                return [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'monto' => $monto,
                    'pedidos_count' => $items->pluck('order_id')->unique()->count(),
                    'precio_promedio' => $cantidad > 0 ? $monto / $cantidad : 0,
                ];
            })
            ->values()
            ->sortByDesc('monto');

        // Calcular estadísticas generales
        $totalPagar = $pagos->sum('monto');
        $totalProductos = $pagos->count();
        $totalCantidad = $pagos->sum('cantidad');
        $totalPedidos = $ventasDetalladas->pluck('order_id')->unique()->count();

        // Para estadísticas adicionales, obtener todos los pedidos del período (no solo armados)
        $todasLasVentas = OrderItem::whereHas('product', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereHas('order', function($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado', '!=', 'cancelado')
                  ->whereBetween('created_at', [
                      $fechaInicio->startOfDay(),
                      $fechaFin->endOfDay()
                  ]);
            })
            ->with(['product', 'order'])
            ->get();

        // Estadísticas por estado de pedido (para información general)
        $estadisticas = [
            'pagados' => [
                'count' => $todasLasVentas->where('order.estado', 'pagado')->pluck('order_id')->unique()->count(),
                'monto' => $todasLasVentas->where('order.estado', 'pagado')->sum(function($item) {
                    return $item->cantidad * $item->precio;
                })
            ],
            'armados' => [
                'count' => $ventasDetalladas->pluck('order_id')->unique()->count(), // Solo armados
                'monto' => $totalPagar // Solo armados
            ],
            'entregados' => [
                'count' => $todasLasVentas->where('order.estado', 'entregado')->pluck('order_id')->unique()->count(),
                'monto' => $todasLasVentas->where('order.estado', 'entregado')->sum(function($item) {
                    return $item->cantidad * $item->precio;
                })
            ]
        ];

        // Top 5 productos más vendidos (solo de pedidos armados para pagos)
        $topProductos = $pagos->take(5);

        // Ventas por día de la semana (solo pedidos armados)
        $ventasPorDia = $ventasDetalladas->groupBy(function($item) {
            return $item->order->created_at->format('l'); // Nombre del día en inglés
        })->map(function($items) {
            return [
                'cantidad' => $items->sum('cantidad'),
                'monto' => $items->sum(function($item) {
                    return $item->cantidad * $item->precio;
                }),
                'pedidos' => $items->pluck('order_id')->unique()->count()
            ];
        });

        // Datos para gráficos (basados en pedidos armados)
        $chartData = [
            'productos_labels' => $topProductos->pluck('producto.nombre')->toArray(),
            'productos_ventas' => $topProductos->pluck('monto')->toArray(),
            'productos_cantidades' => $topProductos->pluck('cantidad')->toArray(),
            'dias_labels' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            'dias_montos' => [
                $ventasPorDia['Monday']['monto'] ?? 0,
                $ventasPorDia['Tuesday']['monto'] ?? 0,
                $ventasPorDia['Wednesday']['monto'] ?? 0,
                $ventasPorDia['Thursday']['monto'] ?? 0,
                $ventasPorDia['Friday']['monto'] ?? 0,
                $ventasPorDia['Saturday']['monto'] ?? 0,
                $ventasPorDia['Sunday']['monto'] ?? 0,
            ]
        ];

        return view('agricultor.pagos', compact(
            'pagos', 
            'totalPagar', 
            'totalProductos', 
            'totalCantidad', 
            'totalPedidos',
            'estadisticas',
            'topProductos',
            'ventasPorDia',
            'chartData',
            'fechaInicio',
            'fechaFin'
        ));
    }
    
    public function resumenPagosAgricultores(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfWeek());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfWeek());
        
        $agricultores = User::where('role', 'agricultor')
            ->whereHas('productos.orderItems.order', function($query) use ($fechaInicio, $fechaFin) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->with(['mercado'])
            ->get()
            ->map(function($agricultor) use ($fechaInicio, $fechaFin) {
                $ventas = OrderItem::whereHas('product', function($query) use ($agricultor) {
                    $query->where('user_id', $agricultor->id);
                })
                ->whereHas('order', function($query) use ($fechaInicio, $fechaFin) {
                    $query->where('estado', 'armado')
                          ->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                })
                ->with(['product', 'order'])
                ->get();

                $totalPagar = $ventas->sum(function($item) {
                    return $item->precio * $item->cantidad;
                });

                return [
                    'id' => $agricultor->id,
                    'nombre' => $agricultor->name,
                    'mercado' => $agricultor->mercado->name ?? 'Sin mercado',
                    'total_pagar' => $totalPagar,
                    'productos_vendidos' => $ventas->count(),
                    'pedidos_involucrados' => $ventas->pluck('order_id')->unique()->count()
                ];
            })
            ->filter(function($agricultor) {
                return $agricultor['total_pagar'] > 0;
            })
            ->sortByDesc('total_pagar')
            ->values();

        $totalGeneral = $agricultores->sum('total_pagar');

        return response()->json([
            'success' => true,
            'data' => [
                'agricultores' => $agricultores,
                'total_general' => $totalGeneral,
                'count_agricultores' => $agricultores->count(),
                'periodo' => [
                    'inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                    'fin' => Carbon::parse($fechaFin)->format('d/m/Y')
                ]
            ]
        ]);
    }

    /**
     * Generar reporte de pagos por período
     */
    public function reportePagosPeriodo(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());
        
        $pagosRealizados = \App\Models\PagoAgricultor::with(['agricultor', 'pagadoPor'])
            ->where('estado', 'pagado')
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->get();

        $pagosPendientes = \App\Models\PagoAgricultor::with(['agricultor'])
            ->where('estado', 'pendiente')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->get();

        $estadisticas = [
            'total_pagado' => $pagosRealizados->sum('monto_total'),
            'total_pendiente' => $pagosPendientes->sum('monto_total'),
            'count_pagados' => $pagosRealizados->count(),
            'count_pendientes' => $pagosPendientes->count(),
            'agricultores_pagados' => $pagosRealizados->pluck('agricultor_id')->unique()->count(),
            'agricultores_pendientes' => $pagosPendientes->pluck('agricultor_id')->unique()->count()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'estadisticas' => $estadisticas,
                'pagos_realizados' => $pagosRealizados,
                'pagos_pendientes' => $pagosPendientes,
                'periodo' => [
                    'inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                    'fin' => Carbon::parse($fechaFin)->format('d/m/Y')
                ]
            ]
        ]);
    }

    /**
     * Procesar pago masivo a múltiples agricultores
     */
    public function pagoMasivoAgricultores(Request $request)
    {
        $validatedData = $request->validate([
            'agricultores' => 'required|array',
            'agricultores.*' => 'exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'metodo_pago' => 'nullable|string',
            'referencia_pago' => 'nullable|string',
            'notas' => 'nullable|string'
        ]);

        $pagosCreados = [];
        $errores = [];

        foreach ($validatedData['agricultores'] as $agricultorId) {
            try {
                $pago = \App\Models\PagoAgricultor::crearPago(
                    $agricultorId,
                    $validatedData['fecha_inicio'],
                    $validatedData['fecha_fin'],
                    'pagado'
                );

                if ($pago) {
                    $pago->update([
                        'metodo_pago' => $validatedData['metodo_pago'],
                        'referencia_pago' => $validatedData['referencia_pago'],
                        'notas' => $validatedData['notas'],
                        'fecha_pago' => now(),
                        'pagado_por' => Auth::id()
                    ]);

                    $pagosCreados[] = $pago;
                } else {
                    $agricultor = User::find($agricultorId);
                    $errores[] = "No hay ventas para pagar a {$agricultor->name}";
                }
            } catch (\Exception $e) {
                $agricultor = User::find($agricultorId);
                $errores[] = "Error al procesar pago para {$agricultor->name}: {$e->getMessage()}";
            }
        }

        $totalPagado = collect($pagosCreados)->sum('monto_total');

        return response()->json([
            'success' => count($pagosCreados) > 0,
            'message' => count($pagosCreados) > 0 
                ? "Se procesaron " . count($pagosCreados) . " pagos por un total de S/ " . number_format($totalPagado, 2)
                : "No se pudo procesar ningún pago",
            'data' => [
                'pagos_creados' => $pagosCreados,
                'total_pagado' => $totalPagado,
                'errores' => $errores
            ]
        ]);
    }

    public function confirmarPedidoListo($id)
    {
        try {
            if (Auth::user()->role !== 'agricultor') {
                abort(403, 'No autorizado');
            }

            $pedido = Order::findOrFail($id);
            $agricultorId = Auth::id();

            // Verificar que el agricultor tenga productos en este pedido
            $productosAgricultor = $pedido->items->filter(function($item) use ($agricultorId) {
                return $item->product->user_id == $agricultorId;
            });

            if ($productosAgricultor->isEmpty()) {
                abort(403, 'No autorizado para modificar este pedido');
            }

            // Solo permitir marcar como listo si está pagado
            if ($pedido->estado !== 'pagado') {
                return redirect()->route('agricultor.pedidos_pendientes')
                                ->with('error', 'Solo se pueden marcar como listos los pedidos pagados');
            }

            // **REGISTRAR CONFIRMACIÓN DEL AGRICULTOR**
            \App\Models\OrderAgricultorConfirmation::updateOrCreate(
                [
                    'order_id' => $pedido->id,
                    'agricultor_id' => $agricultorId
                ],
                [
                    'confirmed_at' => now()
                ]
            );

            // **VERIFICAR SI TODOS LOS AGRICULTORES HAN CONFIRMADO**
            $agricultoresEnPedido = $pedido->items->pluck('product.user_id')->unique();
            $agricultoresConfirmados = \App\Models\OrderAgricultorConfirmation::where('order_id', $pedido->id)
                ->pluck('agricultor_id');

            Log::info("Pedido {$pedido->id}: Agricultores en pedido: " . $agricultoresEnPedido->count());
            Log::info("Pedido {$pedido->id}: Agricultores confirmados: " . $agricultoresConfirmados->count());
            Log::info("Pedido {$pedido->id}: Lista agricultores en pedido: " . $agricultoresEnPedido->implode(', '));
            Log::info("Pedido {$pedido->id}: Lista agricultores confirmados: " . $agricultoresConfirmados->implode(', '));

            // Solo cambiar a 'listo' si TODOS los agricultores han confirmado
            if ($agricultoresEnPedido->count() === $agricultoresConfirmados->count() && 
                $agricultoresEnPedido->diff($agricultoresConfirmados)->isEmpty()) {
                
                $pedido->estado = 'listo';
                $pedido->save();
                
                Log::info("Pedido {$pedido->id} cambiado a LISTO - todos los agricultores confirmaron");
                
                return redirect()->route('agricultor.pedidos_pendientes')
                                ->with('success', '¡Pedido marcado como LISTO! Todos los agricultores han confirmado. El admin lo armará pronto.');
            } else {
                Log::info("Pedido {$pedido->id} - agricultor confirmado pero faltan otros agricultores");
                
                $faltantes = $agricultoresEnPedido->count() - $agricultoresConfirmados->count();
                
                return redirect()->route('agricultor.pedidos_pendientes')
                                ->with('success', "Tu confirmación fue registrada. Faltan {$faltantes} agricultor(es) por confirmar sus productos.");
            }

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido listo: ' . $e->getMessage());
            
            return redirect()->route('agricultor.pedidos_pendientes')
                            ->with('error', 'Error al confirmar el pedido. Inténtalo de nuevo.');
        }
    }
}