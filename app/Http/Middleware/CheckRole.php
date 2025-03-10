<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verificar si el usuario tiene el rol correcto
        if (Auth::user()->rol !== $role) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta página.');
        }

        // Continuar con la solicitud
        return $next($request);
    }
}
