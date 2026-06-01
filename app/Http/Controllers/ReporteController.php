<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Cultivo;
use App\Models\Siembra;
use App\Models\Cosecha;
use App\Models\Alerta;
use App\Models\Sensor;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $reportes = Reporte::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Reporte::where('user_id', $user->id)->count(),
            'pendientes' => 0,
            'descargados' => Reporte::where('user_id', $user->id)->where('descargado', true)->count(),
            'formato_preferido' => $this->obtenerFormatoPreferido($user->id),
        ];

        return view('Reportes.reportes', compact('reportes', 'stats'));
    }

    public function generar(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'periodo' => 'required|string',
            'formato' => 'required|in:PDF,Excel,CSV',
        ]);

        // Calcular fechas según el período
        $fechas = $this->calcularFechasPeriodo($request->periodo);

        // Obtener datos reales según el tipo de reporte
        $datos = $this->obtenerDatosReporte($request->tipo, $fechas['inicio'], $fechas['fin']);

        // Generar contenido del reporte
        $contenido = $this->generarContenidoReporte($request->tipo, $datos, $fechas);

        // Generar nombre del reporte
        $nombre = 'Reporte_' . ucfirst($request->tipo) . '_' . now()->format('Ymd_His');

        // Guardar archivo (simulado por ahora, en producción se generaría PDF/Excel)
        $tamañoKb = $this->guardarReporte($nombre, $contenido, $request->formato);

        // Crear el reporte en la base de datos
        $reporte = Reporte::create([
            'user_id' => auth()->id(),
            'nombre' => 'Reporte de ' . ucfirst($request->tipo) . ' - ' . now()->format('d/m/Y H:i'),
            'tipo' => $request->tipo,
            'periodo_inicio' => $fechas['inicio'],
            'periodo_fin' => $fechas['fin'],
            'formato' => $request->formato,
            'archivo_url' => '#',
            'tamaño_kb' => $tamañoKb,
            'parametros' => $request->all(),
            'descargado' => false,
        ]);

        // Guardar el contenido en sesión para la descarga
        session()->flash('reporte_contenido', $contenido);
        session()->flash('reporte_nombre', $nombre);
        session()->flash('reporte_formato', $request->formato);

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte generado correctamente: ' . $reporte->nombre);
    }

    public function descargar(string $id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);

        // Regenerar el contenido del reporte
        $datos = $this->obtenerDatosReporte($reporte->tipo, $reporte->periodo_inicio, $reporte->periodo_fin);
        $contenido = $this->generarContenidoReporte($reporte->tipo, $datos, [
            'inicio' => $reporte->periodo_inicio,
            'fin' => $reporte->periodo_fin
        ]);

        $nombre = 'Reporte_' . ucfirst($reporte->tipo) . '_' . now()->format('Ymd_His');

        // Descargar según formato
        if ($reporte->formato == 'CSV') {
            return $this->descargarCSV($contenido, $nombre);
        } elseif ($reporte->formato == 'Excel') {
            return $this->descargarExcel($contenido, $nombre);
        } else {
            return $this->descargarPDF($contenido, $nombre);
        }
    }

    private function obtenerDatosReporte($tipo, $fechaInicio, $fechaFin)
    {
        $userId = auth()->id();

        switch ($tipo) {
            case 'cultivos':
                return Cultivo::where('activo', 1)
                    ->orderBy('nombre')
                    ->get();

            case 'siembras':
                return Siembra::where('user_id', $userId)
                    ->when($fechaInicio && $fechaFin, function($query) use ($fechaInicio, $fechaFin) {
                        return $query->whereBetween('fecha_siembra', [$fechaInicio, $fechaFin]);
                    })
                    ->with(['cultivo', 'modulo'])
                    ->orderBy('created_at', 'desc')
                    ->get();

            case 'cosechas':
                return Cosecha::where('user_id', $userId)
                    ->when($fechaInicio && $fechaFin, function($query) use ($fechaInicio, $fechaFin) {
                        return $query->whereBetween('fecha_cosecha', [$fechaInicio, $fechaFin]);
                    })
                    ->with(['siembra.cultivo'])
                    ->orderBy('fecha_cosecha', 'desc')
                    ->get();

            case 'monitoreo':
                $modulo = Modulo::where('user_id', $userId)->first();
                if ($modulo) {
                    return Sensor::where('modulo_id', $modulo->id)
                        ->with(['lecturas' => function($query) use ($fechaInicio, $fechaFin) {
                            $query->whereBetween('created_at', [$fechaInicio, $fechaFin])
                                ->orderBy('created_at', 'desc')
                                ->limit(50);
                        }])
                        ->get();
                }
                return collect();

            case 'alertas':
                return Alerta::where('user_id', $userId)
                    ->when($fechaInicio && $fechaFin, function($query) use ($fechaInicio, $fechaFin) {
                        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

            default:
                return collect();
        }
    }

    private function generarContenidoReporte($tipo, $datos, $fechas)
    {
        $user = auth()->user();
        $fechaGeneracion = now()->format('d/m/Y H:i:s');

        $contenido = [];

        // Cabecera del reporte
        $contenido[] = "========================================";
        $contenido[] = "GROWWISE - SISTEMA DE GESTIÓN DE CULTIVOS";
        $contenido[] = "========================================";
        $contenido[] = "";
        $contenido[] = "Reporte: " . strtoupper($tipo);
        $contenido[] = "Generado por: " . $user->nombre . " " . $user->apellido;
        $contenido[] = "Fecha de generación: " . $fechaGeneracion;
        $contenido[] = "Período: " . ($fechas['inicio'] ?? 'Todo') . " - " . ($fechas['fin'] ?? 'Todo');
        $contenido[] = "";
        $contenido[] = "----------------------------------------";
        $contenido[] = "";

        switch ($tipo) {
            case 'cultivos':
                $contenido[] = "LISTADO DE CULTIVOS DISPONIBLES";
                $contenido[] = "----------------------------------------";
                $contenido[] = "";
                $contenido[] = sprintf("%-5s | %-25s | %-15s | %-10s", "ID", "NOMBRE", "TIPO", "DÍAS COSECHA");
                $contenido[] = str_repeat("-", 65);

                foreach ($datos as $cultivo) {
                    $contenido[] = sprintf("%-5d | %-25s | %-15s | %-10d",
                        $cultivo->id,
                        $cultivo->nombre,
                        $cultivo->tipo,
                        $cultivo->dias_cosecha ?? 'N/A'
                    );
                }

                $contenido[] = "";
                $contenido[] = "Total de cultivos: " . $datos->count();
                break;

            case 'siembras':
                $contenido[] = "LISTADO DE SIEMBRAS";
                $contenido[] = "----------------------------------------";
                $contenido[] = "";
                $contenido[] = sprintf("%-5s | %-20s | %-12s | %-15s | %-10s",
                    "ID", "CULTIVO", "FECHA SIEMBRA", "MÓDULO", "ESTADO");
                $contenido[] = str_repeat("-", 75);

                foreach ($datos as $siembra) {
                    $contenido[] = sprintf("%-5d | %-20s | %-12s | %-15s | %-10s",
                        $siembra->id,
                        $siembra->cultivo->nombre ?? 'N/A',
                        $siembra->fecha_siembra->format('d/m/Y'),
                        $siembra->modulo->nombre ?? 'N/A',
                        $siembra->estado
                    );
                }

                $contenido[] = "";
                $contenido[] = "Total de siembras: " . $datos->count();
                $contenido[] = "Siembras activas: " . $datos->where('estado', 'Activa')->count();
                $contenido[] = "Siembras completadas: " . $datos->where('estado', 'Completada')->count();
                break;

            case 'cosechas':
                $contenido[] = "LISTADO DE COSECHAS";
                $contenido[] = "----------------------------------------";
                $contenido[] = "";
                $contenido[] = sprintf("%-5s | %-20s | %-12s | %-10s | %-10s",
                    "ID", "CULTIVO", "FECHA COSECHA", "CANTIDAD(kg)", "CALIDAD");
                $contenido[] = str_repeat("-", 70);

                $totalKg = 0;
                foreach ($datos as $cosecha) {
                    $totalKg += $cosecha->cantidad_kg;
                    $contenido[] = sprintf("%-5d | %-20s | %-12s | %-10.2f | %-10s",
                        $cosecha->id,
                        $cosecha->siembra->cultivo->nombre ?? 'N/A',
                        $cosecha->fecha_cosecha->format('d/m/Y'),
                        $cosecha->cantidad_kg,
                        $cosecha->calidad
                    );
                }

                $contenido[] = "";
                $contenido[] = "Total de cosechas: " . $datos->count();
                $contenido[] = "Total cosechado: " . number_format($totalKg, 2) . " kg";
                $contenido[] = "Promedio por cosecha: " . number_format($datos->avg('cantidad_kg'), 2) . " kg";
                break;

            case 'monitoreo':
                $contenido[] = "REGISTRO DE MONITOREO";
                $contenido[] = "----------------------------------------";
                $contenido[] = "";

                foreach ($datos as $sensor) {
                    $contenido[] = "Sensor: " . $sensor->nombre;
                    $contenido[] = "Tipo: " . $sensor->tipo;
                    $contenido[] = "Ubicación: " . $sensor->ubicacion;
                    $contenido[] = "Última lectura: " . ($sensor->ultima_lectra ?? 'Sin datos') . " " . $sensor->unidad;
                    $contenido[] = "Última actualización: " . ($sensor->ultima_lectura_at ? \Carbon\Carbon::parse($sensor->ultima_lectura_at)->format('d/m/Y H:i') : 'Nunca');
                    $contenido[] = "";
                    $contenido[] = "Historial de lecturas (últimas):";
                    $contenido[] = sprintf("%-20s | %-10s", "FECHA/HORA", "VALOR");
                    $contenido[] = str_repeat("-", 35);

                    foreach ($sensor->lecturas as $lectura) {
                        $contenido[] = sprintf("%-20s | %-10.2f%s",
                            \Carbon\Carbon::parse($lectura->created_at)->format('d/m/Y H:i'),
                            $lectura->valor,
                            $sensor->unidad
                        );
                    }
                    $contenido[] = "";
                    $contenido[] = "----------------------------------------";
                    $contenido[] = "";
                }
                break;

            case 'alertas':
                $contenido[] = "LISTADO DE ALERTAS";
                $contenido[] = "----------------------------------------";
                $contenido[] = "";
                $contenido[] = sprintf("%-5s | %-30s | %-10s | %-15s | %-20s",
                    "ID", "TÍTULO", "PRIORIDAD", "ESTADO", "FECHA");
                $contenido[] = str_repeat("-", 90);

                foreach ($datos as $alerta) {
                    $contenido[] = sprintf("%-5d | %-30s | %-10s | %-15s | %-20s",
                        $alerta->id,
                        substr($alerta->titulo, 0, 28),
                        $alerta->prioridad,
                        $alerta->estado,
                        $alerta->created_at->format('d/m/Y H:i')
                    );
                }

                $contenido[] = "";
                $contenido[] = "Total de alertas: " . $datos->count();
                $contenido[] = "Alertas pendientes: " . $datos->where('estado', 'Pendiente')->count();
                $contenido[] = "Alertas resueltas: " . $datos->where('estado', 'Resuelta')->count();
                break;
        }

        $contenido[] = "";
        $contenido[] = "========================================";
        $contenido[] = "Fin del reporte - GrowWise";
        $contenido[] = "========================================";

        return implode("\n", $contenido);
    }

    private function guardarReporte($nombre, $contenido, $formato)
    {
        // Simular tamaño del archivo
        $tamaño = strlen($contenido) / 1024;

        // En producción, aquí se guardaría el archivo en storage
        // Storage::disk('public')->put("reportes/{$nombre}.{$formato}", $contenido);

        return round($tamaño, 2);
    }

    private function descargarCSV($contenido, $nombre)
    {
        return response($contenido)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$nombre}.csv");
    }

    private function descargarExcel($contenido, $nombre)
    {
        return response($contenido)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename={$nombre}.xls");
    }

    private function descargarPDF($contenido, $nombre)
    {
        // Para PDF necesitas instalar dompdf o similar
        // Por ahora enviamos como texto plano
        return response($contenido)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename={$nombre}.txt");
    }

    public function verPdf(string $id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);

        $datos = $this->obtenerDatosReporte($reporte->tipo, $reporte->periodo_inicio, $reporte->periodo_fin);
        $contenido = $this->generarContenidoReporte($reporte->tipo, $datos, [
            'inicio' => $reporte->periodo_inicio,
            'fin' => $reporte->periodo_fin
        ]);

        return response($contenido)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "inline; filename=reporte_{$reporte->tipo}.txt");
    }

    public function destroy(string $id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);
        $reporte->delete();

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte eliminado correctamente');
    }

    private function calcularFechasPeriodo($periodo)
    {
        $fin = now();
        $inicio = now();

        switch ($periodo) {
            case 'semana':
                $inicio = now()->subDays(7);
                break;
            case 'mes':
                $inicio = now()->subMonth();
                break;
            case 'trimestre':
                $inicio = now()->subMonths(3);
                break;
            case 'año':
                $inicio = now()->subYear();
                break;
            default:
                $inicio = now()->subMonth();
                break;
        }

        return [
            'inicio' => $inicio->format('Y-m-d'),
            'fin' => $fin->format('Y-m-d'),
        ];
    }

    private function obtenerFormatoPreferido($userId)
    {
        $formato = Reporte::where('user_id', $userId)
            ->select('formato', DB::raw('count(*) as total'))
            ->groupBy('formato')
            ->orderBy('total', 'desc')
            ->first();

        return $formato ? $formato->formato : 'PDF';
    }
}
