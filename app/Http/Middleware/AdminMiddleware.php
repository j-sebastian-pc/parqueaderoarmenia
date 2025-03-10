<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder');
        }
        
        if (auth()->user()->rol !== 'admin') {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        return $next($request);
    }
}