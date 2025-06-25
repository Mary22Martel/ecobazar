<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;

class AgricultorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorizeRoles(['agricultor']);
        
        // ========== CÁLCULO DE SEMANA DE FERIA ACTUAL ==========
        $semanaActual = $this->calcularSemanaFeria();
        $inicioSemana = $semanaActual['inicio_ventas'];  // Domingo
        $finSemana = $semanaActual['fin_ventas'];        // Viernes
        $diaEntrega = $semanaActual['dia_entrega'];      // Sábado
        
        $agricultor = Auth::user();
        
        // ========== PEDIDOS DE ESTA SEMANA SOLAMENTE ==========
        
        // Pedidos por armar de esta semana (pendiente + pagado)
        $pendientesSemana = Order::whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->whereIn('estado', ['pendiente', 'pagado'])
            ->whereBetween('created_at', [
                $inicioSemana->startOfDay(), 
                $finSemana->endOfDay()
            ])
            ->count();
        
        // Pedidos listos de esta semana (listo + armado + entregado)
        $listosSemana = Order::whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->whereIn('estado', ['listo', 'armado', 'entregado'])
            ->whereBetween('created_at', [
                $inicioSemana->startOfDay(), 
                $finSemana->endOfDay()
            ])
            ->count();

        // Total de productos en el catálogo (no por semana)
        $totalProductos = Product::where('user_id', $agricultor->id)->count();
        
        // ========== VARIABLES PARA LA VISTA ==========
        $pendientes = $pendientesSemana;
        $listos = $listosSemana;
        
        return view('agricultor.dashboard', compact(
            'pendientes',
            'listos', 
            'totalProductos',
            'inicioSemana',
            'finSemana',
            'diaEntrega'
        ));
    }

    // ==================== PEDIDOS PENDIENTES CON FILTRO ====================
    
    /**
     * Muestra los pedidos pendientes (pagados) que el agricultor debe preparar
     */
    public function pedidosPendientes(Request $request)
    {
        $this->authorizeRoles(['agricultor']);
        
        // Configurar Carbon para español
        Carbon::setLocale('es');
        
        // Obtener la semana seleccionada (por defecto la actual = 0)
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $fechaInicio = $semanaFeria['inicio_ventas'];
        $fechaFin = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $agricultor = Auth::user();
        
        // Generar opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Obtener pedidos que están en estado 'pagado' (pendientes de preparar)
        // filtrados por la semana seleccionada
        $pedidos = Order::whereIn('estado', ['pendiente', 'pagado'])
            ->whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->whereBetween('created_at', [
                $fechaInicio->startOfDay(), 
                $fechaFin->endOfDay()
            ])
            ->with(['items.product' => function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        
            $pedidosConProgreso = $pedidos->map(function($pedido) use ($agricultor) {
            $agricultoresTotal = $pedido->items->pluck('product.user_id')->unique()->count();
            $agricultoresConfirmados = \App\Models\OrderAgricultorConfirmation::where('order_id', $pedido->id)->count();
            $yaConfirme = \App\Models\OrderAgricultorConfirmation::where('order_id', $pedido->id)
                ->where('agricultor_id', $agricultor->id)
                ->exists();
            
            $pedido->progreso_confirmacion = [
                'total' => $agricultoresTotal,
                'confirmados' => $agricultoresConfirmados,
                'ya_confirme' => $yaConfirme,
                'porcentaje' => $agricultoresTotal > 0 ? round(($agricultoresConfirmados / $agricultoresTotal) * 100) : 0,
                'puede_confirmar' => $pedido->estado === 'pagado' && !$yaConfirme
            ];
            
            return $pedido;
        });

        // Calcular estadísticas rápidas para esta semana
        $estadisticas = $this->calcularEstadisticasSemanaAgricultor($agricultor->id, $fechaInicio, $fechaFin);

        return view('agricultor.pedidos_pendientes', compact(
            'pedidos',
            'pedidosConProgreso',
            'fechaInicio', 
            'fechaFin', 
            'diaEntrega',
            'opcionesSemanas',
            'semanaSeleccionada',
            'estadisticas'
        ));
    }

    /**
     * Muestra los pedidos que ya están listos (preparados por el agricultor)
     */
    public function pedidosListos(Request $request)
    {
        $this->authorizeRoles(['agricultor']);
        
        // Configurar Carbon para español
        Carbon::setLocale('es');
        
        // Obtener la semana seleccionada (por defecto la actual = 0)
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $fechaInicio = $semanaFeria['inicio_ventas'];
        $fechaFin = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $agricultor = Auth::user();
        
        // Generar opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Obtener pedidos que están en estado 'listo', 'armado' o 'entregado'
        // filtrados por la semana seleccionada
        $pedidos = Order::whereIn('estado', ['listo', 'armado', 'entregado'])
            ->whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->whereBetween('created_at', [
                $fechaInicio->startOfDay(), 
                $fechaFin->endOfDay()
            ])
            ->with(['items.product' => function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calcular estadísticas rápidas para esta semana
        $estadisticas = $this->calcularEstadisticasSemanaAgricultor($agricultor->id, $fechaInicio, $fechaFin);

        return view('agricultor.pedidos_listos', compact(
            'pedidos', 
            'fechaInicio', 
            'fechaFin', 
            'diaEntrega',
            'opcionesSemanas',
            'semanaSeleccionada',
            'estadisticas'
        ));
    }

    /**
     * Confirma que el agricultor tiene listo su pedido
     * IMPORTANTE: Solo marca como listo, NO como armado
     */
    public function confirmarPedidoListo($pedidoId)
    {
        $this->authorizeRoles(['agricultor']);
        
        try {
            $agricultor = Auth::user();
            $agricultorId = $agricultor->id;
            
            // Buscar el pedido
            $pedido = Order::findOrFail($pedidoId);

            // Verificar que el agricultor tenga productos en este pedido
            $productosAgricultor = $pedido->items->filter(function($item) use ($agricultorId) {
                return $item->product->user_id == $agricultorId;
            });

            if ($productosAgricultor->isEmpty()) {
                return redirect()->back()->with('error', 'No tienes productos en este pedido.');
            }

            // Solo permitir marcar como listo si está pagado
            if ($pedido->estado !== 'pagado') {
                return redirect()->back()->with('error', 'Solo se pueden marcar como listos los pedidos pagados.');
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

            // Solo cambiar a 'listo' si TODOS los agricultores han confirmado
            if ($agricultoresEnPedido->count() === $agricultoresConfirmados->count() && 
                $agricultoresEnPedido->diff($agricultoresConfirmados)->isEmpty()) {
                
                $pedido->update([
                    'estado' => 'listo',
                    'fecha_listo' => now()
                ]);
                
                Log::info("Pedido {$pedido->id} cambiado a LISTO - todos los agricultores confirmaron");
                
                return redirect()->route('agricultor.pedidos_pendientes')
                    ->with('success', '¡Pedido #' . $pedido->id . ' marcado como LISTO! Todos los agricultores han confirmado. El administrador lo revisará para armarlo.');
            } else {
                Log::info("Pedido {$pedido->id} - agricultor confirmado pero faltan otros agricultores");
                
                $faltantes = $agricultoresEnPedido->count() - $agricultoresConfirmados->count();
                
                return redirect()->route('agricultor.pedidos_pendientes')
                    ->with('success', "Tu confirmación fue registrada para el pedido #{$pedido->id}. Faltan {$faltantes} agricultor(es) por confirmar sus productos.");
            }

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido listo: ' . $e->getMessage());
            
            return redirect()->route('agricultor.pedidos_pendientes')
                ->with('error', 'Error al confirmar el pedido. Inténtalo de nuevo.');
        }
    }

    /**
     * Muestra el detalle completo de un pedido específico
     */
    public function detallePedido($pedidoId)
    {
        $this->authorizeRoles(['agricultor']);
        
        $agricultor = Auth::user();
        
        $pedido = Order::where('id', $pedidoId)
            ->whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->with(['items.product' => function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            }])
            ->first();

        if (!$pedido) {
            return redirect()->back()->with('error', 'Pedido no encontrado.');
        }

        // AGREGAR ESTA LÍNEA: Filtrar solo los productos del agricultor
        $productosAgricultor = $pedido->items->where('product.user_id', $agricultor->id);

        // CORREGIR EL COMPACT: Agregar $productosAgricultor
        return view('agricultor.pedido_detalle', compact('pedido', 'productosAgricultor'));
    }

    // ==================== LÓGICA DE SEMANA DE FERIA ====================
    
    /**
     * Calcula la semana de feria: domingo a viernes (ventas) + sábado (entrega)
     * MISMA LÓGICA QUE EL ADMIN CONTROLLER
     */
    private function calcularSemanaFeria($fecha = null, $semanaOffset = 0)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::now();
        $fecha = $fecha->subWeeks($semanaOffset);
        
        // Si es domingo, esa fecha es el inicio de la semana de feria
        if ($fecha->dayOfWeek === Carbon::SUNDAY) {
            $inicioSemana = $fecha->copy();
        } else {
            // Buscar el domingo anterior
            $inicioSemana = $fecha->copy()->previous(Carbon::SUNDAY);
        }
        
        // El viernes de esa semana es el último día de ventas
        $finVentas = $inicioSemana->copy()->addDays(6); // Viernes
        
        // El sábado es día de entrega en la feria
        $diaEntrega = $inicioSemana->copy()->addDays(6); // Sábado
        
        return [
            'inicio_ventas' => $inicioSemana,      // Domingo
            'fin_ventas' => $finVentas,            // Viernes  
            'dia_entrega' => $diaEntrega           // Sábado
        ];
    }

    /**
     * Genera las opciones de semanas para los selectores
     * MISMA LÓGICA QUE EL ADMIN CONTROLLER
     */
    private function generarOpcionesSemanasFeria($cantidadSemanas = 5)
    {
        $opciones = [];
        
        for ($i = 0; $i < $cantidadSemanas; $i++) {
            $semanaFeria = $this->calcularSemanaFeria(null, $i);
            
            $inicio = $semanaFeria['inicio_ventas'];
            $fin = $semanaFeria['fin_ventas'];
            $entrega = $semanaFeria['dia_entrega'];
            
            $label = sprintf(
                "Ventas: %s al %s (Entrega: %s)",
                $inicio->format('d/m'),
                $fin->format('d/m'),
                $entrega->format('d/m/Y')
            );
            
            if ($i === 0) {
                $label .= " - Actual";
            }
            
            $opciones[$i] = $label;
        }
        
        return $opciones;
    }

    // ==================== ESTADÍSTICAS SIMPLIFICADAS ====================
    
    /**
     * Calcula estadísticas rápidas para el agricultor en una semana específica
     * CORREGIDO: Solo cuenta pedidos de esa semana específica como en AdminController
     */
    private function calcularEstadisticasSemanaAgricultor($agricultorId, $fechaInicio, $fechaFin)
    {
        $estadisticas = [];
        $estados = ['pendiente', 'pagado', 'listo', 'armado', 'entregado'];

        foreach($estados as $estado) {
            // CORREGIDO: Solo pedidos de la semana específica
            $pedidos = Order::whereHas('items.product', function($query) use ($agricultorId) {
                    $query->where('user_id', $agricultorId);
                })
                ->where('estado', $estado)
                ->whereBetween('created_at', [
                    $fechaInicio->startOfDay(), 
                    $fechaFin->endOfDay()
                ])
                ->with(['items.product' => function($query) use ($agricultorId) {
                    $query->where('user_id', $agricultorId);
                }])
                ->get();

            $monto = 0;
            $cantidad = 0;
            
            foreach($pedidos as $pedido) {
                foreach($pedido->items as $item) {
                    if($item->product && $item->product->user_id == $agricultorId) {
                        $monto += $item->cantidad * $item->precio;
                        $cantidad += $item->cantidad;
                    }
                }
            }

            $estadisticas[$estado] = [
                'count' => $pedidos->count(),
                'monto' => $monto,
                'cantidad' => $cantidad
            ];
        }

        return $estadisticas;
    }

    // ==================== PAGOS POR SEMANAS (MANTENER IGUAL) ====================
    
    public function pagos(Request $request)
    {
        $this->authorizeRoles(['agricultor']);
        
        // Configurar Carbon para español
        Carbon::setLocale('es');
        
        // Obtener la semana seleccionada (por defecto la actual = 0)
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $fechaInicio = $semanaFeria['inicio_ventas'];
        $fechaFin = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $agricultor = Auth::user();
        
        // Generar opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Obtener datos de pagos para la semana seleccionada
        $datosCalculados = $this->calcularPagosAgricultor($agricultor->id, $fechaInicio, $fechaFin);
        
        return view('agricultor.pagos', [
            'pagos' => $datosCalculados['pagos'],
            'totalPagar' => $datosCalculados['totalPagar'],
            'totalProductos' => $datosCalculados['totalProductos'],
            'totalCantidad' => $datosCalculados['totalCantidad'],
            'totalPedidos' => $datosCalculados['totalPedidos'],
            'topProductos' => $datosCalculados['topProductos'],
            'estadisticas' => $datosCalculados['estadisticas'],
            'ventasPorDia' => $datosCalculados['ventasPorDia'],
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'diaEntrega' => $diaEntrega,
            'opcionesSemanas' => $opcionesSemanas,
            'semanaSeleccionada' => $semanaSeleccionada
        ]);
    }
    
    /**
     * MÉTODO CORREGIDO PARA COINCIDIR EXACTAMENTE CON EL ADMIN
     * Usa la misma lógica que calcularPagosSemanales() del AdminController
     */
    private function calcularPagosAgricultor($agricultorId, $fechaInicio, $fechaFin)
    {
        // Obtener pedidos armados del agricultor en el período
        // MISMA LÓGICA QUE EL ADMIN: solo pedidos con estado 'armado'
        $pedidosArmados = Order::whereHas('items.product', function($query) use ($agricultorId) {
                $query->where('user_id', $agricultorId);
            })
            ->whereIn('estado', ['armado', 'en_entrega', 'entregado'])
            ->whereBetween('created_at', [
                $fechaInicio->startOfDay(), 
                $fechaFin->endOfDay()
            ])
            ->with(['items.product' => function($query) use ($agricultorId) {
                $query->where('user_id', $agricultorId);
            }])
            ->get();

        // Usar array en lugar de Collection para evitar errores
        $pagosArray = [];
        $totalPagar = 0;
        $totalProductos = 0;
        $totalCantidad = 0;

        foreach($pedidosArmados as $pedido) {
            foreach($pedido->items as $item) {
                if($item->product && $item->product->user_id == $agricultorId) {
                    $producto = $item->product;
                    $monto = $item->cantidad * $item->precio;
                    $productoId = $producto->id;
                    
                    // Verificar si ya existe este producto en el array
                    if(isset($pagosArray[$productoId])) {
                        // Actualizar datos existentes
                        $pagosArray[$productoId]['cantidad'] += $item->cantidad;
                        $pagosArray[$productoId]['monto'] += $monto;
                        $pagosArray[$productoId]['pedidos_count']++;
                        $pagosArray[$productoId]['precio_promedio'] = $pagosArray[$productoId]['monto'] / $pagosArray[$productoId]['cantidad'];
                    } else {
                        // Agregar nuevo producto
                        $pagosArray[$productoId] = [
                            'producto' => $producto,
                            'cantidad' => $item->cantidad,
                            'monto' => $monto,
                            'pedidos_count' => 1,
                            'precio_promedio' => $item->precio
                        ];
                        $totalProductos++;
                    }
                    
                    $totalPagar += $monto;
                    $totalCantidad += $item->cantidad;
                }
            }
        }

        // Convertir array a Collection y ordenar por monto descendente
        $pagos = collect($pagosArray)->sortByDesc('monto');

        // Top 5 productos
        $topProductos = $pagos->take(5);

        // Estadísticas por estado
        $estadisticas = $this->calcularEstadisticasAgricultor($agricultorId, $fechaInicio, $fechaFin);

        // Ventas por día (solo domingo a viernes)
        $ventasPorDia = $this->calcularVentasPorDia($agricultorId, $fechaInicio, $fechaFin);

        return [
            'pagos' => $pagos,
            'totalPagar' => $totalPagar,
            'totalProductos' => $totalProductos,
            'totalCantidad' => $totalCantidad,
            'totalPedidos' => $pedidosArmados->count(),
            'topProductos' => $topProductos,
            'estadisticas' => $estadisticas,
            'ventasPorDia' => $ventasPorDia
        ];
    }

    private function calcularEstadisticasAgricultor($agricultorId, $fechaInicio, $fechaFin)
    {
        $estadisticas = [];
        $estados = ['pagado', 'listo', 'armado', 'en_entrega', 'entregado'];

        foreach($estados as $estado) {
            $pedidos = Order::whereHas('items.product', function($query) use ($agricultorId) {
                    $query->where('user_id', $agricultorId);
                })
                ->where('estado', $estado)
                ->whereBetween('created_at', [
                    $fechaInicio->startOfDay(), 
                    $fechaFin->endOfDay()
                ])
                ->with(['items.product' => function($query) use ($agricultorId) {
                    $query->where('user_id', $agricultorId);
                }])
                ->get();

            $monto = 0;
            $cantidad = 0;
            foreach($pedidos as $pedido) {
                foreach($pedido->items as $item) {
                    if($item->product && $item->product->user_id == $agricultorId) {
                        $monto += $item->cantidad * $item->precio;
                        $cantidad += $item->cantidad;
                    }
                }
            }

            $estadisticas[$estado] = [
                'count' => $pedidos->count(),
                'monto' => $monto,
                'cantidad' => $cantidad
            ];
        }

        return $estadisticas;
    }

    /**
     * CORREGIDO: Solo considera los días de venta (domingo a viernes)
     */
    private function calcularVentasPorDia($agricultorId, $fechaInicio, $fechaFin)
    {
        $ventasPorDia = [];
        
        // Solo los días de venta: domingo a viernes
        $diasVenta = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        foreach($diasVenta as $dia) {
            $ventasPorDia[$dia] = ['monto' => 0, 'pedidos' => 0, 'cantidad' => 0];
        }

        $pedidos = Order::whereHas('items.product', function($query) use ($agricultorId) {
                $query->where('user_id', $agricultorId);
            })
            ->whereIn('estado', ['armado', 'en_entrega', 'entregado'])
            ->whereBetween('created_at', [
                $fechaInicio->startOfDay(), 
                $fechaFin->endOfDay()
            ])
            ->with(['items.product' => function($query) use ($agricultorId) {
                $query->where('user_id', $agricultorId);
            }])
            ->get();

        foreach($pedidos as $pedido) {
            $diaSemana = $pedido->created_at->format('l'); // Sunday, Monday, etc.
            
            // Solo procesar si es un día de venta
            if(in_array($diaSemana, $diasVenta)) {
                $montoPedido = 0;
                $cantidadPedido = 0;

                foreach($pedido->items as $item) {
                    if($item->product && $item->product->user_id == $agricultorId) {
                        $montoPedido += $item->cantidad * $item->precio;
                        $cantidadPedido += $item->cantidad;
                    }
                }

                if($montoPedido > 0) {
                    $ventasPorDia[$diaSemana]['monto'] += $montoPedido;
                    $ventasPorDia[$diaSemana]['pedidos']++;
                    $ventasPorDia[$diaSemana]['cantidad'] += $cantidadPedido;
                }
            }
        }

        return $ventasPorDia;
    }

    // Método para exportar pagos del agricultor individual
    public function exportarPagosPDF(Request $request)
{
    $this->authorizeRoles(['agricultor']);
    
    $semanaSeleccionada = $request->get('semana', 0);
    
    $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
    $fechaInicio = $semanaFeria['inicio_ventas'];
    $fechaFin = $semanaFeria['fin_ventas'];
    $diaEntrega = $semanaFeria['dia_entrega'];
    
    $agricultor = Auth::user();
    $datosCalculados = $this->calcularPagosAgricultor($agricultor->id, $fechaInicio, $fechaFin);
    
    // Generar HTML para PDF
    $html = view('agricultor.pagos-pdf', [
        'agricultor' => $agricultor,
        'pagos' => $datosCalculados['pagos'],
        'totalPagar' => $datosCalculados['totalPagar'],
        'totalProductos' => $datosCalculados['totalProductos'],
        'totalCantidad' => $datosCalculados['totalCantidad'],
        'totalPedidos' => $datosCalculados['totalPedidos'],
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin,
        'diaEntrega' => $diaEntrega,
        'estadisticas' => $datosCalculados['estadisticas']
    ])->render();
    
    // Si tienes dompdf instalado:
    $pdf = PDF::loadHtml($html);
    $filename = "pagos_{$agricultor->name}_entrega_{$diaEntrega->format('d-m-Y')}.pdf";
    
    return $pdf->download($filename);
    
    // Si NO tienes dompdf, usa mPDF o regresa HTML simple:
    /*
    $filename = "pagos_{$agricultor->name}_entrega_{$diaEntrega->format('d-m-Y')}.html";
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', "attachment; filename={$filename}");
    */
}

    /**
     * Método para obtener el detalle completo como el admin
     */
    public function detallePagos(Request $request)
    {
        $this->authorizeRoles(['agricultor']);
        
        $semanaSeleccionada = $request->get('semana', 0);
        
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $fechaInicio = $semanaFeria['inicio_ventas'];
        $fechaFin = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $agricultor = Auth::user();
        
        // Obtener el detalle exacto como lo ve el admin
        $detallesPago = [];
        $totalPago = 0;
        
        $pedidosArmados = Order::whereHas('items.product', function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            })
            ->whereIn('estado', ['armado', 'en_entrega', 'entregado'])
            ->whereBetween('created_at', [
                $fechaInicio->startOfDay(), 
                $fechaFin->endOfDay()
            ])
            ->with(['items.product' => function($query) use ($agricultor) {
                $query->where('user_id', $agricultor->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach($pedidosArmados as $pedido) {
            foreach($pedido->items as $item) {
                if($item->product && $item->product->user_id == $agricultor->id) {
                    $subtotal = $item->cantidad * $item->precio;
                    $totalPago += $subtotal;
                    
                    $detallesPago[] = [
                        'pedido_id' => $pedido->id,
                        'producto' => $item->product->nombre,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio,
                        'subtotal' => $subtotal,
                        'fecha_pedido' => $pedido->created_at,
                        'cliente' => $pedido->nombre . ' ' . $pedido->apellido
                    ];
                }
            }
        }
        
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('agricultor.detalle-pagos', compact(
            'detallesPago',
            'totalPago',
            'agricultor',
            'fechaInicio',
            'fechaFin',
            'diaEntrega',
            'semanaSeleccionada',
            'opcionesSemanas'
        ));
    }

    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }
}