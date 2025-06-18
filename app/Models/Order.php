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
}