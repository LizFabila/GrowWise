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
        $productosAgotados = ProductoVenta::where('user_id', Auth::id())
            ->where(function($q) {
                $q->where('stock', 0)->orWhere('estado', 'agotado');
            })
            ->count();

        // Ventas por mes (últimos 6 meses)
        $ventasPorMes = Venta::select(
            DB::raw('MONTH(fecha_venta) as mes'),
            DB::raw('YEAR(fecha_venta) as año'),
            DB::raw('SUM(total) as total')
        )
            ->where('user_id_vendedor', Auth::id())
            ->where('fecha_venta', '>=', now()->subMonths(6))
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Datos de producción por cultivo (ingresos por cultivo)
        $ventasPorCultivo = DB::table('ventas as v')
            ->join('pedidos as p', 'v.pedido_id', '=', 'p.id')
            ->join('pedidos_detalle as pd', 'p.id', '=', 'pd.pedido_id')
            ->join('productos_venta as pv', 'pd.producto_venta_id', '=', 'pv.id')
            ->join('cultivos as c', 'pv.cultivo_id', '=', 'c.id')
            ->where('v.user_id_vendedor', Auth::id())
            ->select('c.nombre as cultivo', DB::raw('SUM(pd.subtotal) as total'))
            ->groupBy('c.id', 'c.nombre')
            ->get();

        $pedidosRecientes = Pedido::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'detalles.producto.cultivo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('vendedor.dashboard.index', compact(
            'totalVentas', 'totalPedidos', 'productosPublicados', 'productosAgotados',
            'ventasPorMes', 'pedidosRecientes', 'ventasPorCultivo'
        ));
    }

    public function resumenEjecutivo()
    {
        $user = Auth::id();

        // Datos reales de producción
        $produccion = [
            'Lechuga' => ['cantidad' => 12, 'precio' => 28.00, 'costo_semilla' => 11, 'dias_cosecha' => 55],
            'Espinaca' => ['cantidad' => 8, 'precio' => 22.00, 'costo_semilla' => 11, 'dias_cosecha' => 35],
            'Rábano' => ['cantidad' => 8, 'precio' => 20.00, 'costo_semilla' => 11, 'dias_cosecha' => 25],
            'Cilantro' => ['cantidad' => 5, 'precio' => 18.00, 'costo_semilla' => 11, 'dias_cosecha' => 15],
        ];

        $ingresoTotalPorCiclo = 0;
        $costoTotalPorCiclo = 0;

        foreach ($produccion as $cultivo => $datos) {
            $ingresoTotalPorCiclo += $datos['cantidad'] * $datos['precio'];
            $costoTotalPorCiclo += $datos['costo_semilla'];
        }

        $costoSustrato = 150;
        $costoTotalPorCiclo += $costoSustrato;

        $utilidadNetaPorCiclo = $ingresoTotalPorCiclo - $costoTotalPorCiclo;
        $inversionInicial = 25000;
        $ciclosNecesarios = ceil($inversionInicial / $utilidadNetaPorCiclo);
        $tiempoRecuperacionAnios = round($ciclosNecesarios * 45 / 365, 1);

        $resumen = (object)[
            'total_vendido' => $ingresoTotalPorCiclo,
            'inversion_total' => $costoTotalPorCiclo,
            'beneficio_neto' => $utilidadNetaPorCiclo,
            'rentabilidad' => ($utilidadNetaPorCiclo / $costoTotalPorCiclo) * 100,
            'roi' => ($utilidadNetaPorCiclo / $inversionInicial) * 100,
            'producto_mas_rentable' => 'Lechuga',
            'producto_mas_vendido' => 'Lechuga',
            'productos_publicados' => ProductoVenta::where('user_id', $user)->count(),
            'stock_restante' => ProductoVenta::where('user_id', $user)->sum('stock'),
            'productos_vendidos' => Venta::where('user_id_vendedor', $user)->count(),
            'produccion' => $produccion,
            'ciclos_necesarios' => $ciclosNecesarios,
            'tiempo_recuperacion' => $tiempoRecuperacionAnios,
            'inversion_inicial' => $inversionInicial,
        ];

        return view('vendedor.resumen', compact('resumen'));
    }
}
