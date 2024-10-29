<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function items()
    {
        return $this->hasMany(CarritoItem::class);
    }
    public function total()
{
    // Asegúrate de que la relación 'items' esté cargada
    if (!$this->relationLoaded('items')) {
        $this->load('items.product');
    }

    return $this->items->sum(function ($item) {
        return $item->product->precio * $item->cantidad;
    });
}

}