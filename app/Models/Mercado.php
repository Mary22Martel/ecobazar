<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercado extends Model
{
    // Indica los campos que pueden asignarse en bloque
    protected $fillable = [
        'nombre',
        'zona',
    ];

    // Si en lugar de fillable prefieres
    // protected $guarded = []; 
    // para permitir todos los campos

    public function agricultores()
    {
        return $this->hasMany(User::class);
    }
}
