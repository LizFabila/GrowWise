<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoAdminController extends Controller
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
            'total'       => DB::table('productos_venta')->count(),
            'disponibles' => DB::table('productos_venta')->where('estado', 'disponible')->count(),
            'agotados'    => DB::table('productos_venta')->where('estado', 'agotado')->count(),
        ];

        $query = DB::table('productos_venta as pv')
            ->join('users as u',    'pv.user_id',    '=', 'u.id')
            ->join('cultivos as c', 'pv.cultivo_id', '=', 'c.id')
            ->select(
                'pv.id', 'pv.cantidad', 'pv.unidad', 'pv.precio_unitario',
                'pv.stock', 'pv.estado', 'pv.created_at',
                'u.nombre as vendedor_nombre', 'u.apellido as vendedor_apellido',
                'u.avatar as vendedor_avatar', 'u.id as vendedor_id',
                'c.nombre as cultivo_nombre', 'c.tipo as cultivo_tipo'
            )
            ->orderBy('pv.created_at', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('pv.user_id', $request->usuario_id);
        }
        if ($request->filled('estado')) {
            $query->where('pv.estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $query->where('c.nombre', 'like', '%'.$request->buscar.'%');
        }

        $productos = $query->paginate(15)->withQueryString();

        return view('Admin.productos.index', compact(
            'productos', 'stats', 'usuarios', 'usuarioSeleccionado'
        ));
    }
}
