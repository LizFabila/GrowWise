<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaAdminController extends Controller
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
            'total_ventas'  => DB::table('ventas')->count(),
            'ingresos_total'=> DB::table('ventas')->where('estado','completada')->sum('total') ?? 0,
            'este_mes'      => DB::table('ventas')->whereMonth('fecha_venta', now()->month)
                ->whereYear('fecha_venta', now()->year)->count(),
            'ingresos_mes'  => DB::table('ventas')->where('estado','completada')
                    ->whereMonth('fecha_venta', now()->month)
                    ->whereYear('fecha_venta', now()->year)
                    ->sum('total') ?? 0,
        ];

        $query = DB::table('ventas as v')
            ->join('users as uv', 'v.user_id_vendedor', '=', 'uv.id')
            ->join('users as uc', 'v.user_id_cliente',  '=', 'uc.id')
            ->join('pedidos as p', 'v.pedido_id', '=', 'p.id')
            ->select(
                'v.id', 'v.total', 'v.fecha_venta', 'v.estado', 'v.pedido_id',
                'uv.nombre as vendedor_nombre', 'uv.apellido as vendedor_apellido', 'uv.avatar as vendedor_avatar',
                'uc.nombre as cliente_nombre', 'uc.apellido as cliente_apellido',
                'p.estado as pedido_estado', 'p.total_final'
            )
            ->orderBy('v.fecha_venta', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('v.user_id_vendedor', $request->usuario_id);
        }
        if ($request->filled('estado')) {
            $query->where('v.estado', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('v.fecha_venta', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('v.fecha_venta', '<=', $request->hasta);
        }

        $ventas = $query->paginate(15)->withQueryString();

        return view('Admin.ventas.index', compact(
            'ventas', 'stats', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
