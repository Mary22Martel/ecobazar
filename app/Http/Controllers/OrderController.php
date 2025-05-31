<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Carrito;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr; // Agregar esta línea
use Barryvdh\DomPDF\Facade\Pdf;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;

class OrderController extends Controller
{
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
                'pago' => 'required|in:puesto,sistema'
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

            // Verificar que todos los productos pertenezcan al mismo mercado
            $mercadoActual = session('mercado_actual');
            if (!$mercadoActual) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay un mercado seleccionado'
                ], 400);
            }

            // Validar que todos los productos sean del mercado actual
            foreach ($carrito->items as $item) {
                if ($item->product->user->mercado_id != $mercadoActual) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hay productos de diferentes mercados en tu carrito'
                    ], 400);
                }
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($carrito->items as $item) {
                $subtotal += ($item->product->precio * $item->cantidad);
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

                // Buscar repartidor disponible para esta zona
                $repartidor = User::whereHas('zones', function($query) use ($zona) {
                    $query->where('zones.id', $zona->id);
                })->where('role', 'repartidor')->first();

                if ($repartidor) {
                    $repartidorId = $repartidor->id;
                    Log::info('Repartidor asignado: ' . $repartidor->id);
                }
            }

            $total = $subtotal + $costoEnvio;

            Log::info("Totales calculados - Subtotal: {$subtotal}, Envío: {$costoEnvio}, Total: {$total}");

            // Crear la orden
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
            $orden->pago = $validatedData['pago'];
            $orden->total = $total;
            $orden->estado = ($validatedData['pago'] === 'sistema') ? 'pendiente_pago' : 'confirmado';
            $orden->repartidor_id = $repartidorId;
            $orden->save();

            Log::info('Orden creada con ID: ' . $orden->id);

            // Crear los items de la orden y actualizar inventario
            foreach ($carrito->items as $item) {
                // Verificar stock disponible
                if ($item->product->cantidad_disponible < $item->cantidad) {
                    return response()->json([
                        'success' => false,
                        'error' => "Stock insuficiente para: {$item->product->nombre}"
                    ], 400);
                }

                // Crear item de orden
                $orden->items()->create([
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->product->precio
                ]);

                // Actualizar stock
                $item->product->decrement('cantidad_disponible', $item->cantidad);
                Log::info("Stock actualizado para producto {$item->product->nombre}");
            }

            // Limpiar carrito
            $carrito->items()->delete();
            $carrito->delete();
            Log::info('Carrito limpiado');

            // Procesar pago según el método elegido
            if ($validatedData['pago'] === 'sistema') {
                return $this->procesarPagoMercadoPago($orden, $subtotal, $costoEnvio);
            } else {
                // Pago en puesto - redirigir directamente
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('order.success', $orden->id)
                ]);
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

   private function procesarPagoMercadoPago($orden, $subtotal, $costoEnvio)
    {
         try {
        Log::info('=== INICIANDO MERCADO PAGO ===');
        
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
        $client = new PreferenceClient();

        // Preparar items para MercadoPago
        $items = [];
        
        // Item principal con el total
        $items[] = [
            "title" => "Pedido ",
            "quantity" => 1,
            "currency_id" => "PEN",
            "unit_price" => floatval($subtotal + $costoEnvio)
        ];

        $preferenceData = [
            "items" => $items,
            "back_urls" => [
                "success" => url('/orden-exito/' . $orden->id),
                "failure" => url('/order/failed'),
                "pending" => url('/orden-exito/' . $orden->id)
            ],
            "external_reference" => strval($orden->id),
            "statement_descriptor" => "ECOBAZAR"
        ];

        // AGREGAR BREAKDOWN PARA MOSTRAR EL DESGLOSE
        if ($costoEnvio > 0) {
            $preferenceData["differential_pricing"] = [
                "id" => 1
            ];
            
            // Agregar información adicional en el título del item
            $items[0]["title"] = "Productos: S/" . number_format($subtotal, 2) . " + Envío: S/" . number_format($costoEnvio, 2);
        }

        $preferenceData["items"] = $items;

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
            
            // Manejo seguro de la respuesta del error
            try {
                $apiResponse = $e->getApiResponse();
                if ($apiResponse) {
                    $content = $apiResponse->getContent();
                    // Verificar si el contenido es un array y convertirlo a JSON para el log
                    if (is_array($content)) {
                        Log::error('MercadoPago Response (Array): ' . json_encode($content, JSON_PRETTY_PRINT));
                    } elseif (is_string($content)) {
                        Log::error('MercadoPago Response (String): ' . $content);
                    } else {
                        Log::error('MercadoPago Response (Other): ' . print_r($content, true));
                    }
                } else {
                    Log::error('No se pudo obtener respuesta de MercadoPago API');
                }
            } catch (Exception $logException) {
                Log::error('Error al obtener detalles del error de MercadoPago: ' . $logException->getMessage());
            }
            
            // También verificar el código de estado HTTP si está disponible
            try {
                if (method_exists($e, 'getApiResponse') && $e->getApiResponse()) {
                    $statusCode = $e->getApiResponse()->getStatusCode();
                    Log::error('MercadoPago HTTP Status Code: ' . $statusCode);
                }
            } catch (Exception $statusException) {
                Log::error('Error obteniendo status code: ' . $statusException->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago con MercadoPago. Verifica tu configuración.'
            ], 500);
        } catch (Exception $e) {
            Log::error('Error general MercadoPago: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago'
            ], 500);
        }
    }

    public function success($orderId)
    {
        try {
            $orden = Order::with(['items.product', 'user'])->findOrFail($orderId);
            
            // Si viene de MercadoPago y aún está pendiente, marcar como pagado
            if ($orden->estado === 'pendiente_pago') {
                $orden->estado = 'pagado';
                $orden->save();
                Log::info("Orden {$orderId} marcada como pagada");
            }

            // Calcular subtotal y envío para la vista
            $subtotal = $orden->items->sum(function($item) {
                return $item->precio * $item->cantidad;
            });

            $costoEnvio = 0;
            if ($orden->delivery === 'delivery' && $orden->distrito) {
                $zona = Zone::where('name', $orden->distrito)->first();
                if ($zona) {
                    $costoEnvio = $zona->delivery_cost;
                }
            }

            return view('order.success', compact('orden', 'subtotal', 'costoEnvio'));
            
        } catch (Exception $e) {
            Log::error('Error en success: ' . $e->getMessage());
            return redirect()->route('tienda')->with('error', 'Orden no encontrada');
        }
    }

    public function failed()
    {
        return view('order.failed');
    }

    public function downloadVoucher($orderId)
    {
        try {
            $orden = Order::with('items.product')->findOrFail($orderId);

            $subtotal = $orden->items->sum(function($item) {
                return $item->precio * $item->cantidad;
            });

            $costoEnvio = 0;
            if ($orden->delivery === 'delivery' && $orden->distrito) {
                $zona = Zone::where('name', $orden->distrito)->first();
                if ($zona) {
                    $costoEnvio = $zona->delivery_cost;
                }
            }

            $total = $subtotal + $costoEnvio;

            $pdf = PDF::loadView('order.voucher', compact('orden', 'subtotal', 'costoEnvio', 'total'));
            
            return $pdf->download("voucher_orden_{$orderId}.pdf");
            
        } catch (Exception $e) {
            Log::error('Error generando voucher: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el voucher');
        }
    }

    // Métodos para agricultores
    public function pedidosPendientes()
    {
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No autorizado');
        }

        $pedidos = Order::whereHas('items.product', function($query) {
            $query->where('user_id', Auth::id());
        })->with(['items.product', 'user'])->orderBy('created_at', 'desc')->get();

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

    public function confirmarPedidoListo($id)
    {
        $pedido = Order::findOrFail($id);

        $productosAgricultor = $pedido->items->filter(function($item) {
            return $item->product->user_id == Auth::id();
        });

        if ($productosAgricultor->isEmpty()) {
            abort(403, 'No autorizado');
        }

        $pedido->estado = 'listo';
        $pedido->save();

        return redirect()->route('agricultor.pedidos_pendientes')
                        ->with('success', 'Pedido marcado como listo');
    }

    // Métodos para admin
    public function todosLosPedidos()
    {
        $pedidos = Order::with(['items.product', 'user'])
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
        $pagos = \App\Models\OrderItem::whereHas('product', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereHas('order', function($q) {
                $q->where('estado', 'listo');
            })
            ->get()
            ->groupBy('producto_id')
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
                ];
            })
            ->values();

        $totalPagar = $pagos->sum('monto');

        return view('agricultor.pagos', compact('pagos', 'totalPagar'));
    }
}