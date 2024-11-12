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
        // Comprueba si el usuario autenticado es administrador
        if (Auth::user()->is_admin) {
            return '/admin'; // Redirige al panel de Filament para administradores
        }

        return '/home'; // Redirige a la ruta predeterminada para otros usuarios
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
