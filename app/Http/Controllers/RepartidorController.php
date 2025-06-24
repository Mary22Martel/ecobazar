<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorizeRoles(['repartidor']);
        
        // ========== USAR LA MISMA LÓGICA DE SEMANAS QUE EL AGRICULTOR ==========
        $semanaActual = $this->calcularSemanaFeria();
        $inicioSemana = $semanaActual['inicio_ventas'];  // Domingo
        $finSemana = $semanaActual['fin_ventas'];        // Viernes
        $diaEntrega = $semanaActual['dia_entrega'];      // Sábado
        
        $repartidorId = Auth::id();
        $repartidor = Auth::user();
        
        // ========== DEBUG: LOG PARA VERIFICAR DATOS ==========
        Log::info("Dashboard Repartidor - ID: {$repartidorId}");
        Log::info("Semana: {$inicioSemana->format('Y-m-d')} a {$finSemana->format('Y-m-d')}");
        Log::info("Día entrega: {$diaEntrega->format('Y-m-d')}");
        
        // ========== OBTENER ZONAS ASIGNADAS AL REPARTIDOR PARA ESTA SEMANA ESPECÍFICA ==========
        $zonasAsignadas = $repartidor->zones()
                                    ->wherePivot('fecha_asignacion', $diaEntrega->toDateString())
                                    ->get();
        
        Log::info("Zonas asignadas: " . implode(', ', $zonasAsignadas->pluck('name')->toArray()));
        
        // ========== CONTAR TODOS LOS PEDIDOS DEL REPARTIDOR EN ESTA SEMANA ==========
        $queryBaseTodos = Order::where('repartidor_id', $repartidorId)
                               ->whereBetween('created_at', [
                                   $inicioSemana->startOfDay(), 
                                   $finSemana->endOfDay()
                               ]);
        
        // COPIA PARA DEBUG - Ver todos los pedidos sin filtro de zona
        $todosPedidosRepartidor = (clone $queryBaseTodos)->get();
        Log::info("Todos los pedidos del repartidor en esta semana: " . $todosPedidosRepartidor->count());
        
        foreach($todosPedidosRepartidor as $pedido) {
            Log::info("Pedido #{$pedido->id} - Estado: {$pedido->estado} - Distrito: {$pedido->distrito}");
        }
        
        // ========== APLICAR FILTRO DE ZONAS SOLO SI TIENE ZONAS ASIGNADAS ==========
        $queryBase = Order::where('repartidor_id', $repartidorId)
                          ->whereBetween('created_at', [
                              $inicioSemana->startOfDay(), 
                              $finSemana->endOfDay()
                          ]);
        
        // Si tiene zonas asignadas específicas, filtrar por ellas
        if ($zonasAsignadas->isNotEmpty()) {
            $zonasNombres = $zonasAsignadas->pluck('name')->toArray();
            Log::info("Filtrando por zonas: " . implode(', ', $zonasNombres));
            
            // CORREGIR: Usar directamente el campo 'distrito' del pedido
            $queryBase->whereIn('distrito', $zonasNombres);
        }
        
        // ========== CONTAR PEDIDOS POR ESTADO ==========
        
        // CORRECION: Entregas pendientes = pedidos que el admin ya asignó al repartidor (en_entrega)
        $entregasPendientes = (clone $queryBase)->where('estado', 'en_entrega')->count();
        
        // Entregas completadas
        $entregasCompletadas = (clone $queryBase)->where('estado', 'entregado')->count();
        
        // Total de entregas de esta semana (solo estados del repartidor)
        $entregasSemana = (clone $queryBase)->whereIn('estado', ['en_entrega', 'entregado'])->count();
        
        // Total histórico de entregas (todas las semanas)
        $totalEntregas = Order::where('repartidor_id', $repartidorId)
                              ->where('estado', 'entregado')
                              ->count();
        
        // ========== DEBUG: LOG DE RESULTADOS ==========
        Log::info("Pendientes (en_entrega): {$entregasPendientes}");
        Log::info("Completadas: {$entregasCompletadas}");
        Log::info("Total semana: {$entregasSemana}");
        
        // ========== ESTADÍSTICAS POR ZONA ==========
        $estadisticasPorZona = [];
        
        foreach ($zonasAsignadas as $zona) {
            $pedidosZona = Order::where('repartidor_id', $repartidorId)
                               ->where('distrito', $zona->name)
                               ->whereBetween('created_at', [
                                   $inicioSemana->startOfDay(), 
                                   $finSemana->endOfDay()
                               ]);
            
            $pendientesZona = (clone $pedidosZona)->where('estado', 'en_entrega')->count();
            $completadasZona = (clone $pedidosZona)->where('estado', 'entregado')->count();
            $totalZona = (clone $pedidosZona)->whereIn('estado', ['en_entrega', 'entregado'])->count();
            
            $estadisticasPorZona[] = [
                'zona' => $zona,
                'pendientes' => $pendientesZona,
                'completadas' => $completadasZona,
                'total' => $totalZona,
                'costo_envio' => $zona->delivery_cost
            ];
            
            Log::info("Zona {$zona->name}: Pendientes={$pendientesZona}, Completadas={$completadasZona}, Total={$totalZona}");
        }
        
        // ========== AJUSTAR: SOLO CONTAR PEDIDOS EN_ENTREGA COMO PENDIENTES ==========
        // El repartidor solo ve pedidos que el admin ya le asignó (en_entrega)
        
        return view('repartidor.dashboard', compact(
            'entregasPendientes',        // Solo 'armado'
            'entregasCompletadas', 
            'entregasSemana',
            'totalEntregas',
            'inicioSemana',
            'finSemana',
            'diaEntrega',
            'zonasAsignadas',
            'estadisticasPorZona'
        ));
    }

    public function pedidosPendientes(Request $request)
    {
        $this->authorizeRoles(['repartidor']);
        
        $repartidorId = Auth::id();
        $repartidor = Auth::user();
        
        // ========== MANEJO DE SEMANAS ==========
        $semanaSeleccionada = $request->get('semana', 0);
        $semanaData = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        
        $fechaInicio = $semanaData['inicio_ventas'];
        $fechaFin = $semanaData['fin_ventas'];
        $diaEntrega = $semanaData['dia_entrega'];
        
        // FILTRAR ZONAS POR FECHA DE ENTREGA
        $zonasAsignadas = $this->obtenerZonasAsignadasParaFecha($repartidor, $diaEntrega);
        
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        // ========== OBTENER PEDIDOS ==========
        $queryBase = Order::with(['user', 'items.product'])
                          ->where('repartidor_id', $repartidorId)
                          ->whereBetween('created_at', [
                              $fechaInicio->startOfDay(),
                              $fechaFin->endOfDay()
                          ]);
        
        // Filtrar por zonas si las tiene asignadas
        if ($zonasAsignadas->isNotEmpty()) {
            $zonasNombres = $zonasAsignadas->pluck('name')->toArray();
            $queryBase->whereIn('distrito', $zonasNombres);
        }
        
        // ========== CORREGIR: SOLO EN_ENTREGA COMO PENDIENTES ==========
        $pedidos = (clone $queryBase)->where('estado', 'en_entrega')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        
        // ========== ESTADÍSTICAS ==========
        $estadisticas = [
            'en_entrega' => [
                'count' => (clone $queryBase)->where('estado', 'en_entrega')->count(),
                'monto' => (clone $queryBase)->where('estado', 'en_entrega')->sum('total')
            ],
            'entregado' => [
                'count' => (clone $queryBase)->where('estado', 'entregado')->count(),
                'monto' => (clone $queryBase)->where('estado', 'entregado')->sum('total')
            ]
        ];
        
        return view('repartidor.pedidos_pendientes', compact(
            'pedidos',
            'estadisticas',
            'zonasAsignadas',
            'semanaSeleccionada',
            'opcionesSemanas',
            'fechaInicio',
            'fechaFin',
            'diaEntrega'
        ));
    }

    public function rutas(Request $request)
    {
        $this->authorizeRoles(['repartidor']);
        
        $repartidorId = Auth::id();
        $repartidor = Auth::user();
        
        $semanaSeleccionada = $request->get('semana', 0);
        $semanaData = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        
        $fechaInicio = $semanaData['inicio_ventas'];
        $fechaFin = $semanaData['fin_ventas'];
        $diaEntrega = $semanaData['dia_entrega'];
        
        $zonasAsignadas = $this->obtenerZonasAsignadasParaFecha($repartidor, $diaEntrega);
        
        $query = Order::with(['user'])
                      ->where('repartidor_id', $repartidorId)
                      ->where('estado', 'en_entrega') // Solo pedidos asignados al repartidor
                      ->whereBetween('created_at', [
                          $fechaInicio->startOfDay(),
                          $fechaFin->endOfDay()
                      ]);
        
        if ($zonasAsignadas->isNotEmpty()) {
            $zonasNombres = $zonasAsignadas->pluck('name')->toArray();
            $query->whereIn('distrito', $zonasNombres);
        }
        
        $pedidos = $query->get();
        
        // Agrupar por zona/distrito
        $pedidosPorZona = $pedidos->groupBy('distrito'); // CORREGIDO: usar 'distrito' directamente
        
        $totalEntregas = $pedidos->count();
        $zonas = $pedidosPorZona->count();
        
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('repartidor.rutas', compact(
            'pedidos', 
            'pedidosPorZona', 
            'totalEntregas', 
            'zonas',
            'zonasAsignadas',
            'fechaInicio',
            'fechaFin',
            'diaEntrega',
            'semanaSeleccionada',
            'opcionesSemanas'
        ));
    }

    public function entregasCompletadas(Request $request)
    {
        $this->authorizeRoles(['repartidor']);
        
        $repartidorId = Auth::id();
        $repartidor = Auth::user();
        
        $semanaSeleccionada = $request->get('semana', 0);
        $semanaData = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        
        $fechaInicio = $semanaData['inicio_ventas'];
        $fechaFin = $semanaData['fin_ventas'];
        $diaEntrega = $semanaData['dia_entrega'];
        
        $zonasAsignadas = $this->obtenerZonasAsignadasParaFecha($repartidor, $diaEntrega);
        
        $query = Order::with(['user', 'items.product'])
                      ->where('repartidor_id', $repartidorId)
                      ->where('estado', 'entregado')
                      ->whereBetween('created_at', [
                          $fechaInicio->startOfDay(),
                          $fechaFin->endOfDay()
                      ]);
        
        if ($zonasAsignadas->isNotEmpty()) {
            $zonasNombres = $zonasAsignadas->pluck('name')->toArray();
            $query->whereIn('distrito', $zonasNombres);
        }
        
        $pedidos = $query->orderBy('updated_at', 'desc')->paginate(15);
        
        // Estadísticas
        $queryStats = Order::where('repartidor_id', $repartidorId)->where('estado', 'entregado');
        
        if ($zonasAsignadas->isNotEmpty()) {
            $zonasNombres = $zonasAsignadas->pluck('name')->toArray();
            $queryStats->whereIn('distrito', $zonasNombres);
        }
        
        $totalEntregadas = (clone $queryStats)->count();
        $entregadasSemana = (clone $queryStats)
                           ->whereBetween('created_at', [
                               $fechaInicio->startOfDay(),
                               $fechaFin->endOfDay()
                           ])
                           ->count();
        $entregadasHoy = (clone $queryStats)
                        ->whereDate('updated_at', today())
                        ->count();
        
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('repartidor.entregas_completadas', compact(
            'pedidos',
            'totalEntregadas',
            'entregadasHoy',
            'entregadasSemana',
            'zonasAsignadas',
            'fechaInicio',
            'fechaFin',
            'diaEntrega',
            'semanaSeleccionada',
            'opcionesSemanas'
        ));
    }

    public function detallePedido($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        $pedido = Order::with(['items.product.user', 'user'])
                      ->where('repartidor_id', Auth::id())
                      ->findOrFail($id);
        
        return view('repartidor.pedido_detalle', compact('pedido'));
    }

    public function marcarComoEntregado($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        $pedido = Order::where('repartidor_id', Auth::id())
                      ->findOrFail($id);
        
        if (!in_array($pedido->estado, ['armado', 'en_entrega'])) {
            return redirect()->back()->with('error', 'El pedido no está en estado válido para ser entregado.');
        }

        $pedido->estado = 'entregado';
        $pedido->save();

        Log::info("Pedido #{$pedido->id} marcado como entregado por repartidor " . Auth::id());

        return redirect()->route('repartidor.pedidos_pendientes')
                        ->with('success', 'Pedido #' . $pedido->id . ' marcado como entregado correctamente.');
    }

    public function marcarEnProceso($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        $pedido = Order::where('repartidor_id', Auth::id())
                      ->findOrFail($id);
        
        if ($pedido->estado !== 'armado') {
            return redirect()->back()->with('error', 'El pedido no está listo para ser marcado en entrega.');
        }

        $pedido->estado = 'en_entrega';
        $pedido->save();

        Log::info("Pedido #{$pedido->id} marcado en entrega por repartidor " . Auth::id());

        return redirect()->route('repartidor.pedidos_pendientes')
                        ->with('success', 'Pedido #' . $pedido->id . ' marcado como en entrega.');
    }

    // ==================== MÉTODOS HELPER ====================
    
    private function obtenerZonasAsignadasParaFecha($repartidor, $fecha)
    {
        return $repartidor->zones()
                         ->wherePivot('fecha_asignacion', $fecha->toDateString())
                         ->get();
    }

    private function calcularSemanaFeria($fecha = null, $semanaOffset = 0)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::now();
        $fecha = $fecha->subWeeks($semanaOffset);
        
        if ($fecha->dayOfWeek === Carbon::SUNDAY) {
            $inicioSemana = $fecha->copy();
        } else {
            $inicioSemana = $fecha->copy()->previous(Carbon::SUNDAY);
        }
        
        $finVentas = $inicioSemana->copy()->addDays(6); // Viernes
        $diaEntrega = $inicioSemana->copy()->addDays(6); // Sábado
        
        return [
            'inicio_ventas' => $inicioSemana,
            'fin_ventas' => $finVentas,
            'dia_entrega' => $diaEntrega
        ];
    }
    
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

    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }
}