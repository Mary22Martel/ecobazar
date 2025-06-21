<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Categoria;
use App\Models\Zone;
use App\Models\Mercado;
use App\Models\Medida;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->checkExpiredOrders();
    }

    private function checkExpiredOrders()
    {
        try {
            $expiredOrders = Order::where('estado', 'pendiente')
                ->where('expires_at', '<=', Carbon::now('America/Lima'))
                ->where('stock_reserved', true)
                ->get();

            foreach ($expiredOrders as $order) {
                foreach ($order->items as $item) {
                    $item->product->increment('cantidad_disponible', $item->cantidad);
                }
                
                $order->update([
                    'estado' => 'expirado',
                    'stock_reserved' => false
                ]);
                
                Log::info("Pedido #{$order->id} expirado automáticamente desde AdminController");
            }
            
        } catch (\Exception $e) {
            Log::error('Error verificando pedidos expirados desde Admin: ' . $e->getMessage());
        }
    }

    // ==================== LÓGICA DE SEMANA DE FERIA ====================
    
    /**
     * Calcula la semana de feria: domingo a viernes (ventas) + sábado (entrega)
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

  
public function index()
{
    $this->authorizeRoles(['admin']);
    
    // ========== CÁLCULO DE SEMANA DE FERIA ACTUAL ==========
    $semanaActual = $this->calcularSemanaFeria();
    $inicioSemana = $semanaActual['inicio_ventas'];  // Domingo
    $finSemana = $semanaActual['fin_ventas'];        // Viernes
    $diaEntrega = $semanaActual['dia_entrega'];      // Sábado
    
    // ========== RESUMEN DE PEDIDOS PARA ESTA SEMANA ==========
    
    // Pedidos de esta semana (domingo a viernes)
    $pedidosSemana = Order::whereBetween('created_at', [
                        $inicioSemana->startOfDay(), 
                        $finSemana->endOfDay()
                    ])
                    ->where('estado', '!=', 'expirado')
                    ->count();
    
    // Pedidos pendientes/pagados (necesitan preparación)
    $pedidosPendientesSemana = Order::whereBetween('created_at', [
                                $inicioSemana->startOfDay(), 
                                $finSemana->endOfDay()
                            ])
                            ->whereIn('estado', ['pendiente', 'pagado'])
                            ->count();
    
    // Pedidos listos (necesitan ser armados por admin)
    $pedidosListosSemana = Order::whereBetween('created_at', [
                            $inicioSemana->startOfDay(), 
                            $finSemana->endOfDay()
                        ])
                        ->where('estado', 'listo')
                        ->count();
    
    // Pedidos armados (listos para entrega en sábado)
    $pedidosArmadosSemana = Order::whereBetween('created_at', [
                            $inicioSemana->startOfDay(), 
                            $finSemana->endOfDay()
                        ])
                        ->where('estado', 'armado')
                        ->count();
    
    // Pedidos expirados de esta semana
    $pedidosExpiradosSemana = Order::whereBetween('created_at', [
                                $inicioSemana->startOfDay(), 
                                $finSemana->endOfDay()
                            ])
                            ->where('estado', 'expirado')
                            ->count();
    
    // ========== VENTAS Y ESTADÍSTICAS DE LA SEMANA ==========
    
    // Ventas totales de la semana (solo pedidos armados = completados)
    $ventasSemanaActual = Order::whereBetween('created_at', [
                            $inicioSemana->startOfDay(), 
                            $finSemana->endOfDay()
                        ])
                        ->where('estado', 'armado')
                        ->sum('total');
    
    // Ventas potenciales (todos los pedidos no cancelados ni expirados)
    $ventasPotencialesSemana = Order::whereBetween('created_at', [
                                $inicioSemana->startOfDay(), 
                                $finSemana->endOfDay()
                            ])
                            ->whereNotIn('estado', ['cancelado', 'expirado'])
                            ->sum('total');
    
    // Agricultores activos esta semana (con ventas)
    $agricultoresActivosSemana = User::where('role', 'agricultor')
                                   ->whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                                       $query->whereBetween('created_at', [
                                           $inicioSemana->startOfDay(), 
                                           $finSemana->endOfDay()
                                       ])
                                       ->whereNotIn('estado', ['cancelado', 'expirado']);
                                   })
                                   ->count();
    
    // Productos vendidos esta semana (cantidad total)
    $productosVendidosSemana = \App\Models\OrderItem::whereHas('order', function($query) use ($inicioSemana, $finSemana) {
                                    $query->whereBetween('created_at', [
                                        $inicioSemana->startOfDay(), 
                                        $finSemana->endOfDay()
                                    ])
                                    ->where('estado', 'armado');
                                })
                                ->sum('cantidad');
    
    // Clientes únicos esta semana
    $clientesUnicosSemana = Order::whereBetween('created_at', [
                            $inicioSemana->startOfDay(), 
                            $finSemana->endOfDay()
                        ])
                        ->whereNotIn('estado', ['cancelado', 'expirado'])
                        ->distinct('user_id')
                        ->count('user_id');
    
    // ========== RESUMEN GLOBALES DEL SISTEMA ==========
    
    // Pedidos globales (todos los tiempos, excluyendo expirados)
    $totalPedidosGlobal = Order::where('estado', '!=', 'expirado')->count();
    
    // Ventas globales (solo pedidos completados)
    $totalVentasGlobal = Order::where('estado', 'armado')->sum('total');
    
    // Total de agricultores en el sistema
    $totalAgricultores = User::where('role', 'agricultor')->count();
    
    // Total de productos en el catálogo
    $totalProductos = Product::count();
    
    // Total de clientes registrados
    $totalClientes = User::where('role', 'cliente')->count();
    
    // Promedio de venta por pedido (global)
    $promedioVentaPorPedido = $totalPedidosGlobal > 0 ? $totalVentasGlobal / $totalPedidosGlobal : 0;
    
    // ========== ESTADÍSTICAS ADICIONALES ==========
    
    // Tasa de conversión de la semana (armados vs total)
    $tasaConversionSemana = $pedidosSemana > 0 ? ($pedidosArmadosSemana / $pedidosSemana) * 100 : 0;
    
    // Valor promedio por pedido esta semana
    $promedioVentaSemana = $pedidosArmadosSemana > 0 ? $ventasSemanaActual / $pedidosArmadosSemana : 0;
    
    // Top categoría de la semana
    $topCategoriaSemana = \App\Models\Categoria::whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                            $query->whereBetween('created_at', [
                                $inicioSemana->startOfDay(), 
                                $finSemana->endOfDay()
                            ])
                            ->where('estado', 'armado');
                        })
                        ->withCount(['productos as items_vendidos' => function($query) use ($inicioSemana, $finSemana) {
                            $query->whereHas('orderItems.order', function($subQuery) use ($inicioSemana, $finSemana) {
                                $subQuery->whereBetween('created_at', [
                                    $inicioSemana->startOfDay(), 
                                    $finSemana->endOfDay()
                                ])
                                ->where('estado', 'armado');
                            });
                        }])
                        ->orderByDesc('items_vendidos')
                        ->first();
    
    // ========== CONFIGURACIONES DEL SISTEMA ==========
    $totalZonas = Zone::count();
    $totalCategorias = Categoria::count();
    $totalMedidas = Medida::count();
    $totalMercados = Mercado::count();
    
    // ========== ALERTAS Y NOTIFICACIONES ==========
    
    // Pedidos que necesitan atención inmediata
    $pedidosUrgentes = Order::where('estado', 'listo')
                       ->whereBetween('created_at', [
                           $inicioSemana->startOfDay(), 
                           $finSemana->endOfDay()
                       ])
                       ->count();
    
    // Productos con stock bajo (menos de 5 unidades)
    $productosStockBajo = Product::where('cantidad_disponible', '<', 5)
                                ->where('cantidad_disponible', '>', 0)
                                ->count();
    
    // Productos sin stock
    $productosSinStock = Product::where('cantidad_disponible', 0)->count();
    
    // ========== VARIABLES DE COMPATIBILIDAD ==========
    // ESTAS SON LAS QUE FALTABAN Y CAUSABAN EL ERROR
    $pedidosHoy = $pedidosSemana;           // Cambiado a semana
    $pedidosPendientes = $pedidosPendientesSemana;
    $pedidosListos = $pedidosListosSemana;
    $pedidosArmados = $pedidosArmadosSemana;
    $ventasSemana = $ventasSemanaActual;
    $agricultoresActivos = $agricultoresActivosSemana;
    
    return view('admin.dashboard', compact(
        // Información de la semana de feria
        'inicioSemana',
        'finSemana',
        'diaEntrega',
        
        // Resumen de pedidos para esta semana
        'pedidosSemana',
        'pedidosPendientesSemana', 
        'pedidosListosSemana',
        'pedidosArmadosSemana',
        'pedidosExpiradosSemana',
        
        // Ventas y estadísticas de la semana
        'ventasSemanaActual',
        'ventasPotencialesSemana',
        'agricultoresActivosSemana',
        'productosVendidosSemana',
        'clientesUnicosSemana',
        'tasaConversionSemana',
        'promedioVentaSemana',
        'topCategoriaSemana',
        
        // Totales globales del sistema
        'totalPedidosGlobal',
        'totalVentasGlobal',
        'totalAgricultores',
        'totalProductos',
        'totalClientes',
        'promedioVentaPorPedido',
        
        // Configuraciones del sistema
        'totalZonas',
        'totalCategorias',
        'totalMedidas',
        'totalMercados',
        
        // Alertas y notificaciones
        'pedidosUrgentes',
        'productosStockBajo',
        'productosSinStock',
        
        // Variables compatibles con vista actual
        'pedidosHoy',
        'pedidosPendientes',
        'pedidosListos',
        'pedidosArmados',
        'ventasSemana',
        'agricultoresActivos'
    ));
}

    // ==================== GESTIÓN DE PEDIDOS ====================
    
    public function pedidos(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Consulta base con filtro de semana
        $query = Order::with(['user', 'items.product.user'])
                    ->where('estado', '!=', 'expirado')
                    ->whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc');
        
        $pedidos = $query->paginate(20);
        
        // Estadísticas de la semana
        $estadisticasSemana = $this->calcularEstadisticasPedidosSemana($inicioSemana, $finSemana);
        
        return view('admin.pedidos.index', compact(
            'pedidos',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'estadisticasSemana'
        ));
    }

    public function detallePedido($id)
    {
        $this->authorizeRoles(['admin']);
        
        $order = Order::with(['user', 'items.product.user'])->findOrFail($id);
        
        return view('admin.pedidos.show', compact('order'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $this->authorizeRoles(['admin']);
        
        $order = Order::findOrFail($id);
        $oldEstado = $order->estado;
        $order->estado = $request->estado;
        $order->save();
        
        $estadoTexto = [
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'listo' => 'Listo',
            'armado' => 'Armado',
            'en_entrega' => 'En Entrega',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];
        
        $mensaje = "Pedido #{$order->id} cambiado de '{$estadoTexto[$oldEstado]}' a '{$estadoTexto[$request->estado]}'";
        
        return back()->with('success', $mensaje);
    }

    public function pedidosPagados(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Consulta con filtros
        $pedidos = Order::with(['user', 'items.product.user'])
                    ->where('estado', 'pagado')
                    ->whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        
        // Estadísticas de la semana
        $estadisticasSemana = $this->calcularEstadisticasPedidosSemana($inicioSemana, $finSemana);
        
        return view('admin.pedidos.pagados', compact(
            'pedidos',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'estadisticasSemana'
        ));
    }


    public function pedidosListos(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Consulta con filtros
        $pedidos = Order::with(['user', 'items.product.user'])
                    ->where('estado', 'listo')
                    ->whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        
        // Estadísticas de la semana
        $estadisticasSemana = $this->calcularEstadisticasPedidosSemana($inicioSemana, $finSemana);
        
        return view('admin.pedidos.listos', compact(
            'pedidos',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'estadisticasSemana'
        ));
    }

    public function pedidosArmados(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Consulta con filtros
        $pedidos = Order::with(['user', 'items.product.user'])
                    ->where('estado', 'armado')
                    ->whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        
        // Estadísticas de la semana
        $estadisticasSemana = $this->calcularEstadisticasPedidosSemana($inicioSemana, $finSemana);
        
        return view('admin.pedidos.armados', compact(
            'pedidos',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'estadisticasSemana'
        ));
    }

   public function pedidosExpirados(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // Consulta con filtros
        $pedidos = Order::with(['user', 'items.product.user'])
                    ->where('estado', 'expirado')
                    ->whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->orderBy('updated_at', 'desc')
                    ->paginate(20);
        
        // Estadísticas de la semana
        $estadisticasSemana = $this->calcularEstadisticasPedidosSemana($inicioSemana, $finSemana);
        
        return view('admin.pedidos.expirados', compact(
            'pedidos',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'estadisticasSemana'
        ));
    }

    private function calcularEstadisticasPedidosSemana($inicioSemana, $finSemana)
    {
        // Total de pedidos de la semana (excluyendo expirados)
        $total = Order::whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->where('estado', '!=', 'expirado')
                    ->count();
        
        // Pedidos pendientes (pendiente + pagado)
        $pendientes = Order::whereBetween('created_at', [
                            $inicioSemana->startOfDay(),
                            $finSemana->endOfDay()
                        ])
                        ->whereIn('estado', ['pendiente', 'pagado'])
                        ->count();
        
        // Pedidos listos
        $listos = Order::whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->where('estado', 'listo')
                    ->count();
        
        // Pedidos armados
        $armados = Order::whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->where('estado', 'armado')
                    ->count();
        
        // Ventas completadas (solo armados)
        $ventas = Order::whereBetween('created_at', [
                        $inicioSemana->startOfDay(),
                        $finSemana->endOfDay()
                    ])
                    ->where('estado', 'armado')
                    ->sum('total');
        
        return [
            'total' => $total,
            'pendientes' => $pendientes,
            'listos' => $listos,
            'armados' => $armados,
            'ventas' => $ventas
        ];
    }

    // ==================== GESTIÓN DE PAGOS ====================

    public function pagosAgricultores(Request $request)
{
    $this->authorizeRoles(['admin']);
    
    // Obtener la semana seleccionada (por defecto la actual = 0)
    $semanaSeleccionada = $request->get('semana', 0);
    
    // Calcular fechas de la semana de feria usando el método existente
    $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
    $inicioSemana = $semanaFeria['inicio_ventas'];      // Domingo
    $finSemana = $semanaFeria['fin_ventas'];            // Viernes  
    $diaEntrega = $semanaFeria['dia_entrega'];          // Sábado
    
    // Obtener pagos de agricultores de esa semana usando el método existente
    $pagosAgricultores = $this->calcularPagosSemanales($inicioSemana, $finSemana);
    
    // Generar opciones de semanas usando el método existente
    $opcionesSemanas = $this->generarOpcionesSemanasFeria();
    
    // Estadísticas de la semana usando el método existente
    $estadisticas = $this->calcularEstadisticasSemana($inicioSemana, $finSemana);
    
    // ========== VARIABLES ADICIONALES QUE NECESITA LA VISTA ==========
    
    // Calcular totales generales
    $totalPagar = $pagosAgricultores->sum('total_pago');
    $totalPedidos = $pagosAgricultores->sum('pedidos_atendidos');
    $totalCantidad = $pagosAgricultores->sum('total_productos');
    $totalProductos = $pagosAgricultores->count(); // Tipos de productos diferentes
    
    // Obtener todos los pedidos de la semana para estadísticas detalladas
    $pedidosSemana = Order::with(['items.product.user', 'items.product.medida'])
        ->whereBetween('created_at', [
            $inicioSemana->startOfDay(), 
            $finSemana->endOfDay()
        ])
        ->where('estado', 'armado')
        ->get();
    
    // Estadísticas por estado (todos los estados de pedidos en esa semana) - CORREGIDO
    $estadisticasEstado = [
        'pagado' => [
            'count' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                           ->where('estado', 'pagado')->count(),
            'monto' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                           ->where('estado', 'pagado')->sum('total')
        ],
        'listo' => [
            'count' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                           ->where('estado', 'listo')->count(),
            'monto' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                           ->where('estado', 'listo')->sum('total')
        ],
        'armado' => [
            'count' => $pedidosSemana->count(),
            'monto' => $pedidosSemana->sum('total')
        ],
        'entregado' => [
            'count' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                            ->where('estado', 'entregado')->count(),
            'monto' => Order::whereBetween('created_at', [$inicioSemana->startOfDay(), $finSemana->endOfDay()])
                            ->where('estado', 'entregado')->sum('total')
        ]
    ];
    
    // Ventas por día de la semana (domingo a viernes)
    $ventasPorDia = [];
    $diasSemana = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    foreach($diasSemana as $dia) {
        $pedidosDia = $pedidosSemana->filter(function($pedido) use ($dia) {
            return $pedido->created_at->format('l') === $dia;
        });
        
        $ventasPorDia[$dia] = [
            'pedidos' => $pedidosDia->count(),
            'monto' => $pedidosDia->sum('total'),
            'cantidad' => $pedidosDia->sum(function($pedido) {
                return $pedido->items->sum('cantidad');
            })
        ];
    }
    
    // Top productos más vendidos (corregido para evitar error de Collection)
    $productosVendidos = [];
    
    foreach($pedidosSemana as $pedido) {
        foreach($pedido->items as $item) {
            $key = $item->product->id;
            if (isset($productosVendidos[$key])) {
                $productosVendidos[$key]['cantidad'] += $item->cantidad;
                $productosVendidos[$key]['monto'] += $item->cantidad * $item->precio;
                $productosVendidos[$key]['pedidos_count']++;
            } else {
                $productosVendidos[$key] = [
                    'producto' => $item->product,
                    'cantidad' => $item->cantidad,
                    'monto' => $item->cantidad * $item->precio,
                    'pedidos_count' => 1,
                    'precio_promedio' => $item->precio
                ];
            }
        }
    }
    
    // Convertir a Collection y ordenar (top 5)
    $topProductos = collect($productosVendidos)->sortByDesc('monto')->take(5);
    
    // USAR LOS NOMBRES CORRECTOS QUE ESPERA LA VISTA
    $fechaInicio = $inicioSemana;  // Para compatibilidad con otros métodos
    $fechaFin = $finSemana;        // Para compatibilidad con otros métodos
    $pagos = $topProductos;        // Para compatibilidad con la vista
    
    return view('admin.pagos.agricultores', compact(
        'pagosAgricultores',
        'opcionesSemanas', 
        'semanaSeleccionada',
        'estadisticas',
        'inicioSemana',        // NOMBRE CORRECTO PARA LA VISTA
        'finSemana',           // NOMBRE CORRECTO PARA LA VISTA
        'fechaInicio',         // Para compatibilidad
        'fechaFin',            // Para compatibilidad
        'diaEntrega',
        'totalPagar',
        'totalPedidos',
        'totalProductos',
        'totalCantidad',
        'estadisticasEstado',
        'ventasPorDia',
        'topProductos',
        'pagos'
    ));
}

    private function calcularPagosSemanales($inicioSemana, $finSemana)
    {
        return User::where('role', 'agricultor')
            ->whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [
                          $inicioSemana->startOfDay(), 
                          $finSemana->endOfDay()
                      ]);
            })
            ->with(['productos.orderItems' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                });
            }])
            ->get()
            ->map(function($agricultor) use ($inicioSemana, $finSemana) {
                $totalPago = 0;
                $totalProductos = 0;
                $pedidosAtendidos = collect();
                
                foreach($agricultor->productos as $producto) {
                    foreach($producto->orderItems as $item) {
                        if ($item->order->estado === 'armado' && 
                            $item->order->created_at >= $inicioSemana->startOfDay() && 
                            $item->order->created_at <= $finSemana->endOfDay()) {
                            
                            $totalPago += $item->cantidad * $item->precio;
                            $totalProductos += $item->cantidad;
                            $pedidosAtendidos->push($item->order->id);
                        }
                    }
                }
                
                return [
                    'agricultor' => $agricultor,
                    'total_pago' => $totalPago,
                    'total_productos' => $totalProductos,
                    'pedidos_atendidos' => $pedidosAtendidos->unique()->count(),
                    'productos_vendidos' => $agricultor->productos->filter(function($producto) use ($inicioSemana, $finSemana) {
                        return $producto->orderItems->filter(function($item) use ($inicioSemana, $finSemana) {
                            return $item->order->estado === 'armado' && 
                                   $item->order->created_at >= $inicioSemana->startOfDay() && 
                                   $item->order->created_at <= $finSemana->endOfDay();
                        })->count() > 0;
                    })
                ];
            })
            ->filter(function($agricultor) {
                return $agricultor['total_pago'] > 0;
            })
            ->sortByDesc('total_pago');
    }

    private function calcularEstadisticasSemana($inicioSemana, $finSemana)
    {
        $pedidosArmados = Order::where('estado', 'armado')
                              ->whereBetween('created_at', [
                                  $inicioSemana->startOfDay(), 
                                  $finSemana->endOfDay()
                              ])
                              ->get();
        
        $totalVentas = $pedidosArmados->sum('total');
        $totalPedidos = $pedidosArmados->count();
        
        $agricultoresActivos = User::where('role', 'agricultor')
            ->whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [
                          $inicioSemana->startOfDay(), 
                          $finSemana->endOfDay()
                      ]);
            })
            ->count();
        
        $productosVendidos = $pedidosArmados->sum(function($pedido) {
            return $pedido->items->sum('cantidad');
        });
        
        return [
            'total_ventas' => $totalVentas,
            'total_pedidos' => $totalPedidos,
            'agricultores_activos' => $agricultoresActivos,
            'productos_vendidos' => $productosVendidos,
            'promedio_por_pedido' => $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0
        ];
    }

    public function detallePagoAgricultor(Request $request, $agricultorId)
    {
        $this->authorizeRoles(['admin']);
        
        $semanaSeleccionada = $request->get('semana', 0);
        
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        
        $agricultor = User::where('role', 'agricultor')
            ->with(['productos.orderItems' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                })->with(['order', 'product']);
            }])
            ->findOrFail($agricultorId);
        
        // Calcular detalles del pago
        $detallesPago = [];
        $totalPago = 0;
        
        foreach($agricultor->productos as $producto) {
            foreach($producto->orderItems as $item) {
                $subtotal = $item->cantidad * $item->precio;
                $totalPago += $subtotal;
                
                $detallesPago[] = [
                    'pedido_id' => $item->order->id,
                    'producto' => $producto->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio,
                    'subtotal' => $subtotal,
                    'fecha_pedido' => $item->order->created_at,
                    'cliente' => $item->order->nombre . ' ' . $item->order->apellido
                ];
            }
        }
        
        return view('admin.pagos.detalle-agricultor', compact(
            'agricultor',
            'detallesPago',
            'totalPago',
            'inicioSemana',
            'finSemana',
            'semanaSeleccionada'
        ));
    }

    public function exportarPagos(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $semanaSeleccionada = $request->get('semana', 0);
        
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        $pagosAgricultores = $this->calcularPagosSemanales($inicioSemana, $finSemana);
        
        // Crear CSV
        $nombreArchivo = "pagos_agricultores_entrega_{$diaEntrega->format('d-m-Y')}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ];
        
        $callback = function() use ($pagosAgricultores) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, ['Agricultor', 'Teléfono', 'Productos Vendidos', 'Pedidos Atendidos', 'Total a Pagar']);
            
            // Datos
            foreach($pagosAgricultores as $pago) {
                fputcsv($file, [
                    $pago['agricultor']->name,
                    $pago['agricultor']->telefono ?? 'N/A',
                    $pago['total_productos'],
                    $pago['pedidos_atendidos'],
                    'S/ ' . number_format($pago['total_pago'], 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // ==================== REPORTES SEMANALES ====================

    public function reportesSemanales(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Datos para los reportes
        $reporteVentas = $this->generarReporteVentas($inicioSemana, $finSemana);
        $reporteProductos = $this->generarReporteProductos($inicioSemana, $finSemana);
        $reporteAgricultores = $this->generarReporteAgricultores($inicioSemana, $finSemana);
        
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        $estadisticas = $this->calcularEstadisticasSemana($inicioSemana, $finSemana);
        
        return view('admin.reportes.semanales', compact(
            'reporteVentas',
            'reporteProductos', 
            'reporteAgricultores',
            'opcionesSemanas',
            'semanaSeleccionada',
            'estadisticas',
            'inicioSemana',
            'finSemana',
            'diaEntrega'
        ));
    }

    private function generarReporteVentas($inicioSemana, $finSemana)
    {
        $pedidosPorDia = Order::where('estado', 'armado')
            ->whereBetween('created_at', [
                $inicioSemana->startOfDay(), 
                $finSemana->endOfDay()
            ])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as pedidos, SUM(total) as ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        
        $ventasPorTipo = Order::where('estado', 'armado')
            ->whereBetween('created_at', [
                $inicioSemana->startOfDay(), 
                $finSemana->endOfDay()
            ])
            ->selectRaw('delivery, COUNT(*) as pedidos, SUM(total) as ventas')
            ->groupBy('delivery')
            ->get();
        
        return [
            'por_dia' => $pedidosPorDia,
            'por_tipo' => $ventasPorTipo
        ];
    }

    private function generarReporteProductos($inicioSemana, $finSemana)
    {
        $productosTop = Product::whereHas('orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [
                          $inicioSemana->startOfDay(), 
                          $finSemana->endOfDay()
                      ]);
            })
            ->withSum(['orderItems' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                });
            }], 'cantidad')
            ->with(['categoria', 'medida', 'user'])
            ->get()
            ->sortByDesc('order_items_sum_cantidad')
            ->take(10);
        
        $categorias = Categoria::whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [
                          $inicioSemana->startOfDay(), 
                          $finSemana->endOfDay()
                      ]);
            })
            ->withCount(['productos as ventas_count' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('orderItems.order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                });
            }])
            ->get()
            ->sortByDesc('ventas_count');
        
        return [
            'productos_top' => $productosTop,
            'categorias' => $categorias
        ];
    }

    private function generarReporteAgricultores($inicioSemana, $finSemana)
    {
        return User::where('role', 'agricultor')
            ->whereHas('productos.orderItems.order', function($query) use ($inicioSemana, $finSemana) {
                $query->where('estado', 'armado')
                      ->whereBetween('created_at', [
                          $inicioSemana->startOfDay(), 
                          $finSemana->endOfDay()
                      ]);
            })
            ->withCount(['productos as pedidos_count' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('orderItems.order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                });
            }])
            ->with(['productos' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereHas('orderItems.order', function($subQuery) use ($inicioSemana, $finSemana) {
                    $subQuery->where('estado', 'armado')
                             ->whereBetween('created_at', [
                                 $inicioSemana->startOfDay(), 
                                 $finSemana->endOfDay()
                             ]);
                })->with(['orderItems' => function($itemQuery) use ($inicioSemana, $finSemana) {
                    $itemQuery->whereHas('order', function($orderQuery) use ($inicioSemana, $finSemana) {
                        $orderQuery->where('estado', 'armado')
                                 ->whereBetween('created_at', [
                                     $inicioSemana->startOfDay(), 
                                     $finSemana->endOfDay()
                                 ]);
                    });
                }]);
            }])
            ->get()
            ->map(function($agricultor) {
                $totalVentas = 0;
                $totalProductos = 0;
                
                foreach($agricultor->productos as $producto) {
                    foreach($producto->orderItems as $item) {
                        $totalVentas += $item->cantidad * $item->precio;
                        $totalProductos += $item->cantidad;
                    }
                }
                
                $agricultor->total_ventas = $totalVentas;
                $agricultor->total_productos = $totalProductos;
                
                return $agricultor;
            })
            ->sortByDesc('total_ventas')
            ->take(10);
    }

    // ==================== GESTIÓN DE ZONAS ====================
    
    public function zonas()
    {
        $this->authorizeRoles(['admin']);
        
        $zonas = \App\Models\Zone::orderBy('name')->paginate(15);
        
        return view('admin.configuracion.zonas', compact('zonas'));
    }
    
    public function crearZona()
    {
        $this->authorizeRoles(['admin']);
        
        return view('admin.configuracion.zona-crear');
    }
    
    public function guardarZona(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:zones,name',
            'delivery_cost' => 'required|numeric|min:0'
        ]);
        
        \App\Models\Zone::create([
            'name' => $request->name,
            'delivery_cost' => $request->delivery_cost,
            'active' => $request->has('active')
        ]);
        
        return redirect()->route('admin.configuracion.zonas')->with('success', 'Zona creada exitosamente');
    }
    
    public function editarZona($id)
    {
        $this->authorizeRoles(['admin']);
        
        $zona = \App\Models\Zone::findOrFail($id);
        
        return view('admin.configuracion.zona-editar', compact('zona'));
    }
    
    public function actualizarZona(Request $request, $id)
    {
        $this->authorizeRoles(['admin']);
        
        $zona = \App\Models\Zone::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:zones,name,' . $id,
            'delivery_cost' => 'required|numeric|min:0'
        ]);
        
        $zona->update([
            'name' => $request->name,
            'delivery_cost' => $request->delivery_cost,
            'active' => $request->has('active')
        ]);
        
        return redirect()->route('admin.configuracion.zonas')->with('success', 'Zona actualizada exitosamente');
    }
    
    public function eliminarZona($id)
    {
        $this->authorizeRoles(['admin']);
        
        $zona = \App\Models\Zone::findOrFail($id);
        
        // Verificar si la zona está siendo usada
        $enUso = \App\Models\Order::where('distrito', $zona->name)->exists();
        
        if ($enUso) {
            return back()->with('error', 'No se puede eliminar la zona porque tiene pedidos asociados');
        }
        
        $zona->delete();
        
        return redirect()->route('admin.configuracion.zonas')->with('success', 'Zona eliminada exitosamente');
    }

    // ==================== GESTIÓN DE CATEGORÍAS ====================

    public function categorias()
    {
        $this->authorizeRoles(['admin']);
        
        try {
            $categorias = \App\Models\Categoria::orderBy('nombre')->paginate(15);
            return view('admin.configuracion.categorias', compact('categorias'));
        } catch (\Exception $e) {
            // Si falla paginate, usar get() como fallback
            $categorias = \App\Models\Categoria::orderBy('nombre')->get();
            return view('admin.configuracion.categorias', compact('categorias'));
        }
    }

    public function crearCategoria()
    {
        $this->authorizeRoles(['admin']);
        
        return view('admin.configuracion.categoria-crear');
    }

    public function guardarCategoria(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'description' => 'nullable|string|max:500'
        ]);
        
        \App\Models\Categoria::create([
            'nombre' => $request->nombre,
            'description' => $request->description,
            'active' => $request->has('active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.configuracion.categorias')->with('success', 'Categoría creada exitosamente');
    }

    public function editarCategoria($id)
    {
        $this->authorizeRoles(['admin']);
        
        $categoria = \App\Models\Categoria::findOrFail($id);
        
        return view('admin.configuracion.categoria-editar', compact('categoria'));
    }

    public function actualizarCategoria(Request $request, $id)
    {
        $this->authorizeRoles(['admin']);
        
        $categoria = \App\Models\Categoria::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $id,
            'description' => 'nullable|string|max:500'
        ]);
        
        $categoria->update([
            'nombre' => $request->nombre,
            'description' => $request->description,
            'active' => $request->has('active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.configuracion.categorias')->with('success', 'Categoría actualizada exitosamente');
    }

    public function eliminarCategoria($id)
    {
        $this->authorizeRoles(['admin']);
        
        $categoria = \App\Models\Categoria::findOrFail($id);
        
        // Verificar si la categoría está siendo usada
        $enUso = \App\Models\Product::where('categoria_id', $id)->exists();
        
        if ($enUso) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }
        
        $categoria->delete();
        
        return redirect()->route('admin.configuracion.categorias')->with('success', 'Categoría eliminada exitosamente');
    }

    // ==================== GESTIÓN DE MEDIDAS ====================

    public function medidas()
    {
        $this->authorizeRoles(['admin']);
        
        try {
            $medidas = \App\Models\Medida::orderBy('nombre')->paginate(15);
            return view('admin.configuracion.medidas', compact('medidas'));
        } catch (\Exception $e) {
            // Si falla paginate, usar get() como fallback
            $medidas = \App\Models\Medida::orderBy('nombre')->get();
            return view('admin.configuracion.medidas', compact('medidas'));
        }
    }

    public function crearMedida()
    {
        $this->authorizeRoles(['admin']);
        
        return view('admin.configuracion.medida-crear');
    }

    public function guardarMedida(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $request->validate([
            'nombre' => 'required|string|max:255|unique:medidas,nombre',
            'simbolo' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500'
        ]);
        
        \App\Models\Medida::create([
            'nombre' => $request->nombre,
            'simbolo' => $request->simbolo,
            'description' => $request->description,
            'active' => $request->has('active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.configuracion.medidas')->with('success', 'Medida creada exitosamente');
    }

    public function editarMedida($id)
    {
        $this->authorizeRoles(['admin']);
        
        $medida = \App\Models\Medida::findOrFail($id);
        
        return view('admin.configuracion.medida-editar', compact('medida'));
    }

    public function actualizarMedida(Request $request, $id)
    {
        $this->authorizeRoles(['admin']);
        
        $medida = \App\Models\Medida::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255|unique:medidas,nombre,' . $id,
            'simbolo' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500'
        ]);
        
        $medida->update([
            'nombre' => $request->nombre,
            'simbolo' => $request->simbolo,
            'description' => $request->description,
            'active' => $request->has('active') ? 1 : 0
        ]);
        
        return redirect()->route('admin.configuracion.medidas')->with('success', 'Medida actualizada exitosamente');
    }

    public function eliminarMedida($id)
    {
        $this->authorizeRoles(['admin']);
        
        $medida = \App\Models\Medida::findOrFail($id);
        
        // Verificar si la medida está siendo usada
        $enUso = \App\Models\Product::where('medida_id', $id)->exists();
        
        if ($enUso) {
            return back()->with('error', 'No se puede eliminar la medida porque tiene productos asociados');
        }
        
        $medida->delete();
        
        return redirect()->route('admin.configuracion.medidas')->with('success', 'Medida eliminada exitosamente');
    }

    // ==================== GESTIÓN DE MERCADOS ====================
    
    public function mercados()
    {
        $this->authorizeRoles(['admin']);
        
        $mercados = \App\Models\Mercado::orderBy('name')->paginate(15);
        
        return view('admin.configuracion.mercados', compact('mercados'));
    }

    // ==================== MÉTODO DE AUTORIZACIÓN ====================
    
    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }

    // En AdminController.php, agrega:
    public function reportePagos(Request $request)
    {
        return $this->reportesSemanales($request);
    }
}