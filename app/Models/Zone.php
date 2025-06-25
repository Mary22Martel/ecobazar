<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model 
{
    protected $fillable = ['name', 'delivery_cost', 'active'];

    public function users() 
    {
        return $this->belongsToMany(User::class, 'zone_user');
    }

    // ========== ESPECÍFICO PARA REPARTIDORES ==========
    public function repartidores() 
    {
        return $this->belongsToMany(User::class)
                    ->where('role', 'repartidor');
    }

    // Obtener el repartidor principal de esta zona
    public function repartidorPrincipal()
    {
        return $this->repartidores()->first();
    }

    // Verificar si la zona tiene repartidores disponibles
    public function tieneRepartidoresDisponibles()
    {
        return $this->repartidores()->count() > 0;
    }

    // Pedidos de esta zona
    public function orders()
    {
        return $this->hasMany(Order::class, 'zone_id');
    }

    // Estadísticas de entregas por zona
    public function estadisticasEntregas($fechaInicio = null, $fechaFin = null)
    {
        $query = $this->orders();
        
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }
        
        return [
            'total' => $query->count(),
            'entregados' => $query->where('estado', 'entregado')->count(),
            'pendientes' => $query->where('estado', 'armado')->count(),
        ];
    }
}