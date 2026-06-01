<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ===========================================
        // DATOS REALES DE PRODUCCIÓN POR CULTIVO
        // ===========================================
        $produccion = [
            'Lechuga' => ['cantidad' => 12, 'precio' => 28.00, 'ingreso' => 336.00, 'dias_cosecha' => 55],
            'Espinaca' => ['cantidad' => 8, 'precio' => 22.00, 'ingreso' => 176.00, 'dias_cosecha' => 35],
            'Rábano' => ['cantidad' => 8, 'precio' => 20.00, 'ingreso' => 160.00, 'dias_cosecha' => 25],
            'Cilantro' => ['cantidad' => 5, 'precio' => 18.00, 'ingreso' => 90.00, 'dias_cosecha' => 15],
        ];

        $ingresoTotalPorCiclo = array_sum(array_column($produccion, 'ingreso'));
        $costoSemillas = 44; // 4 cultivos x $11
        $costoSustrato = 150;
        $costoTotalPorCiclo = $costoSemillas + $costoSustrato;
        $utilidadNetaPorCiclo = $ingresoTotalPorCiclo - $costoTotalPorCiclo;

        // ===========================================
        // ESTADÍSTICAS GENERALES
        // ===========================================
        $stats = [
            'total_cultivos' => Cultivo::where('activo', 1)->count(),
            'siembras_activas' => Siembra::where('user_id', $user->id)->where('estado', 'Activa')->count(),
            'alertas_pendientes' => Alerta::where('user_id', $user->id)->where('estado', 'Pendiente')->count(),
            'total_siembras' => Siembra::where('user_id', $user->id)->count(),
            'cosechas_mes' => Cosecha::where('user_id', $user->id)
                ->whereMonth('fecha_cosecha', now()->month)
                ->sum('cantidad_kg'),
            'inversion_total' => $this->calcularInversionTotal($user->id),
            'ingresos_estimados' => Cosecha::where('user_id', $user->id)
                    ->whereMonth('fecha_cosecha', now()->month)
                    ->sum('cantidad_kg') * 5,
            'ventas_totales' => Venta::where('user_id_vendedor', $user->id)->sum('total'),
            'pedidos_recibidos' => Pedido::where('user_id_vendedor', $user->id)->count(),
            'productos_publicados' => ProductoVenta::where('user_id', $user->id)->where('estado', 'disponible')->count(),
            'total_cosechas' => Cosecha::where('user_id', $user->id)->count(),
            'ingreso_por_ciclo' => $ingresoTotalPorCiclo,
            'costo_por_ciclo' => $costoTotalPorCiclo,
            'utilidad_neta_por_ciclo' => $utilidadNetaPorCiclo,
        ];

        // ===========================================
        // CÁLCULOS DE RENTABILIDAD
        // ===========================================
        $inversionInicial = 25000;
        $ciclosNecesarios = ceil($inversionInicial / $utilidadNetaPorCiclo);
        $tiempoRecuperacionAnios = round($ciclosNecesarios * 45 / 365, 1);

        $stats['inversion_inicial'] = $inversionInicial;
        $stats['ciclos_necesarios'] = $ciclosNecesarios;
        $stats['tiempo_recuperacion'] = $tiempoRecuperacionAnios;
        $stats['produccion'] = $produccion;

        // ===========================================
        // SIEMBRAS RECIENTES (con progreso real)
        // ===========================================
        $siembrasRecientes = Siembra::with(['cultivo', 'modulo'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($siembra) {
                $diasTranscurridos = now()->diffInDays($siembra->fecha_siembra);
                $diasTotales = $siembra->cultivo->dias_cosecha ?? 30;
                $siembra->progreso = min(round(($diasTranscurridos / $diasTotales) * 100), 100);
                return $siembra;
            });

        // ===========================================
        // ALERTAS RECIENTES
        // ===========================================
        $alertasRecientes = Alerta::with(['modulo'])
            ->where('user_id', $user->id)
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ===========================================
        // MONITOREO AMBIENTAL (datos reales de sensores)
        // ===========================================
        $modulo = Modulo::where('user_id', $user->id)->first();

        // Inicializar monitoreo con valores por defecto
        $monitoreo = [
            'temperatura' => '--',
            'luz' => '--',
            'humedad_charola1' => '--',
            'humedad_charola2' => '--',
            'humedad_charola3' => '--',
            'humedad_charola4' => '--',
        ];

        if ($modulo) {
            // Temperatura
            $tempSensor = Sensor::where('modulo_id', $modulo->id)
                ->where('tipo', 'Temperatura')
                ->first();
            if ($tempSensor && $tempSensor->ultima_lectura) {
                $monitoreo['temperatura'] = $tempSensor->ultima_lectura;
            }

            // Luz
            $luzSensor = Sensor::where('modulo_id', $modulo->id)
                ->where('tipo', 'Luz')
                ->first();
            if ($luzSensor && $luzSensor->ultima_lectura) {
                $monitoreo['luz'] = $luzSensor->ultima_lectura;
            }

            // Buscar sensores de humedad por nombre específico
            $sensoresHumedad = Sensor::where('modulo_id', $modulo->id)
                ->where('tipo', 'Humedad')
                ->get();

            // Mapeo de sensores por cultivo
            foreach ($sensoresHumedad as $sensor) {
                $nombre = $sensor->nombre;
                if (str_contains($nombre, 'Rábano') || str_contains($nombre, 'Charola 1')) {
                    $monitoreo['humedad_charola1'] = $sensor->ultima_lectura ?? '--';
                } elseif (str_contains($nombre, 'Lechuga') || str_contains($nombre, 'Charola 2')) {
                    $monitoreo['humedad_charola2'] = $sensor->ultima_lectura ?? '--';
                } elseif (str_contains($nombre, 'Espinaca') || str_contains($nombre, 'Charola 3')) {
                    $monitoreo['humedad_charola3'] = $sensor->ultima_lectura ?? '--';
                } elseif (str_contains($nombre, 'Cilantro') || str_contains($nombre, 'Charola 4')) {
                    $monitoreo['humedad_charola4'] = $sensor->ultima_lectura ?? '--';
                }
            }

            // Si no se encontraron sensores por nombre, intentar buscar por ubicación
            if ($monitoreo['humedad_charola1'] == '--') {
                $sensor = Sensor::where('modulo_id', $modulo->id)
                    ->where('tipo', 'Humedad')
                    ->where('ubicacion', 'like', '%Charola 1%')
                    ->first();
                if ($sensor) $monitoreo['humedad_charola1'] = $sensor->ultima_lectura ?? '--';
            }
            if ($monitoreo['humedad_charola2'] == '--') {
                $sensor = Sensor::where('modulo_id', $modulo->id)
                    ->where('tipo', 'Humedad')
                    ->where('ubicacion', 'like', '%Charola 2%')
                    ->first();
                if ($sensor) $monitoreo['humedad_charola2'] = $sensor->ultima_lectura ?? '--';
            }
            if ($monitoreo['humedad_charola3'] == '--') {
                $sensor = Sensor::where('modulo_id', $modulo->id)
                    ->where('tipo', 'Humedad')
                    ->where('ubicacion', 'like', '%Charola 3%')
                    ->first();
                if ($sensor) $monitoreo['humedad_charola3'] = $sensor->ultima_lectura ?? '--';
            }
            if ($monitoreo['humedad_charola4'] == '--') {
                $sensor = Sensor::where('modulo_id', $modulo->id)
                    ->where('tipo', 'Humedad')
                    ->where('ubicacion', 'like', '%Charola 4%')
                    ->first();
                if ($sensor) $monitoreo['humedad_charola4'] = $sensor->ultima_lectura ?? '--';
            }
        }

        // ===========================================
        // EVALUACIONES RECIENTES
        // ===========================================
        $evaluacionesRecientes = DB::table('evaluaciones as e')
            ->join('siembras as s', 'e.siembra_id', '=', 's.id')
            ->join('cultivos as c', 's.cultivo_id', '=', 'c.id')
            ->where('s.user_id', $user->id)
            ->orderBy('e.fecha_evaluacion', 'desc')
            ->limit(5)
            ->select('e.*', 'c.nombre as cultivo_nombre')
            ->get();

        return view('Dashboard.dashboard', compact(
            'stats',
            'siembrasRecientes',
            'alertasRecientes',
            'monitoreo',
            'evaluacionesRecientes'
        ));
    }

    /**
     * Calcular inversión total estimada
     */
    private function calcularInversionTotal($userId)
    {
        $costoSemillas = 11; // Costo real por semilla
        $costoSustrato = 150; // Costo real de sustrato por ciclo
        $costoLuzSemana = 57;

        $siembras = Siembra::where('user_id', $userId)->count();
        $fechaMin = Siembra::where('user_id', $userId)->min('fecha_siembra');
        $semanasActivas = max(1, ceil(now()->diffInDays($fechaMin ?? now()) / 7));

        $totalSemillas = $siembras * $costoSemillas;
        $totalSustrato = $costoSustrato;
        $totalLuz = $semanasActivas * $costoLuzSemana;

        return $totalSemillas + $totalSustrato + $totalLuz;
    }
}
