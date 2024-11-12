<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'delivery_cost'];

    public function users()
{
    return $this->belongsToMany(User::class, 'zone_user'); 
}
}


