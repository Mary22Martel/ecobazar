<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';
    protected $description = 'Marcar como expirados los pedidos pendientes que han superado el tiempo límite';

    public function handle()
    {
        $expiredCount = 0;
        
        // Buscar pedidos pendientes que han expirado
        $expiredOrders = Order::where('estado', 'pendiente')
            ->where('expires_at', '<=', Carbon::now('America/Lima'))
            ->where('stock_reserved', true)
            ->get();

        foreach ($expiredOrders as $order) {
            if ($order->markAsExpired()) {
                $expiredCount++;
                $this->info("Pedido #{$order->id} marcado como expirado");
                Log::info("Pedido #{$order->id} expirado automáticamente - Stock liberado");
            }
        }

        if ($expiredCount > 0) {
            $this->info("Se expiraron {$expiredCount} pedidos y se liberó el stock");
        } else {
            $this->info("No hay pedidos para expirar");
        }

        return 0;
    }
}