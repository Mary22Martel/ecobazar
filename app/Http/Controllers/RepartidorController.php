<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
       // dd(Auth::id()); // Verificar el ID del usuario autenticado
    
        $repartidorId = Auth::id();
//dd($repartidorId); // Verificar si se obtiene el ID del usuario autenticado correctamente

$pedidos = Order::where('repartidor_id', $repartidorId)
               ->where('estado', 'pendiente')
               ->get();

//dd($pedidos); // Verificar si se obtienen los pedidos correctamente

    
        return view('repartidor.pedidos_pendientes', compact('pedidos'));
    }
    
    

    public function marcarComoEntregado($id)
    {
        $this->authorizeRoles(['repartidor']);
        
        // Buscar el pedido y actualizar su estado
        $pedido = Order::findOrFail($id);
        
        if ($pedido->estado !== 'listo' && $pedido->estado !== 'pendiente') {
            return redirect()->route('repartidor.pedidos_pendientes')->with('error', 'El pedido no está listo para ser entregado.');
        }
    
        $pedido->estado = 'entregado';
        $pedido->save();
    
        return redirect()->route('repartidor.pedidos_pendientes')->with('success', 'Pedido marcado como entregado.');
    }
    
}

