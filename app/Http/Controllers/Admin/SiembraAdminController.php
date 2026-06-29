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
        $query = Siembra::with(['cultivo', 'modulo', 'user']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->whereHas('cultivo', fn($c) => $c->where('nombre', 'like', "%$b%"))
                    ->orWhereHas('modulo',  fn($m) => $m->where('nombre', 'like', "%$b%"))
                    ->orWhereHas('user',    fn($u) => $u->where('nombre', 'like', "%$b%")
                        ->orWhere('apellido', 'like', "%$b%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        $siembras = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total'         => Siembra::count(),
            'activas'       => Siembra::where('estado', 'Activa')->count(),
            'por_cosechar'  => Siembra::where('estado', 'Activa')
                ->whereNotNull('fecha_estimada_cosecha')
                ->where('fecha_estimada_cosecha', '<=', now()->addDays(15))
                ->count(),
            'con_problemas' => Siembra::where('estado', 'Problema')->count(),
        ];

        $usuarios = User::whereIn('role', ['vendedor', 'cliente', 'admin'])
            ->orderBy('nombre')->get();

        return view('Admin.siembras.index', compact('siembras', 'stats', 'usuarios'));
    }
}
