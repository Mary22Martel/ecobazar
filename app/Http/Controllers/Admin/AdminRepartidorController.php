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
    private function generarOpcionesSemanasFeria($cantidadSemanas = null)
    {
        // CAMBIO CLAVE: Si no se especifica cantidad, calcular todas las semanas disponibles
        if ($cantidadSemanas === null) {
            $cantidadSemanas = $this->calcularSemanasDesdeInicio();
        }
        
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

    private function calcularSemanasDesdeInicio()
    {
        try {
            // Obtener la fecha del primer pedido del sistema
            $primerPedido = Order::oldest('created_at')->first();
            
            if (!$primerPedido) {
                // Si no hay pedidos, mostrar solo la semana actual
                return 1;
            }
            
            $fechaPrimerPedido = Carbon::parse($primerPedido->created_at);
            $fechaActual = Carbon::now();
            
            // Calcular la diferencia en semanas
            $semanasTranscurridas = $fechaPrimerPedido->diffInWeeks($fechaActual);
            
            // Agregar 1 para incluir la semana actual + un margen de seguridad
            $totalSemanas = $semanasTranscurridas + 2;
            
            // Limitar a un máximo razonable para evitar problemas de rendimiento
            return min($totalSemanas, 52); // Máximo 1 año de semanas
            
        } catch (\Exception $e) {
            Log::error('Error calculando semanas desde inicio en AdminRepartidorController: ' . $e->getMessage());
            // Fallback: devolver 10 semanas
            return 10;
        }
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
    public function detalle($repartidorId, Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        $repartidor = User::where('role', 'repartidor')->findOrFail($repartidorId);
        
        // Obtener semana seleccionada del request
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular el día de entrega para la semana seleccionada
        $semanaActual = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $diaEntrega = $semanaActual['dia_entrega'];
        
        // Obtener zonas asignadas para esta fecha específica
        $zonasAsignadas = $repartidor->zones()
                                ->wherePivot('fecha_asignacion', $diaEntrega->toDateString())
                                ->get();
        
        // Obtener pedidos del repartidor para esta semana específica
        $pedidos = Order::where('repartidor_id', $repartidor->id)
                    ->whereBetween('created_at', [
                        $semanaActual['inicio_ventas']->startOfDay(),
                        $semanaActual['fin_ventas']->endOfDay()
                    ])
                    ->whereIn('estado', ['armado', 'en_entrega', 'entregado'])
                    ->with(['user', 'items.product'])
                    ->get();
        
        // Agrupar pedidos por zona (distrito)
        $pedidosPorZona = $pedidos->groupBy('distrito');
        
        // Obtener información de zonas para calcular tarifas
        $zonasInfo = Zone::whereIn('name', $pedidosPorZona->keys())->get()->keyBy('name');
        
        // Calcular estadísticas de entregas
        $estadisticasEntregas = [
            'total_entregas' => $pedidos->count(),
            'entregas_completadas' => $pedidos->where('estado', 'entregado')->count(),
            'entregas_pendientes' => $pedidos->whereIn('estado', ['armado', 'en_entrega'])->count(),
        ];
        
        // Calcular total a pagar
        $totalAPagar = 0;
        
        foreach ($pedidosPorZona as $zona => $pedidosZona) {
            $zonaInfo = $zonasInfo->get($zona);
            if ($zonaInfo) {
                $pedidosEntregados = $pedidosZona->where('estado', 'entregado')->count();
                $totalAPagar += $pedidosEntregados * $zonaInfo->delivery_cost;
            }
        }
        
        // Generar opciones de semanas (historial completo para admin)
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('admin.repartidores.detalle', compact(
            'repartidor',
            'zonasAsignadas',
            'pedidos',
            'pedidosPorZona',
            'zonasInfo',
            'diaEntrega',
            'estadisticasEntregas',
            'totalAPagar',
            'opcionesSemanas',
            'semanaSeleccionada'
        ));
    }

    public function reportePagosRepartidores(Request $request)
    {
        $this->authorizeRoles(['admin']);
        
        // Obtener semana seleccionada
        $semanaSeleccionada = $request->get('semana', 0);
        
        // Calcular fechas de la semana de feria
        $semanaFeria = $this->calcularSemanaFeria(null, $semanaSeleccionada);
        $inicioSemana = $semanaFeria['inicio_ventas'];
        $finSemana = $semanaFeria['fin_ventas'];
        $diaEntrega = $semanaFeria['dia_entrega'];
        
        // Obtener repartidores que tuvieron entregas en esta semana
        $repartidoresConEntregas = User::where('role', 'repartidor')
            ->where('email', '!=', 'sistema.repartidor@puntoVerde.com')
            ->whereHas('orders', function($query) use ($inicioSemana, $finSemana) {
                $query->whereBetween('created_at', [
                    $inicioSemana->startOfDay(),
                    $finSemana->endOfDay()
                ])
                ->where('estado', 'entregado');
            })
            ->with(['orders' => function($query) use ($inicioSemana, $finSemana) {
                $query->whereBetween('created_at', [
                    $inicioSemana->startOfDay(),
                    $finSemana->endOfDay()
                ])
                ->where('estado', 'entregado')
                ->with(['zone' => function($zoneQuery) {
                    $zoneQuery->select('name', 'delivery_cost');
                }]);
            }])
            ->get();
        
        // Calcular pagos por repartidor
        $pagosRepartidores = [];
        $totalGeneralAPagar = 0;
        
        foreach ($repartidoresConEntregas as $repartidor) {
            $entregasPorZona = [];
            $totalRepartidor = 0;
            
            // Agrupar entregas por zona
            foreach ($repartidor->orders as $pedido) {
                $zona = $pedido->distrito;
                $zonaInfo = Zone::where('name', $zona)->first();
                $tarifa = $zonaInfo ? $zonaInfo->delivery_cost : 0;
                
                if (!isset($entregasPorZona[$zona])) {
                    $entregasPorZona[$zona] = [
                        'entregas' => 0,
                        'tarifa' => $tarifa,
                        'total' => 0
                    ];
                }
                
                $entregasPorZona[$zona]['entregas']++;
                $entregasPorZona[$zona]['total'] += $tarifa;
                $totalRepartidor += $tarifa;
            }
            
            $pagosRepartidores[] = [
                'repartidor' => $repartidor,
                'entregas_por_zona' => $entregasPorZona,
                'total_entregas' => $repartidor->orders->count(),
                'total_pago' => $totalRepartidor
            ];
            
            $totalGeneralAPagar += $totalRepartidor;
        }
        
        // Ordenar por total a pagar (descendente)
        usort($pagosRepartidores, function($a, $b) {
            return $b['total_pago'] <=> $a['total_pago'];
        });
        
        // Opciones de semanas
        $opcionesSemanas = $this->generarOpcionesSemanasFeria();
        
        return view('admin.pagos.repartidores', compact(
            'pagosRepartidores',
            'totalGeneralAPagar',
            'opcionesSemanas',
            'semanaSeleccionada',
            'inicioSemana',
            'finSemana',
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