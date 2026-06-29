<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoAdminController extends Controller
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
            'total'      => DB::table('pedidos')->count(),
            'pendientes' => DB::table('pedidos')->where('estado', 'pendiente')->count(),
            'enviados'   => DB::table('pedidos')->where('estado', 'enviado')->count(),
            'entregados' => DB::table('pedidos')->where('estado', 'entregado')->count(),
        ];

        $query = DB::table('pedidos as p')
            ->join('users as uv', 'p.user_id_vendedor', '=', 'uv.id')
            ->join('users as uc', 'p.user_id_cliente',  '=', 'uc.id')
            ->select(
                'p.id', 'p.subtotal', 'p.impuesto', 'p.total_final',
                'p.estado', 'p.fecha_pedido', 'p.notas',
                'uv.nombre as vendedor_nombre', 'uv.apellido as vendedor_apellido',
                'uv.id as vendedor_id', 'uv.avatar as vendedor_avatar',
                'uc.nombre as cliente_nombre', 'uc.apellido as cliente_apellido'
            )
            ->orderBy('p.fecha_pedido', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('p.user_id_vendedor', $request->usuario_id);
        }
        if ($request->filled('estado')) {
            $query->where('p.estado', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('p.fecha_pedido', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('p.fecha_pedido', '<=', $request->hasta);
        }

        $pedidos = $query->paginate(15)->withQueryString();

        return view('Admin.pedidos.index', compact(
            'pedidos', 'stats', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
