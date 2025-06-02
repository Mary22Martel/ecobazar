<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'empresa', 'email', 'telefono', 
        'delivery', 'direccion', 'distrito', 'pago', 'total', 'estado', 
        'envio', 'repartidor_id', 'mercadopago_payment_id', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
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
}