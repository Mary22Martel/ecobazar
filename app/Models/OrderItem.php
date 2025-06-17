<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'producto_id', // Ajustar según tu tabla
        'cantidad',
        'precio'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
    ];

    /**
     * Relación: Un item pertenece a un pedido
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relación: Un item pertenece a un producto
     * IMPORTANTE: Ajustar según el nombre de la columna en tu tabla
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'producto_id'); // Si tu tabla usa 'producto_id'
        // O usa esto si la tabla usa 'product_id':
        // return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Accessor: Subtotal del item
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio;
    }
}