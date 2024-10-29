<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Campos que son asignables en masa
    protected $fillable = [
        'user_id',
        'nombre',
        'medida_id',
        'descripcion',
        'precio',
        'cantidad_disponible',
        'imagen',
        'categoria_id'
    ];

    protected $table = 'productos';

    // Relación con el modelo User (agricultor)
    public function usuario()
{
    return $this->belongsTo(User::class, 'user_id'); // Asegúrate de que el campo 'user_id' es la clave foránea correcta
}
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }
    public function canastas()
{
    return $this->belongsToMany(Canasta::class, 'canasta_producto', 'producto_id', 'canasta_id')
                ->withPivot('cantidad')
                ->withTimestamps();
}
   

}
