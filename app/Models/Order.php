<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'empresa', 'email', 'telefono', 
        'delivery', 'direccion', 'distrito', 'pago', 'total', 'estado', 
        'envio', 'repartidor_id', 'mercadopago_payment_id', 'paid_at',
        'expires_at', 'stock_reserved'  // NUEVOS CAMPOS
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',  // NUEVO
        'stock_reserved' => 'boolean'  // NUEVO
    ];

    // Relación con el usuario que realizó la orden
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los ítems de la orden
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    // ==================== MÉTODOS PARA REPARTIDOR DEL SISTEMA ====================

    /**
     * Obtener el ID del repartidor del sistema
     */
    public static function getRepartidorSistemaId()
    {
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')
                                ->where('role', 'repartidor')
                                ->first();
        
        return $repartidorSistema ? $repartidorSistema->id : 1; // Fallback a ID 1
    }

    /**
     * Asignar automáticamente el repartidor del sistema al crear el pedido
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            // Si no se asigna repartidor, usar el del sistema
            if (!$order->repartidor_id) {
                $order->repartidor_id = self::getRepartidorSistemaId();
            }
        });
    }

    /**
     * Transferir pedido de repartidor del sistema a repartidor real
     */
    public function transferirARepartidor($nuevoRepartidorId, $zonaId = null)
    {
        // Verificar que el nuevo repartidor pueda entregar en la zona
        $nuevoRepartidor = User::find($nuevoRepartidorId);
        
        if (!$nuevoRepartidor || $nuevoRepartidor->role !== 'repartidor') {
            return false;
        }

        // Si se especifica zona, verificar que el repartidor la tenga asignada
        if ($zonaId) {
            $puedeEntregar = $nuevoRepartidor->zones()
                                            ->where('zone_id', $zonaId)
                                            ->where('fecha_asignacion', now()->toDateString())
                                            ->where('activa', true)
                                            ->exists();
            
            if (!$puedeEntregar) {
                return false;
            }
        }

        // Transferir el pedido
        $this->update([
            'repartidor_id' => $nuevoRepartidorId,
            'updated_at' => now()
        ]);

        return true;
    }

    /**
     * Verificar si el pedido está asignado al repartidor del sistema
     */
    public function esDelRepartidorSistema()
    {
        return $this->repartidor_id == self::getRepartidorSistemaId();
    }

    /**
     * Obtener zona del pedido basada en el distrito
     */
    public function getZonaAttribute()
    {
        if ($this->distrito) {
            return Zone::where('name', $this->distrito)->first();
        }
        return null;
    }

    // NUEVOS MÉTODOS SIMPLES PARA EXPIRACIÓN
    
    /**
     * Verificar si el pedido ha expirado
     */
    public function hasExpired()
    {
        return $this->expires_at && Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Marcar pedido como expirado y liberar stock
     */
    public function markAsExpired()
    {
        if ($this->estado === 'pendiente' && $this->stock_reserved) {
            // Liberar stock reservado
            foreach ($this->items as $item) {
                $item->product->increment('cantidad_disponible', $item->cantidad);
            }
            
            // Actualizar estado
            $this->update([
                'estado' => 'expirado',
                'stock_reserved' => false
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Crear pedido con expiración automática
     */
    public static function createWithExpiration($data, $minutesToExpire = 20)
    {
        $data['expires_at'] = Carbon::now()->addMinutes($minutesToExpire);
        $data['stock_reserved'] = true;
        
        return static::create($data);
    }

    // ==================== SCOPES ÚTILES ====================

    /**
     * Scope para pedidos del repartidor del sistema
     */
    public function scopeDelSistema($query)
    {
        return $query->where('repartidor_id', self::getRepartidorSistemaId());
    }

    /**
     * Scope para pedidos de repartidores reales
     */
    public function scopeDeRepartidoresReales($query)
    {
        return $query->where('repartidor_id', '!=', self::getRepartidorSistemaId());
    }

    /**
     * Scope para pedidos por zona
     */
   public function scopePorZona($query, $zonaId)
    {
        $zona = Zone::find($zonaId);
        if (!$zona) return $query->whereRaw('1 = 0');
        
        return $query->where('distrito', $zona->name); // 👈 Usar 'distrito', no zone_id
    }
}