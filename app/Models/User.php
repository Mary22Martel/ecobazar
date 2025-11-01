<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Mercado;
use App\Models\Order;
use App\Models\Zone;


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
        'telefono',
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
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($user) {
            // Esto se ejecutará automáticamente al eliminar
            if ($user->role === 'agricultor') {
                $user->productos()->delete();
            }
        });
    }
    public function productos()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

     // ========== ZONAS PARA REPARTIDORES ==========
    // public function zones()
    // {
    //     return $this->belongsToMany(Zone::class, 'zone_user'); 
    // }
    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'zone_user');
    }

    // Obtener la zona principal del repartidor (primera asignada)
    public function primaryZone()
    {
        return $this->zones()->first();
    }

    // Verificar si el repartidor puede entregar en una zona específica
    public function canDeliverToZone($zoneId)
    {
        return $this->zones()->where('zones.id', $zoneId)->exists();
    }

    // Obtener pedidos del repartidor filtrados por zona
    public function pedidosPorZona($zoneId = null)
    {
        $query = $this->hasMany(Order::class, 'repartidor_id');
        
        if ($zoneId) {
            $query->whereHas('zone', function($q) use ($zoneId) {
                $q->where('zones.id', $zoneId);
            });
        }
        
        return $query;
    }
    public function mercado()
    {
        return $this->belongsTo(Mercado::class, 'mercado_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Pedidos asignados como repartidor
    public function entregasAsignadas()
    {
        return $this->hasMany(Order::class, 'repartidor_id');
    }

}
