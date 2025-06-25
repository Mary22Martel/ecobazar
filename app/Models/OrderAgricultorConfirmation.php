<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAgricultorConfirmation extends Model
{
    protected $fillable = ['order_id', 'agricultor_id', 'confirmed_at'];
    
    protected $dates = ['confirmed_at'];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function agricultor()
    {
        return $this->belongsTo(User::class, 'agricultor_id');
    }
}