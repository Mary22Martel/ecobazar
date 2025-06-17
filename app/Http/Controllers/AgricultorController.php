<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgricultorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('agricultor.dashboard');
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
        $finVentas = $inicioSemana->copy()->addDays(5); // Viernes
        
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

    // ==================== PAGOS POR SEMANAS ====================
    
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
            ->where('estado', 'armado')
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
        $estados = ['pagado', 'listo', 'armado', 'entregado'];

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
            ->where('estado', 'armado')
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
    public function exportarPagos(Request $request)
    {
        $this->authorizeRoles(['agricultor']);
        
        $semanaSeleccionada = $request->get('semana', 0);
        
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $fechaInicio = $semanaFeria['inicio_ventas'];
        $fechaFin = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $agricultor = Auth::user();
        $datosCalculados = $this->calcularPagosAgricultor($agricultor->id, $fechaInicio, $fechaFin);
        
        // Generar CSV
        $filename = "mis_pagos_" . str_replace(' ', '_', $agricultor->name) . "_entrega_{$diaEntrega->format('d-m-Y')}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($datosCalculados, $agricultor, $fechaInicio, $fechaFin, $diaEntrega) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados del reporte
            fputcsv($file, ['Reporte de Pagos - ' . $agricultor->name]);
            fputcsv($file, ['Período de ventas: ' . $fechaInicio->format('d/m/Y') . ' - ' . $fechaFin->format('d/m/Y')]);
            fputcsv($file, ['Entrega en feria: ' . $diaEntrega->format('l, d/m/Y')]);
            fputcsv($file, []);
            fputcsv($file, ['Producto', 'Cantidad', 'Precio Promedio', 'Pedidos', 'Total']);
            
            foreach($datosCalculados['pagos'] as $pago) {
                fputcsv($file, [
                    $pago['producto']->nombre,
                    $pago['cantidad'],
                    number_format($pago['precio_promedio'], 2),
                    $pago['pedidos_count'],
                    number_format($pago['monto'], 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['TOTAL A COBRAR:', '', '', '', number_format($datosCalculados['totalPagar'], 2)]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
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
            ->where('estado', 'armado')
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