<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    use HasFactory;
    protected $table = 'productos';

    // Campos que son asignables en masa
    protected $fillable = [
        'user_id',
        'nombre',
        'medida_id',
        'descripcion',
        'precio',
        'cantidad_disponible',
        'imagen',
        'categoria_id',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad_disponible' => 'integer',
    ];

    // Relación con el modelo User (agricultor)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class, 'medida_id');
    }

    // public function canastas()
    // {
    //     return $this->belongsToMany(Canasta::class, 'canasta_producto', 'producto_id', 'canasta_id')
    //                 ->withPivot('cantidad')
    //                 ->withTimestamps();
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // IMPORTANTE: Corregir la relación con OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'producto_id'); // Si tu tabla order_items usa 'producto_id'
        // O usa esto si la tabla usa 'product_id':
        // return $this->hasMany(OrderItem::class, 'product_id');
    }
}