<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoItem extends Model
{
    use HasFactory;

    protected $fillable = ['carrito_id', 'producto_id', 'cantidad'];

    // Relación con el modelo Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    // Relación con el modelo Carrito - ESTA ES LA QUE FALTABA
    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }
}