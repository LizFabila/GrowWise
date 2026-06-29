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

        // El admin tiene acceso a TODO sin restricción de rol
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Para otros usuarios, verificar que su rol esté en la lista permitida
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Redirigir al lugar correcto según su rol real
        if ($user->isVendedor()) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes permiso para esa sección.');
        }

        if ($user->isCliente()) {
            return redirect()->route('cliente.tienda.index')
                ->with('error', 'No tienes permiso para esa sección.');
        }

        abort(403, 'Acceso denegado.');
    }
}
