<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canasta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion'
    ];

    // Relación con los productos
    public function productos()
{
    return $this->belongsToMany(Product::class, 'canasta_producto', 'canasta_id', 'producto_id')
                ->withPivot('cantidad')
                ->withTimestamps();
}
}
