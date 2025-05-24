<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Mercado;
use App\Models\Order;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'mercado_id',
    ];

    /**
     * The attributes that should be hidden for serialization
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
    public function productos()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'zone_user'); 
    }
    public function mercado()
    {
        return $this->belongsTo(Mercado::class, 'mercado_id');
    }
    public function orders()
{
    return $this->hasMany(Order::class);
}

}
