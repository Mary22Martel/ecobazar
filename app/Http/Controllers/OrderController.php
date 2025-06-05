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
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;
use Carbon\Carbon; 

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
                'pago' => 'required|in:sistema' // Solo permitir pago por sistema
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
            $mercadoActual = 1; // Siempre usar mercado 1
            Log::info('Mercado forzado a: ' . $mercadoActual);

            // COMENTADO TEMPORALMENTE: Validación de mercado
            /*
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
            */

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
            $orden->pago = 'sistema'; // Siempre sistema
            $orden->total = $total;
            $orden->estado = 'pendiente'; // Siempre pendiente hasta confirmar pago
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

            // Siempre procesar pago con MercadoPago
            return $this->procesarPagoMercadoPago($orden, $subtotal, $costoEnvio);

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
                "title" => "Pedido #" . $orden->id . " - Punto Verde ",
                "quantity" => 1,
                "currency_id" => "PEN",
                "unit_price" => floatval($subtotal + $costoEnvio)
            ];

            $preferenceData = [
                "items" => $items,
                "back_urls" => [
                    "success" => "http://127.0.0.1:8000/orden-exito/" . $orden->id,
                    "failure" => "http://127.0.0.1:8000/order/failed",
                    "pending" => "http://127.0.0.1:8000/orden-exito/" . $orden->id
                ],
                "external_reference" => strval($orden->id),
                "statement_descriptor" => "Punto Verde"
            ];

            // AGREGAR BREAKDOWN PARA MOSTRAR EL DESGLOSE
            if ($costoEnvio > 0) {
                $preferenceData["differential_pricing"] = [
                    "id" => 1
                ];
                
                // Agregar información adicional en el título del item
                $items[0]["title"] = "Pedido #" . $orden->id . " - Productos: S/" . number_format($subtotal, 2) . " + Envío: S/" . number_format($costoEnvio, 2);
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
            Log::info("=== ACCESO A PÁGINA DE ÉXITO ===");
            Log::info("Orden ID: {$orderId}");
            
            $orden = Order::with(['items.product', 'user'])->findOrFail($orderId);
            
            if ($orden->estado === 'pendiente') {
                Log::info("Orden pendiente, verificando pago con MercadoPago...");
                $this->verificarYActualizarPago($orden);
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

    /**
     * Verificar el estado del pago con MercadoPago y actualizar la orden
     */
    private function verificarYActualizarPago($orden)
    {
        try {
            // NUEVO: Para desarrollo local - marcar automáticamente como pagado
            if (config('app.env') === 'local' && $orden->estado === 'pendiente') {
                $orden->estado = 'pagado';
                $orden->paid_at = now();
                $orden->save();
                Log::info("Orden {$orden->id} marcada como pagada (desarrollo local)");
                return;
            }

            $paymentId = request()->get('payment_id') ?? request()->get('collection_id');
            
            if (!$paymentId) {
                Log::warning("No se encontró payment_id para verificar la orden {$orden->id}");
                if ($orden->estado === 'pendiente') {
                    $orden->estado = 'pagado';
                    $orden->paid_at = now();
                    $orden->save();
                    Log::info("Orden {$orden->id} marcada como pagada por fallback");
                }
                return;
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
                        break;
                        
                    case 'rejected':
                    case 'cancelled':
                        $orden->estado = 'cancelado';
                        $orden->save();
                        Log::info("Orden {$orden->id} marcada como cancelada");
                        break;
                        
                    default:
                        Log::info("Pago aún en proceso, estado: {$payment->status}");
                        break;
                }
            } else {
                Log::warning("El external_reference del pago ({$payment->external_reference}) no coincide con la orden ({$orden->id})");
            }
            
        } catch (Exception $e) {
            Log::error("Error verificando pago para orden {$orden->id}: " . $e->getMessage());
            
            if ($orden->estado === 'pendiente') {
                $orden->estado = 'pagado';
                $orden->paid_at = now();
                $orden->save();
                Log::info("Orden {$orden->id} marcada como pagada por fallback");
            }
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
        ->whereIn('estado', ['armado', 'entregado']) // Ya listos = generan pago
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

            // Verificar que el agricultor tenga productos en este pedido
            $productosAgricultor = $pedido->items->filter(function($item) {
                return $item->product->user_id == Auth::id();
            });

            if ($productosAgricultor->isEmpty()) {
                abort(403, 'No autorizado para modificar este pedido');
            }

            // Solo permitir marcar como listo si está pagado
            if ($pedido->estado !== 'pagado') {
                return redirect()->route('agricultor.pedidos_pendientes')
                                ->with('error', 'Solo se pueden marcar como listos los pedidos pagados');
            }

            // Cambiar directamente a "armado" 
            // (Esto significa: "El agricultor tiene todo listo, considerarlo para pago")
            $pedido->estado = 'armado';
            $pedido->save();

            return redirect()->route('agricultor.pedidos_pendientes')
                            ->with('success', '¡Pedido marcado como listo! Ya se considera para tu pago.');

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido listo: ' . $e->getMessage());
            
            return redirect()->route('agricultor.pedidos_pendientes')
                            ->with('error', 'Error al confirmar el pedido. Inténtalo de nuevo.');
        }
    }
}