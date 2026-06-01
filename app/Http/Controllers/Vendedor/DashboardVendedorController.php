<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\ProductoVenta;
use App\Models\Venta;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardVendedorController extends Controller
{
    public function index()
    {
        $totalVentas = Venta::where('user_id_vendedor', Auth::id())->sum('total');
        $totalPedidos = Pedido::where('user_id_vendedor', Auth::id())->count();
        $productosPublicados = ProductoVenta::where('user_id', Auth::id())->where('estado', 'disponible')->count();
        $productosAgotados = ProductoVenta::where('user_id', Auth::id())->where('estado', 'agotado')->count();

        $ventasPorMes = Venta::select(
            DB::raw('MONTH(fecha_venta) as mes'),
            DB::raw('YEAR(fecha_venta) as año'),
            DB::raw('SUM(total) as total')
        )
            ->where('user_id_vendedor', Auth::id())
            ->where('fecha_venta', '>=', now()->subMonths(6))
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        $pedidosRecientes = Pedido::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'detalles.producto.cultivo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('vendedor.dashboard.index', compact(
            'totalVentas', 'totalPedidos', 'productosPublicados', 'productosAgotados',
            'ventasPorMes', 'pedidosRecientes'
        ));
    }

    public function resumenEjecutivo()
    {
        $resultados = DB::select('CALL sp_costo_beneficio_vendedor(?)', [Auth::id()]);
        $resumen = $resultados[0] ?? null;

        return view('vendedor.dashboard.resumen', compact('resumen'));
    }
}
