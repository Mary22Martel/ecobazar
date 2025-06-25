<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre', 'description', 'active']; 

    /**
     * IMPORTANTE: Relación con productos
     * Una categoría tiene muchos productos
     */
    public function productos()
    {
        return $this->hasMany(Product::class, 'categoria_id');
    }

    /**
     * Scope: Solo categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}