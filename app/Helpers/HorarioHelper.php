<?php

namespace App\Helpers;

use Carbon\Carbon;

class HorarioHelper
{
    /**
     * Verifica si la tienda está abierta para realizar compras
     * 
     * HORARIO CORRECTO:
     * - ABIERTO: Domingo 00:00 hasta Jueves 15:59
     * - CERRADO: Jueves 16:00 hasta Sábado 23:59
     * 
     * @return bool
     */
    public static function tiendaAbierta(): bool
    {
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek; // 0=Domingo, 1=Lunes, ..., 4=Jueves, 5=Viernes, 6=Sábado
        $hora = $ahora->hour;
        
        // CERRADO: Viernes (5) y Sábado (6) TODO EL DÍA
        if ($dia === Carbon::FRIDAY || $dia === Carbon::SATURDAY) {
            return false;
        }
        
        // CERRADO: Jueves (4) desde las 4 PM (16:00) en adelante
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            return false;
        }
        
        // ABIERTO: Domingo (0), Lunes (1), Martes (2), Miércoles (3), Jueves antes de las 4 PM
        return true;
    }
    
    /**
     * Obtiene el mensaje de cierre apropiado según el día/hora
     * 
     * @return string
     */
    public static function mensajeCierre(): string
    {
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        // Si es Jueves después de las 4 PM
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            return '⏰ Las compras se cerraron hoy jueves a las 4:00 PM para que los agricultores preparen y cosechen los pedidos frescos para la feria del sábado. 
                    👉 Podrás volver a comprar el <strong>domingo a partir de las 12:00 AM</strong>. 🌱';
        }
        
        // Si es Viernes
        if ($dia === Carbon::FRIDAY) {
            return '📦 Los viernes la tienda está cerrada porque los agricultores están preparando todos los pedidos para la feria del sábado. 
                    👉 Podrás volver a comprar el <strong>domingo</strong>. 🌱';
        }
        
        // Si es Sábado
        if ($dia === Carbon::SATURDAY) {
            return '🎪 ¡Hoy es día de feria en Paucarbambilla! 
                    La tienda está cerrada porque estamos en la <strong>feria del Segundo Parque de Paucarbambilla (7am - 12pm)</strong>. 
                    👉 Puedes acercarte a comprar directamente o volver a comprar online el <strong>domingo</strong>. 🌱';
        }
        
        return ''; // No debería llegar aquí si tiendaAbierta() funciona correctamente
    }
    
    /**
     * Obtiene el próximo horario de apertura
     * 
     * @return Carbon
     */
    public static function proximaApertura(): Carbon
    {
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        $hora = $ahora->hour;
        
        // Si estamos en horario cerrado (Jueves 4PM en adelante, Viernes o Sábado)
        if (($dia === Carbon::THURSDAY && $hora >= 16) || 
            $dia === Carbon::FRIDAY || 
            $dia === Carbon::SATURDAY) {
            
            // La próxima apertura es el domingo a las 00:00
            $proximaApertura = $ahora->copy()->next(Carbon::SUNDAY)->startOfDay();
            return $proximaApertura;
        }
        
        // Si estamos en horario abierto, ya está abierto
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
        
        // Si ya pasó el jueves 4 PM, la entrega es del SIGUIENTE sábado
        if ($dia === Carbon::THURSDAY && $hora >= 16) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        // Si es viernes, la entrega es del sábado de mañana
        if ($dia === Carbon::FRIDAY) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        // Si es sábado, la entrega es ESTE sábado (hoy)
        if ($dia === Carbon::SATURDAY) {
            $proximoSabado = $ahora->copy();
        }
        
        // Si es domingo/lunes/martes/miércoles o jueves antes de 4 PM, 
        // la entrega es del próximo sábado
        if ($dia >= Carbon::SUNDAY && $dia <= Carbon::WEDNESDAY) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        if ($dia === Carbon::THURSDAY && $hora < 16) {
            $proximoSabado = $ahora->copy()->next(Carbon::SATURDAY);
        }
        
        return [
            'fecha' => $proximoSabado,
            'texto' => $proximoSabado->locale('es')->isoFormat('dddd D [de] MMMM'),
            'dias_faltantes' => (int) $ahora->diffInDays($proximoSabado, false) // Convertido a entero
        ];
    }
    
    /**
     * Obtiene el horario de cierre en formato legible
     * 
     * @return string
     */
    public static function horarioCierre(): string
    {
        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeek;
        
        // Si es domingo a miércoles, mostrar cuánto falta para el jueves 4 PM
        if ($dia >= Carbon::SUNDAY && $dia <= Carbon::WEDNESDAY) {
            $proximoJueves = $ahora->copy()->next(Carbon::THURSDAY)->setTime(16, 0, 0);
            $diasFaltantes = (int) $ahora->diffInDays($proximoJueves, false); // CONVERTIDO A ENTERO
            
            if ($diasFaltantes > 1) {
                return "Cierre de pedidos: Jueves a las 4:00 PM (en {$diasFaltantes} días)";
            } elseif ($diasFaltantes === 1) {
                return "Cierre de pedidos: Jueves a las 4:00 PM (mañana)";
            } else {
                $horasFaltantes = (int) $ahora->diffInHours($proximoJueves);
                return "Cierre de pedidos: Jueves a las 4:00 PM (en {$horasFaltantes} horas)";
            }
        }
        
        // Si es jueves antes de las 4 PM
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
        $ahora = Carbon::now('America/Lima');
        return $ahora->dayOfWeek === Carbon::THURSDAY && $ahora->hour < 16;
    }
}