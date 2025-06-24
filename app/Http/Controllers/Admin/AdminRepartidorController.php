<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista todos los repartidores y sus asignaciones
     */
    public function index(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada del request
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular semana actual de feria con offset
        $semanaActual = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $diaEntrega = $semanaActual['dia_entrega']; // Sábado
        
        // Obtener todos los repartidores (excluyendo el del sistema)
        $repartidores = User::where('role', 'repartidor')
                        ->where('email', '!=', 'sistema.repartidor@puntoVerde.com')
                        ->with(['zones' => function($query) use ($diaEntrega) {
                            $query->where('zone_user.fecha_asignacion', $diaEntrega->toDateString());
                        }])
                        ->get();

        // Obtener todas las zonas disponibles
        $zonasDisponibles = Zone::where('active', true)->get();
        
        // Obtener estadísticas del día de entrega (FILTRADAS POR SEMANA)
        $estadisticas = $this->obtenerEstadisticasEntrega($diaEntrega, $semanaActual);
        
        // Obtener repartidor del sistema
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        
        // Generar opciones de semanas para la vista
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('admin.repartidores.index', compact(
            'repartidores',
            'zonasDisponibles', 
            'diaEntrega',
            'estadisticas',
            'repartidorSistema',
            'opcionesSemanas',
            'semanaSeleccionada'
        ));
    }

    /**
     * Generar opciones de semanas de feria
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

    /**
     * Obtener estadísticas de entregas FILTRADAS POR SEMANA
     */
    private function obtenerEstadisticasEntrega($diaEntrega, $semanaActual = null)
    {
        // Si no se pasa semanaActual, calcular la actual
        if (!$semanaActual) {
            $semanaActual = $this->calcularSemanaFeria();
        }
        
        // Obtener ID del repartidor del sistema
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        $repartidorSistemaId = $repartidorSistema ? $repartidorSistema->id : null;
        
        return [
            // Total de pedidos de la semana filtrada
            'total_pedidos' => Order::whereBetween('created_at', [
                                    $semanaActual['inicio_ventas']->startOfDay(),
                                    $semanaActual['fin_ventas']->endOfDay()
                                ])
                                ->where('estado', 'armado')
                                ->count(),
            
            // Pedidos del sistema filtrados por semana
            'pedidos_sistema' => $repartidorSistemaId ? 
                                Order::where('repartidor_id', $repartidorSistemaId)
                                     ->whereBetween('created_at', [
                                         $semanaActual['inicio_ventas']->startOfDay(),
                                         $semanaActual['fin_ventas']->endOfDay()
                                     ])
                                     ->where('estado', 'armado')
                                     ->count() : 0,
            
            // Repartidores activos para esta fecha específica
            'repartidores_activos' => User::where('role', 'repartidor')
                                        ->where('email', '!=', 'sistema.repartidor@puntoVerde.com')
                                        ->whereHas('zones', function($query) use ($diaEntrega) {
                                            $query->where('zone_user.fecha_asignacion', $diaEntrega->toDateString());
                                        })
                                        ->count(),
            
            // Zonas cubiertas para esta fecha específica
            'zonas_cubiertas' => Zone::whereHas('users', function($query) use ($diaEntrega) {
                                      $query->where('zone_user.fecha_asignacion', $diaEntrega->toDateString())
                                            ->where('users.role', 'repartidor');
                                  })
                                  ->count()
        ];
    }

    /**
     * Asignar zonas a un repartidor para el día de entrega
     */
    public function asignarZonas(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $request->validate([
            'repartidor_id' => 'required|exists:users,id',
            'zonas' => 'required|array',
            'zonas.*' => 'exists:zones,id'
        ]);

        try {
            DB::beginTransaction();
            
            $repartidor = User::findOrFail($request->repartidor_id);
            $zonas = $request->zonas;
            
            // Verificar que es repartidor
            if ($repartidor->role !== 'repartidor') {
                return back()->with('error', 'El usuario seleccionado no es un repartidor');
            }

            // Calcular el día de entrega (sábado de esta semana)
            $semanaActual = $this->calcularSemanaFeria();
            $diaEntrega = $semanaActual['dia_entrega'];
            
            // Limpiar asignaciones previas de este repartidor para este día
            $repartidor->zones()
                      ->wherePivot('fecha_asignacion', $diaEntrega->toDateString())
                      ->detach();
            
            // Asignar nuevas zonas
            $asignacionesData = [];
            foreach ($zonas as $zonaId) {
                $asignacionesData[$zonaId] = [
                    'fecha_asignacion' => $diaEntrega->toDateString(),
                    'activa' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            $repartidor->zones()->attach($asignacionesData);
            
            // Transferir pedidos del repartidor del sistema a este repartidor
            $pedidosTransferidos = $this->transferirPedidosPorZonas($zonas, $repartidor->id, $semanaActual);
            
            DB::commit();
            
            $zonasNombres = Zone::whereIn('id', $zonas)->pluck('name')->implode(', ');
            
            Log::info("Admin " . Auth::id() . " asignó zonas [{$zonasNombres}] al repartidor {$repartidor->name} para {$diaEntrega->format('d/m/Y')}");
            
            return back()->with('success', 
                "Zonas asignadas exitosamente a {$repartidor->name}. " .
                "Se transfirieron {$pedidosTransferidos} pedidos."
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error asignando zonas: ' . $e->getMessage());
            return back()->with('error', 'Error al asignar zonas: ' . $e->getMessage());
        }
    }

    /**
     * Quitar asignación de zonas de un repartidor
     */
    public function quitarAsignacion(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $request->validate([
            'repartidor_id' => 'required|exists:users,id',
            'zona_id' => 'required|exists:zones,id'
        ]);

        try {
            DB::beginTransaction();
            
            $repartidor = User::findOrFail($request->repartidor_id);
            $zonaId = $request->zona_id;
            
            // Calcular el día de entrega
            $semanaActual = $this->calcularSemanaFeria();
            $diaEntrega = $semanaActual['dia_entrega'];
            
            // Quitar asignación de zona - MÉTODO CORREGIDO
            $detached = $repartidor->zones()
                                  ->wherePivot('fecha_asignacion', $diaEntrega->toDateString())
                                  ->detach($zonaId);
            
            if ($detached > 0) {
                // Devolver pedidos al repartidor del sistema
                $pedidosDevueltos = $this->devolverPedidosAlSistema($zonaId, $repartidor->id, $semanaActual);
                
                DB::commit();
                
                $zona = Zone::find($zonaId);
                
                Log::info("Admin " . Auth::id() . " quitó zona {$zona->name} del repartidor {$repartidor->name}");
                
                return back()->with('success', 
                    "Zona {$zona->name} quitada de {$repartidor->name}. " .
                    "Se devolvieron {$pedidosDevueltos} pedidos al sistema."
                );
            } else {
                DB::rollback();
                return back()->with('error', 'No se pudo quitar la zona. Puede que ya no esté asignada para esta fecha.');
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error quitando asignación: ' . $e->getMessage());
            return back()->with('error', 'Error al quitar asignación: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de asignaciones y pedidos de un repartidor
     */
    public function detalle($repartidorId)
    {
        $this->authorizeRoles(['admin']);
        
        $repartidor = User::where('role', 'repartidor')->findOrFail($repartidorId);
        
        // Calcular el día de entrega
        $semanaActual = $this->calcularSemanaFeria();
        $diaEntrega = $semanaActual['dia_entrega'];
        
        // Obtener zonas asignadas para esta fecha
        $zonasAsignadas = $repartidor->zones()
                                   ->wherePivot('fecha_asignacion', $diaEntrega->toDateString())
                                   ->get();
        
        // Obtener pedidos del repartidor para el día de entrega
        $pedidos = Order::where('repartidor_id', $repartidor->id)
                       ->whereIn('estado', ['armado', 'en_entrega', 'entregado'])
                       ->whereBetween('created_at', [
                           $semanaActual['inicio_ventas']->startOfDay(),
                           $semanaActual['fin_ventas']->endOfDay()
                       ])
                       ->with(['user', 'items.product'])
                       ->get();
        
        // Agrupar pedidos por zona
        $pedidosPorZona = $pedidos->groupBy('distrito');
        
        return view('admin.repartidores.detalle', compact(
            'repartidor',
            'zonasAsignadas',
            'pedidos',
            'pedidosPorZona',
            'diaEntrega'
        ));
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Transferir pedidos del sistema a un repartidor por zonas
     */
    private function transferirPedidosPorZonas($zonasIds, $repartidorId, $semanaActual)
    {
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        
        if (!$repartidorSistema) {
            Log::warning('Repartidor del sistema no encontrado');
            return 0;
        }

        $zonasNombres = Zone::whereIn('id', $zonasIds)->pluck('name')->toArray();
        
        // Buscar pedidos del sistema que corresponden a las zonas asignadas y de esta semana
        $pedidosATransferir = Order::where('repartidor_id', $repartidorSistema->id)
                                ->whereIn('distrito', $zonasNombres)
                                ->whereIn('estado', ['armado', 'en_entrega'])
                                ->whereBetween('created_at', [
                                    $semanaActual['inicio_ventas']->startOfDay(),
                                    $semanaActual['fin_ventas']->endOfDay()
                                ])
                                ->get();

        $transferidos = 0;
        foreach ($pedidosATransferir as $pedido) {
            $pedido->update(['repartidor_id' => $repartidorId]);
            $transferidos++;
            
            Log::info("Pedido #{$pedido->id} transferido del sistema al repartidor {$repartidorId}");
        }

        return $transferidos;
    }

    /**
     * Devolver pedidos del repartidor al sistema
     */
    private function devolverPedidosAlSistema($zonaId, $repartidorId, $semanaActual)
    {
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        $zona = Zone::find($zonaId);
        
        if (!$repartidorSistema || !$zona) {
            Log::warning('Repartidor del sistema o zona no encontrados');
            return 0;
        }

        // Buscar pedidos del repartidor en esa zona y de esta semana
        $pedidosADevolver = Order::where('repartidor_id', $repartidorId)
                                ->where('distrito', $zona->name)
                                ->whereIn('estado', ['armado', 'en_entrega'])
                                ->whereBetween('created_at', [
                                    $semanaActual['inicio_ventas']->startOfDay(),
                                    $semanaActual['fin_ventas']->endOfDay()
                                ])
                                ->get();

        $devueltos = 0;
        foreach ($pedidosADevolver as $pedido) {
            $pedido->update(['repartidor_id' => $repartidorSistema->id]);
            $devueltos++;
            
            Log::info("Pedido #{$pedido->id} devuelto del repartidor {$repartidorId} al sistema");
        }

        return $devueltos;
    }

    /**
     * Calcular semana de feria (mismo método que AdminController)
     */
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

    /**
     * Autorización de roles
     */
    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }
}