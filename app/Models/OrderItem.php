<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    // Relación con la orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }
}
