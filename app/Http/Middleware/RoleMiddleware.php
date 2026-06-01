<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Redirigir según el rol en lugar de mostrar error 403
        if ($user->isCliente()) {
            return redirect()->route('cliente.tienda.index');
        }

        if ($user->isVendedor() || $user->isAdmin()) {
            return redirect()->route('vendedor.dashboard');
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
