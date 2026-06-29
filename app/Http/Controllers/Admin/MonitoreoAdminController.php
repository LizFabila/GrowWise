<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoreoAdminController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::whereIn('role', ['vendedor', 'admin'])
            ->orderBy('nombre')->get();

        $usuarioSeleccionado = null;
        if ($request->filled('usuario_id')) {
            $usuarioSeleccionado = User::find($request->usuario_id);
        }

        $statsGlobal = [
            'total_modulos'    => Modulo::count(),
            'modulos_activos'  => Modulo::where('activo', 1)->count(),
            'total_sensores'   => DB::table('sensores')->count(),
            'lecturas_hoy'     => DB::table('lecturas_sensores')
                ->whereDate('created_at', today())->count(),
        ];

        $query = Modulo::with(['user', 'sensores']);

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        $modulos = $query->orderBy('activo', 'desc')
            ->orderBy('nombre')
            ->get();

        // Últimas 40 lecturas con info completa
        $lecturasQuery = DB::table('lecturas_sensores as ls')
            ->join('sensores as s',  'ls.sensor_id', '=', 's.id')
            ->join('modulos as m',   's.modulo_id',  '=', 'm.id')
            ->join('users as u',     'm.user_id',    '=', 'u.id')
            ->select(
                'ls.valor',
                'ls.created_at',
                's.nombre as sensor_nombre',
                's.tipo',
                's.unidad',
                'm.nombre as modulo_nombre',
                'm.id as modulo_id',
                'u.id as user_id',
                DB::raw("CONCAT(u.nombre,' ',u.apellido) as usuario")
            )
            ->orderBy('ls.created_at', 'desc');

        if ($request->filled('usuario_id')) {
            $lecturasQuery->where('m.user_id', $request->usuario_id);
        }

        $lecturasRecientes = $lecturasQuery->limit(40)->get();

        return view('Admin.monitoreo.index', compact(
            'modulos', 'statsGlobal', 'lecturasRecientes', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
