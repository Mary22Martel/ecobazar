<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Asegúrate de agregar esta línea

class RepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorizeRoles(['repartidor']);
        return view('repartidor.dashboard');
    }

    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }

    public function pedidosPendientes()
    {
        $this->authorizeRoles(['repartidor']);
        $repartidorId = Auth::id();
        $pedidos = Order::where('repartidor_id', $repartidorId)->get();
        return view('repartidor.pedidos_pendientes', compact('pedidos'));
    }

    public function marcarComoEntregado($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        $pedido = Order::findOrFail($id);
        
        // if ($pedido->estado !== 'listo' && $pedido->estado !== 'pendiente') {
        //     return redirect()->route('repartidor.pedidos_pendientes')->with('error', 'El pedido no está listo para ser entregado.');
        // }

        $pedido->estado = 'entregado';
        $pedido->save();

        // Verificar si el estado cambió correctamente
        if ($pedido->wasChanged('estado')) {
            return redirect()->route('repartidor.pedidos_pendientes')->with('success', 'Pedido marcado como entregado.');
        } else {
            return redirect()->route('repartidor.pedidos_pendientes')->with('error', 'Hubo un problema al actualizar el pedido.');
        }
    }

    public function marcarEnProceso($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        $pedido = Order::findOrFail($id);
        
        // Permitir el cambio solo si el estado es "pagado"
        if (!in_array(strtolower($pedido->estado), ['pagado', 'pendiente'])) {
            return redirect()->route('repartidor.pedidos_pendientes')->with('error', 'El pedido no puede ser marcado como "En Proceso".');
        }

        
        $pedido->estado = 'en proceso';
        $pedido->save();

        return redirect()->route('repartidor.pedidos_pendientes')->with('success', 'El pedido se ha marcado como "En Proceso".');
    }
  

    public function detallePedido($id)
    {
        $this->authorizeRoles(['repartidor']);
        $pedido = Order::with(['items.product.usuario'])->findOrFail($id);
        return view('repartidor.pedido_detalle', compact('pedido'));
    }
}
