<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Canasta;
use App\Models\Categoria;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;


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

    public function createRepartidor()
    {
        $this->authorizeRoles(['admin']);
        return view('admin.repartidor.create_repartidor'); 
    }

    public function storeRepartidor(Request $request)
    {
        $this->authorizeRoles(['admin']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'repartidor',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Repartidor creado con éxito.');
    }

    public function listRepartidores()
    {
        $this->authorizeRoles(['admin']);
        $repartidores = User::where('role', 'repartidor')->get();
        return view('admin.repartidor.list', compact('repartidores'));
    }

    public function editRepartidor($id)
    {
        $this->authorizeRoles(['admin']);
        $repartidor = User::findOrFail($id);
        return view('admin.repartidor.edit', compact('repartidor'));
    }

    public function updateRepartidor(Request $request, $id)
    {
        $this->authorizeRoles(['admin']);
        $repartidor = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $repartidor->name = $request->name;
        $repartidor->email = $request->email;

        if ($request->filled('password')) {
            $repartidor->password = Hash::make($request->password);
        }

        $repartidor->save();

        return redirect()->route('admin.repartidor.list')->with('success', 'Repartidor actualizado con éxito.');
    }

    public function deleteRepartidor($id)
    {
        $this->authorizeRoles(['admin']);
        $repartidor = User::findOrFail($id);
        $repartidor->delete();

        return redirect()->route('admin.repartidor.list')->with('success', 'Repartidor eliminado con éxito.');
    }

   // AdminController.php

public function asignarRepartidorVista()
{
    $pedidos = Order::whereNull('repartidor_id')->get(); // Obtener pedidos sin repartidor asignado
    $repartidores = User::where('role', 'repartidor')->get(); // Obtener todos los repartidores

    return view('admin.repartidor.asignar_repartidor', compact('pedidos', 'repartidores'));
}

public function asignarRepartidor(Request $request, $id)
{
    $request->validate([
        'repartidor_id' => 'required|exists:users,id',
    ]);

    $pedido = Order::findOrFail($id);
    $pedido->repartidor_id = $request->repartidor_id;
    $pedido->save();

    return redirect()->route('admin.repartidor.asignar_repartidor_vista')
                     ->with('success', 'Repartidor asignado exitosamente.');
}

public function detallePedidoAdmin($id)
{
    // Cargar los detalles del pedido junto con los items, el producto y el repartidor
    $pedido = Order::with(['items.product', 'repartidor'])->findOrFail($id);
    return view('admin.pedido.detalle', compact('pedido'));
}

    

}

