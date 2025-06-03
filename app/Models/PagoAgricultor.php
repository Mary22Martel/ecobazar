<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PagoAgricultor extends Model
{
    use HasFactory;

    protected $table = 'pagos_agricultores';

    protected $fillable = [
        'agricultor_id',
        'periodo_inicio',
        'periodo_fin',
        'monto_total',
        'productos_vendidos',
        'pedidos_involucrados',
        'estado',
        'fecha_pago',
        'metodo_pago',
        'referencia_pago',
        'notas',
        'pagado_por' // ID del admin que realizó el pago
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'fecha_pago' => 'datetime',
        'monto_total' => 'decimal:2',
        'productos_vendidos' => 'array', // JSON con detalle de productos
        'pedidos_involucrados' => 'array' // JSON con IDs de pedidos
    ];

    // Relaciones
    public function agricultor()
    {
        return $this->belongsTo(User::class, 'agricultor_id');
    }

    public function pagadoPor()
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeDelPeriodo($query, $inicio, $fin)
    {
        return $query->where('periodo_inicio', '>=', $inicio)
                    ->where('periodo_fin', '<=', $fin);
    }

    // Métodos helper
    public function marcarComoPagado($metodoPago = null, $referencia = null, $notas = null)
    {
        $this->update([
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'metodo_pago' => $metodoPago,
            'referencia_pago' => $referencia,
            'notas' => $notas,
            'pagado_por' => Auth::check() ? Auth::id() : null
        ]);
    }

    public function getPeriodoFormateadoAttribute()
    {
        return $this->periodo_inicio->format('d/m/Y') . ' - ' . $this->periodo_fin->format('d/m/Y');
    }

    public function getDiasTranscurridosAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    // Método estático para crear un pago
    public static function crearPago($agricultorId, $periodoInicio, $periodoFin, $estado = 'pendiente')
    {
        // Calcular datos del período
        $orderItems = \App\Models\OrderItem::query()
            ->join('productos', 'order_items.producto_id', '=', 'productos.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('productos.user_id', $agricultorId)
            ->where('orders.estado', 'armado')
            ->whereBetween('orders.created_at', [
                Carbon::parse($periodoInicio)->startOfDay(),
                Carbon::parse($periodoFin)->endOfDay()
            ])
            ->with(['product', 'order'])
            ->get();

        if ($orderItems->isEmpty()) {
            return null;
        }

        $montoTotal = $orderItems->sum(function($item) {
            return $item->precio * $item->cantidad;
        });

        $productosDetalle = $orderItems->groupBy('producto_id')->map(function($items) {
            $producto = $items->first()->product;
            return [
                'nombre' => $producto->nombre,
                'cantidad_total' => $items->sum('cantidad'),
                'monto_total' => $items->sum(function($item) {
                    return $item->precio * $item->cantidad;
                }),
                'ventas_count' => $items->count()
            ];
        })->values()->toArray();

        $pedidosIds = $orderItems->pluck('order_id')->unique()->values()->toArray();

        return self::create([
            'agricultor_id' => $agricultorId,
            'periodo_inicio' => $periodoInicio,
            'periodo_fin' => $periodoFin,
            'monto_total' => $montoTotal,
            'productos_vendidos' => $productosDetalle,
            'pedidos_involucrados' => $pedidosIds,
            'estado' => $estado
        ]);
    }
}