<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Canasta;
use App\Models\Categoria;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorizeRoles(['admin']);

        $categorias = Categoria::all();
        $canastas = Canasta::all(); 
        $pedidos = Order::with('items.product')->get();

        return view('admin.dashboard', compact('categorias', 'canastas', 'pedidos'));
    }

    private function authorizeRoles($roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta página.');
        }
    }

 
public function detallePedido()
{
    // Obtener todos los pedidos
    $pedidos = Order::with('items.product')->get();

    return view('admin.pedido.index', compact('pedidos'));
}
}

