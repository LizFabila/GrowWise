<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siembra;
use App\Models\Alerta;
use App\Models\Cosecha;
use App\Models\Modulo;
use App\Models\Cultivo;
use App\Models\Sensor;
use App\Models\Venta;
use App\Models\Pedido;
use App\Models\ProductoVenta;
use App\Models\Evaluacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // ============================================
        // ESTADÍSTICAS GLOBALES DEL SISTEMA
        // ============================================
        $stats = [
            'total_usuarios'      => User::count(),
            'total_vendedores'    => User::where('role', 'vendedor')->count(),
            'total_clientes'      => User::where('role', 'cliente')->count(),
            'total_admins'        => User::where('role', 'admin')->count(),
            'total_siembras'      => Siembra::count(),
            'siembras_activas'    => Siembra::where('estado', 'Activa')->count(),
            'alertas_pendientes'  => Alerta::where('estado', 'Pendiente')->count(),
            'alertas_criticas'    => Alerta::where('estado', 'Pendiente')->where('prioridad', 'Critica')->count(),
            'total_cultivos'      => Cultivo::count(),
            'cultivos_activos'    => Cultivo::where('activo', 1)->count(),
            'total_cosechas'      => Cosecha::count(),
            'kg_totales'          => Cosecha::sum('cantidad_kg'),
            'total_ventas'        => Venta::sum('total'),
            'total_pedidos'       => Pedido::count(),
            'pedidos_pendientes'  => Pedido::where('estado', 'pendiente')->count(),
            'total_modulos'       => Modulo::count(),
            'modulos_activos'     => Modulo::where('activo', 1)->count(),
            'total_sensores'      => Sensor::count(),
            'total_productos'     => ProductoVenta::count(),
            'productos_activos'   => ProductoVenta::where('estado', 'disponible')->count(),
        ];

        // Usuarios recientes
        $usuariosRecientes = User::orderBy('created_at', 'desc')->limit(8)->get();

        // Alertas críticas globales
        $alertasCriticas = Alerta::with(['user'])
            ->where('estado', 'Pendiente')
            ->whereIn('prioridad', ['Critica', 'Alta'])
            ->orderByRaw("FIELD(prioridad, 'Critica', 'Alta', 'Media', 'Baja')")
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Ventas por mes (últimos 6 meses)
        $ventasPorMes = Venta::select(
            DB::raw('MONTH(fecha_venta) as mes'),
            DB::raw('YEAR(fecha_venta) as anio'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as cantidad')
        )
            ->where('fecha_venta', '>=', now()->subMonths(6))
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        // Top vendedores
        $topVendedores = Venta::select(
            'users.id',
            DB::raw("CONCAT(users.nombre, ' ', users.apellido) as nombre"),
            'users.avatar',
            DB::raw('SUM(ventas.total) as total_ventas'),
            DB::raw('COUNT(ventas.id) as num_ventas')
        )
            ->join('users', 'ventas.user_id_vendedor', '=', 'users.id')
            ->groupBy('users.id', 'users.nombre', 'users.apellido', 'users.avatar')
            ->orderBy('total_ventas', 'desc')
            ->limit(5)
            ->get();

        // Siembras recientes globales
        $siembrasRecientes = Siembra::with(['cultivo', 'modulo', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($s) {
                $dias = now()->diffInDays($s->fecha_siembra);
                $total = $s->cultivo->dias_cosecha ?? 30;
                $s->progreso = min(round(($dias / $total) * 100), 100);
                return $s;
            });

        // Distribución de cosechas por cultivo
        $cosechasPorCultivo = Cosecha::select(
            'cultivos.nombre',
            DB::raw('SUM(cosechas.cantidad_kg) as total_kg'),
            DB::raw('COUNT(cosechas.id) as num_cosechas')
        )
            ->join('siembras', 'cosechas.siembra_id', '=', 'siembras.id')
            ->join('cultivos', 'siembras.cultivo_id', '=', 'cultivos.id')
            ->groupBy('cultivos.nombre')
            ->orderBy('total_kg', 'desc')
            ->limit(6)
            ->get();

        return view('Admin.dashboard.index', compact(
            'stats',
            'usuariosRecientes',
            'alertasCriticas',
            'ventasPorMes',
            'topVendedores',
            'siembrasRecientes',
            'cosechasPorCultivo'
        ));
    }
}
