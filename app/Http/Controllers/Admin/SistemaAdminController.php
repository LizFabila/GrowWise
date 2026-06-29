<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siembra;
use App\Models\User;
use Illuminate\Http\Request;

class SiembraAdminController extends Controller
{
    public function index(Request $request)
    {
        // Lista de usuarios para el selector
        $usuarios = User::whereIn('role', ['vendedor', 'admin'])
            ->orderBy('nombre')->get();

        // Usuario seleccionado
        $usuarioSeleccionado = null;
        if ($request->filled('usuario_id')) {
            $usuarioSeleccionado = User::find($request->usuario_id);
        }

        // Query base — siempre global para stats
        $queryBase = Siembra::query();

        // Stats globales (sin filtro de usuario)
        $stats = [
            'total'         => Siembra::count(),
            'activas'       => Siembra::where('estado', 'Activa')->count(),
            'por_cosechar'  => Siembra::where('estado', 'Activa')
                ->whereNotNull('fecha_estimada_cosecha')
                ->where('fecha_estimada_cosecha', '<=', now()->addDays(15))
                ->count(),
            'con_problemas' => Siembra::where('estado', 'Problema')->count(),
        ];

        // Query con filtros
        $query = Siembra::with(['cultivo', 'modulo', 'user']);

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->whereHas('cultivo', fn($c) => $c->where('nombre', 'like', "%$b%"))
                    ->orWhereHas('modulo',  fn($m) => $m->where('nombre', 'like', "%$b%"));
            });
        }

        $siembras = $query->orderBy('fecha_siembra', 'desc')->paginate(15)->withQueryString();

        return view('Admin.siembras.index', compact(
            'siembras', 'stats', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
