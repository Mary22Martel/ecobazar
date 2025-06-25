<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirige a los usuarios después de iniciar sesión según su rol.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Redirigir según el rol del usuario
        switch ($user->role) {
            case 'admin':
                return '/admin'; // Redirige al panel de administración para administradores
            case 'repartidor':
                return '/repartidor'; // Redirige al dashboard del repartidor
            case 'agricultor':
                return '/agricultor'; // Redirige al dashboard del agricultor
            case 'cliente':
                return '/tienda'; // Redirige a la página de inicio para clientes
            default:
                return '/tienda'; // Redirige a la página de inicio si el rol no coincide con ninguno
        }
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
