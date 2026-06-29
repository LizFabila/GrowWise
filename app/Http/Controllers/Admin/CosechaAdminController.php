<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cosecha;
use App\Models\User;
use Illuminate\Http\Request;

class CosechaAdminController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::whereIn('role', ['vendedor', 'admin'])
            ->orderBy('nombre')->get();

        $usuarioSeleccionado = null;
        if ($request->filled('usuario_id')) {
            $usuarioSeleccionado = User::find($request->usuario_id);
        }

        $stats = [
            'total_cosechas'   => Cosecha::count(),
            'kg_totales'       => Cosecha::sum('cantidad_kg') ?? 0,
            'usuarios_activos' => Cosecha::distinct('user_id')->count('user_id'),
            'este_mes'         => Cosecha::whereMonth('fecha_cosecha', now()->month)
                ->whereYear('fecha_cosecha',  now()->year)->count(),
            'kg_mes'           => Cosecha::whereMonth('fecha_cosecha', now()->month)
                    ->whereYear('fecha_cosecha',  now()->year)
                    ->sum('cantidad_kg') ?? 0,
        ];

        $query = Cosecha::with(['siembra.cultivo', 'siembra.modulo', 'user']);

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }
        if ($request->filled('calidad')) {
            $query->where('calidad', $request->calidad);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_cosecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_cosecha', '<=', $request->hasta);
        }

        $cosechas = $query->orderBy('fecha_cosecha', 'desc')->paginate(15)->withQueryString();

        return view('Admin.cosechas.index', compact(
            'cosechas', 'stats', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
