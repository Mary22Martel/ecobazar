<?php

namespace App\Helpers;

use Carbon\Carbon;

class HorarioHelper
{
    // 🚨 MODO PRUEBA ACTIVADO - TIENDA ABIERTA TODOS LOS DÍAS 🚨
    private static $MODE_PRUEBA = false; // ⚠️ Cambiar a false después de las pruebas
    
    /**
     * Verifica si la tienda está abierta para realizar compras
     * 
     * HORARIO NORMAL:
     * - ABIERTO: Domingo 00:00 hasta Jueves 15:59
     * - CERRADO: Jueves 16:00 hasta Sábado 23:59
     * 
     * MODO PRUEBA:
     * - ABIERTO: Todos los días, todas las horas
     * 
     * @return bool
     */
    public static function tiendaAbierta(): bool
    {
        // 🚨 SI ESTÁ EN MODO PRUEBA, SIEMPRE RETORNA TRUE
        if (self::$MODE_PRUEBA) {
            return true;
        }
        
        // LÓGICA NORMAL (cuando MODE_PRUEBA = false)
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        if ($dia === Carbon::FRIDAY || $dia === Carbon::SATURDAY) {
            return false;
        }
        
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtiene el mensaje de cierre apropiado según el día/hora
     * 
     * @return string
     */
    public static function mensajeCierre(): string
    {
        // En modo prueba, no hay mensaje de cierre
        if (self::$MODE_PRUEBA) {
            return '🧪 MODO PRUEBA ACTIVADO - La tienda está disponible para pruebas.';
        }
        
        // LÓGICA NORMAL
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            return '⏰ Las compras se cerraron hoy jueves a las 4:00 PM para que los agricultores preparen y cosechen los pedidos frescos para la feria del sábado. 
                    👉 Podrás volver a comprar el <strong>domingo a partir de las 12:00 AM</strong>. 🌱';
        }
        
        if ($dia === Carbon::FRIDAY) {
            return '📦 Los viernes la tienda está cerrada porque los agricultores están preparando todos los pedidos para la feria del sábado. 
                    👉 Podrás volver a comprar el <strong>domingo</strong>. 🌱';
        }
        
        if ($dia === Carbon::SATURDAY) {
            return '🎪 ¡Hoy es día de feria en Paucarbambilla! 
                    La tienda está cerrada porque estamos en la <strong>feria del Segundo Parque de Paucarbambilla (7am - 12pm)</strong>. 
                    👉 Puedes acercarte a comprar directamente o volver a comprar online el <strong>domingo</strong>. 🌱';
        }
        
        return '';
    }
    
    /**
     * Obtiene el próximo horario de apertura
     * 
     * @return Carbon
     */
    public static function proximaApertura(): Carbon
    {
        $ahora = Carbon::now('America/Lima');
        
        // En modo prueba, ya está abierto
        if (self::$MODE_PRUEBA) {
            return $ahora;
        }
        
        // LÓGICA NORMAL
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        if (($dia === Carbon::THURSDAY && $hora >= 16) || 
            $dia === Carbon::FRIDAY || 
            $dia === Carbon::SATURDAY) {
            
            $proximaApertura = $ahora->copy()->next(Carbon::SUNDAY)->startOfDay();
            return $proximaApertura;
        }
        
        return $ahora;
    }
    
    /**
     * Obtiene información del próximo sábado de entrega
     * 
     * @return array
     */
    public static function infoEntrega(): array
    {
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        // Determinar el próximo sábado de entrega
        $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        if ($dia === Carbon::FRIDAY) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        if ($dia === Carbon::SATURDAY) {
            $proximoSabado = $ahora->copy();
        }
        
        if ($dia >= Carbon::SUNDAY && $dia <= Carbon::WEDNESDAY) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        if ($dia === Carbon::THURSDAY && $hora < 16) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        return [
            'fecha' => $proximoSabado,
            'texto' => $proximoSabado->locale('es')->isoFormat('dddd D [de] MMMM'),
            'dias_faltantes' => (int) $ahora->diffInDays($proximoSabado, false)
        ];
    }
    
    /**
     * Obtiene el horario de cierre en formato legible
     * 
     * @return string
     */
    public static function horarioCierre(): string
    {
        // En modo prueba, mostrar mensaje especial
        if (self::$MODE_PRUEBA) {
            return "🧪 MODO PRUEBA - Sin restricciones de horario";
        }
        
        // LÓGICA NORMAL
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        
        if ($dia >= Carbon::SUNDAY && $dia <= Carbon::WEDNESDAY) {
            $proximoJueves = $ahora->copy()->next(Carbon::THURSDAY)->setTime(16, 0, 0);
            $diasFaltantes = (int) $ahora->diffInDays($proximoJueves, false);
            
            if ($diasFaltantes > 1) {
                return "Cierre de pedidos: Jueves a las 4:00 PM (en {$diasFaltantes} días)";
            } elseif ($diasFaltantes === 1) {
                return "Cierre de pedidos: Jueves a las 4:00 PM (mañana)";
            } else {
                $horasFaltantes = (int) $ahora->diffInHours($proximoJueves);
                return "Cierre de pedidos: Jueves a las 4:00 PM (en {$horasFaltantes} horas)";
            }
        }
        
        if ($dia === Carbon::THURSDAY && $ahora->hour < 16) {
            $cierreHoy = $ahora->copy()->setTime(16, 0, 0);
            $horasFaltantes = (int) $ahora->diffInHours($cierreHoy);
            $minutosFaltantes = (int) ($ahora->diffInMinutes($cierreHoy) % 60);
            
            if ($horasFaltantes > 0) {
                return "⚠️ ¡Última oportunidad! Cierre de pedidos hoy a las 4:00 PM (en {$horasFaltantes}h {$minutosFaltantes}min)";
            } else {
                return "⚠️ ¡ÚLTIMA HORA! Cierre de pedidos en {$minutosFaltantes} minutos";
            }
        }
        
        return "Cerrado hasta el domingo";
    }
    
    /**
     * Verifica si estamos en el último día de compras (jueves antes de 4 PM)
     * 
     * @return bool
     */
    public static function esUltimoDia(): bool
    {
        // En modo prueba, nunca es último día
        if (self::$MODE_PRUEBA) {
            return false;
        }
        
        // LÓGICA NORMAL
        $ahora = Carbon::now('America/Lima');
        return $ahora->dayOfWeek === Carbon::THURSDAY && $ahora->hour < 16;
    }
    
    /**
     * Verifica si el modo prueba está activo
     * 
     * @return bool
     */
    public static function isModoPrueba(): bool
    {
        return self::$MODE_PRUEBA;
    }
}