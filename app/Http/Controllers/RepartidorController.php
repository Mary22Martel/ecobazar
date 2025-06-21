<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
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
        
        // ========== ENTREGAS DE ESTA SEMANA SOLAMENTE ==========
        
        // Entregas pendientes de esta semana (estado 'armado' = listo para entregar)
        $entregasPendientes = Order::where('repartidor_id', $repartidorId)
                                  ->where('estado', 'armado')
                                  ->whereBetween('created_at', [
                                      $inicioSemana->startOfDay(), 
                                      $finSemana->endOfDay()
                                  ])
                                  ->count();
        
        // Entregas completadas de esta semana
        $entregasCompletadas = Order::where('repartidor_id', $repartidorId)
                                   ->where('estado', 'entregado')
                                   ->whereBetween('created_at', [
                                       $inicioSemana->startOfDay(), 
                                       $finSemana->endOfDay()
                                   ])
                                   ->count();
        
        // Total de entregas de esta semana
        $entregasSemana = Order::where('repartidor_id', $repartidorId)
                               ->whereIn('estado', ['armado', 'entregado'])
                               ->whereBetween('created_at', [
                                   $inicioSemana->startOfDay(), 
                                   $finSemana->endOfDay()
                               ])
                               ->count();
        
        // Total histórico de entregas (todas las semanas)
        $totalEntregas = Order::where('repartidor_id', $repartidorId)
                              ->where('estado', 'entregado')
                              ->count();
        
        return view('repartidor.dashboard', compact(
            'entregasPendientes',
            'entregasCompletadas', 
            'entregasSemana',
            'totalEntregas',
            'inicioSemana',
            'finSemana',
            'diaEntrega'
        ));
    }

    // ==================== LÓGICA DE SEMANA DE FERIA (IGUAL QUE AGRICULTOR) ====================
    
    /**
     * Calcula la semana de feria: domingo a viernes (ventas) + sábado (entrega)
     * MISMA LÓGICA QUE EL AGRICULTOR Y ADMIN CONTROLLER
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

    public function pedidosPendientes()
    {
        $this->authorizeRoles(['repartidor']);
        
        $repartidorId = Auth::id();
        
        // Obtener pedidos asignados al repartidor que están listos para entrega
        $pedidos = Order::with(['user', 'items.product'])
                       ->where('repartidor_id', $repartidorId)
                       ->whereIn('estado', ['armado', 'en_entrega']) // Pedidos listos para entregar
                       ->orderBy('created_at', 'desc')
                       ->get();
        
        return view('repartidor.pedidos_pendientes', compact('pedidos'));
    }

    public function rutas()
    {
        $this->authorizeRoles(['repartidor']);
        
        // Obtener pedidos del repartidor para optimizar rutas
        $repartidorId = Auth::id();
        
        $pedidos = Order::with(['user'])
                       ->where('repartidor_id', $repartidorId)
                       ->whereIn('estado', ['armado', 'en_entrega'])
                       ->get();
        
        // Agrupar por zona/distrito para optimización de rutas
        $pedidosPorZona = $pedidos->groupBy('distrito');
        
        // Calcular estadísticas de ruta
        $totalEntregas = $pedidos->count();
        $zonas = $pedidosPorZona->count();
        
        return view('repartidor.rutas', compact(
            'pedidos', 
            'pedidosPorZona', 
            'totalEntregas', 
            'zonas'
        ));
    }

    public function entregasCompletadas()
    {
        $this->authorizeRoles(['repartidor']);
        
        $repartidorId = Auth::id();
        
        $pedidos = Order::with(['user', 'items.product'])
                       ->where('repartidor_id', $repartidorId)
                       ->where('estado', 'entregado')
                       ->orderBy('updated_at', 'desc')
                       ->paginate(15);
        
        // Estadísticas de entregas completadas
        $totalEntregadas = Order::where('repartidor_id', $repartidorId)
                               ->where('estado', 'entregado')
                               ->count();
        
        $entregadasHoy = Order::where('repartidor_id', $repartidorId)
                             ->where('estado', 'entregado')
                             ->whereDate('updated_at', today())
                             ->count();
        
        $entregadasSemana = Order::where('repartidor_id', $repartidorId)
                                ->where('estado', 'entregado')
                                ->whereBetween('updated_at', [
                                    now()->startOfWeek(),
                                    now()->endOfWeek()
                                ])
                                ->count();
        
        return view('repartidor.entregas_completadas', compact(
            'pedidos',
            'totalEntregadas',
            'entregadasHoy',
            'entregadasSemana'
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
        
        // Verificar que el pedido esté en estado válido para entrega
        if (!in_array($pedido->estado, ['armado', 'en_entrega'])) {
            return redirect()->back()->with('error', 'El pedido no está en estado válido para ser entregado.');
        }

        $pedido->estado = 'entregado';
        $pedido->fecha_entrega = now();
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
        
        // Verificar que el pedido esté en estado válido
        if ($pedido->estado !== 'armado') {
            return redirect()->back()->with('error', 'El pedido no está listo para ser marcado en entrega.');
        }

        $pedido->estado = 'en_entrega';
        $pedido->fecha_inicio_entrega = now();
        $pedido->save();

        Log::info("Pedido #{$pedido->id} marcado en entrega por repartidor " . Auth::id());

        return redirect()->route('repartidor.pedidos_pendientes')
                        ->with('success', 'Pedido #' . $pedido->id . ' marcado como en entrega.');
    }

    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }
}