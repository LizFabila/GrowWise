<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Siembra;
use App\Models\Cultivo;
use App\Models\LecturaSensor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reportes = Reporte::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $cultivosActivos = Cultivo::whereHas('siembras', function($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('estado', 'Activa');
        })->get();

        return view('Reportes.reportes', compact('reportes', 'cultivosActivos'));
    }

    public function generarPDF($cultivo_id = null)
    {
        $user = auth()->user();
        $nombreCultivo = null;

        if ($cultivo_id && $cultivo_id !== 'null' && $cultivo_id !== '') {
            $cultivo = Cultivo::find($cultivo_id);
            $nombreCultivo = $cultivo ? $cultivo->nombre : null;

            $siembras = Siembra::with(['cultivo', 'modulo'])
                ->where('user_id', $user->id)
                ->where('cultivo_id', $cultivo_id)
                ->where('estado', 'Activa')
                ->get();
        } else {
            $siembras = Siembra::with(['cultivo', 'modulo'])
                ->where('user_id', $user->id)
                ->where('estado', 'Activa')
                ->get();
        }

        if ($siembras->isEmpty()) {
            return back()->with('error', 'No hay siembras activas para generar el reporte.');
        }

        if ($nombreCultivo) {
            $nombreReporte = 'Reporte de ' . $nombreCultivo . ' - ' . now()->format('d/m/Y H:i');
        } else {
            $nombreReporte = 'Reporte de todos los cultivos - ' . now()->format('d/m/Y H:i');
        }

        $data = [];
        foreach ($siembras as $siembra) {
            $fechaSiembra = \Carbon\Carbon::parse($siembra->fecha_siembra)->startOfDay();
            $fechaEstimada = \Carbon\Carbon::parse($siembra->fecha_estimada_cosecha)->startOfDay();
            $hoy = \Carbon\Carbon::now()->startOfDay();

            // ========== PROGRESO CORRECTO ==========
            $diasTotales = $fechaSiembra->diffInDays($fechaEstimada);

            if ($hoy->greaterThanOrEqualTo($fechaSiembra)) {
                $diasTranscurridos = $fechaSiembra->diffInDays($hoy);
            } else {
                $diasTranscurridos = 0;
            }

            if ($diasTotales > 0) {
                $progreso = min(100, round(($diasTranscurridos / $diasTotales) * 100));
            } else {
                $progreso = 0;
            }

            // Si ya pasó la fecha de cosecha, progreso = 100%
            if ($hoy->greaterThanOrEqualTo($fechaEstimada)) {
                $progreso = 100;
            }

            // ========== FIN PROGRESO ==========

            $ultimaHumedad = 'N/A';
            try {
                $lectura = LecturaSensor::where('siembra_id', $siembra->id)
                    ->where('tipo', 'humedad_sustrato')
                    ->latest()
                    ->first();
                $ultimaHumedad = $lectura ? number_format($lectura->valor, 1) : 'N/A';
            } catch (\Exception $e) {
                $ultimaHumedad = 'N/A';
            }

            // ========== IMAGEN EN BASE64 PARA PDF ==========
            $imagenBase64 = null;
            $nombreCultivoImagen = $siembra->cultivo->nombre;

            $rutasImagenes = [
                'Lechuga' => public_path('images/cultivos/lechuga.jpg'),
                'Rábano' => public_path('images/cultivos/rabano.jpg'),
                'Cilantro' => public_path('images/cultivos/cilantro.jpg'),
                'Espinaca' => public_path('images/cultivos/espinacas.jpg'),
                'Zanahoria' => public_path('images/cultivos/zanahoria.jpg'),
            ];

            $rutaImagen = $rutasImagenes[$nombreCultivoImagen] ?? null;

            if ($rutaImagen && file_exists($rutaImagen) && filesize($rutaImagen) > 1000) {
                $tipoImagen = mime_content_type($rutaImagen);
                $contenido = file_get_contents($rutaImagen);
                $imagenBase64 = 'data:' . $tipoImagen . ';base64,' . base64_encode($contenido);
            }

            $data[] = [
                'siembra' => $siembra,
                'progreso' => $progreso,
                'humedad' => $ultimaHumedad,
                'dias_transcurridos' => $diasTranscurridos,
                'dias_totales' => $diasTotales,
                'fecha_siembra' => $fechaSiembra->format('d/m/Y'),
                'fecha_estimada' => $fechaEstimada->format('d/m/Y'),
                'precio_semilla' => $siembra->cultivo->precio_semilla ?? 0,
                'imagen_base64' => $imagenBase64,
            ];
        }

        $pdf = Pdf::loadView('Reportes.pdf.cultivo', [
            'data' => $data,
            'usuario' => $user,
            'fecha_generacion' => now()->format('d/m/Y H:i'),
            'nombre_reporte' => $nombreReporte,
        ]);

        $carpetaReportes = storage_path('app/reportes');
        if (!file_exists($carpetaReportes)) {
            mkdir($carpetaReportes, 0755, true);
        }

        $nombreArchivo = 'reporte_' . Str::slug($user->nombre) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        $rutaGuardado = $carpetaReportes . '/' . $nombreArchivo;

        $pdf->save($rutaGuardado);

        $tamañoKB = file_exists($rutaGuardado) ? round(filesize($rutaGuardado) / 1024, 2) : null;

        $reporte = Reporte::create([
            'user_id' => $user->id,
            'nombre' => $nombreReporte,
            'tipo' => 'pdf',
            'formato' => 'PDF',
            'archivo_url' => $nombreArchivo,
            'tamaño_kb' => $tamañoKB,
            'descargado' => 0,
        ]);

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte generado correctamente. Puedes descargarlo desde la lista.');
    }

    public function descargar($id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);

        if (empty($reporte->archivo_url)) {
            return redirect()->route('reportes.index')
                ->with('warning', 'Este reporte no tiene archivo asociado.');
        }

        $rutaArchivo = storage_path('app/reportes/' . $reporte->archivo_url);

        if (file_exists($rutaArchivo) && is_file($rutaArchivo)) {
            $reporte->descargado = 1;
            $reporte->save();

            return response()->download($rutaArchivo, $reporte->archivo_url, [
                'Content-Type' => 'application/pdf',
            ]);
        } else {
            return redirect()->route('reportes.index')
                ->with('warning', 'El archivo no se encuentra en el servidor.');
        }
    }

    public function ver($id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);

        if (empty($reporte->archivo_url)) {
            return redirect()->route('reportes.index')
                ->with('warning', 'Este reporte no tiene archivo asociado.');
        }

        $rutaArchivo = storage_path('app/reportes/' . $reporte->archivo_url);

        if (file_exists($rutaArchivo) && is_file($rutaArchivo)) {
            return response()->file($rutaArchivo, [
                'Content-Type' => 'application/pdf',
            ]);
        } else {
            return redirect()->route('reportes.index')
                ->with('warning', 'El archivo no se encuentra en el servidor.');
        }
    }

    public function generar(Request $request, $cultivo_id = null)
    {
        return $this->generarPDF($cultivo_id);
    }

    public function eliminar($id)
    {
        $reporte = Reporte::where('user_id', auth()->id())->findOrFail($id);

        if (!empty($reporte->archivo_url)) {
            $rutaArchivo = storage_path('app/reportes/' . $reporte->archivo_url);
            if (file_exists($rutaArchivo) && is_file($rutaArchivo)) {
                @unlink($rutaArchivo);
            }
        }

        $reporte->delete();

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte eliminado correctamente.');
    }
}
