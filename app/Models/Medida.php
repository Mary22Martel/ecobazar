<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre', 'simbolo', 'description', 'active']; 

    /**
     * IMPORTANTE: Relación con productos
     * Una medida tiene muchos productos
     */
    public function productos()
    {
        return $this->hasMany(Product::class, 'medida_id');
    }

    /**
     * Scope: Solo medidas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}