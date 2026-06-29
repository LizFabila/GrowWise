<?php

namespace App\Http\Controllers;

use App\Models\Siembra;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $alertas = [];

        // Obtener TODAS las siembras activas con fecha de cosecha futura
        $siembras = Siembra::with('cultivo')
            ->where('user_id', $user->id)
            ->where('estado', 'Activa')
            ->whereNotNull('fecha_estimada_cosecha')
            ->whereDate('fecha_estimada_cosecha', '>=', now())
            ->orderBy('fecha_estimada_cosecha', 'asc')
            ->get();

        foreach ($siembras as $siembra) {
            $fechaEstimada = Carbon::parse($siembra->fecha_estimada_cosecha)->startOfDay();
            $fechaSiembra = Carbon::parse($siembra->fecha_siembra)->startOfDay();
            $hoy = Carbon::now()->startOfDay();

            // ========== DÍAS RESTANTES ==========
            $diasRestantes = (int) $hoy->diffInDays($fechaEstimada, false);

            // ========== PROGRESO CORRECTO (mismo que en vista general) ==========
            // Días totales del ciclo (desde siembra hasta cosecha estimada)
            $diasTotales = $fechaSiembra->diffInDays($fechaEstimada);

            // Días transcurridos (desde siembra hasta hoy)
            if ($hoy->greaterThanOrEqualTo($fechaSiembra)) {
                $diasTranscurridos = $fechaSiembra->diffInDays($hoy);
            } else {
                $diasTranscurridos = 0;
            }

            // Calcular progreso (no puede superar 100%)
            if ($diasTotales > 0) {
                $progreso = min(100, round(($diasTranscurridos / $diasTotales) * 100));
            } else {
                $progreso = 0;
            }

            // Si la fecha estimada ya pasó, progreso = 100%
            if ($hoy->greaterThanOrEqualTo($fechaEstimada)) {
                $progreso = 100;
            }

            // Determinar prioridad según días restantes
            if ($diasRestantes <= 1) {
                $prioridad = 'Crítica';
                $color = 'critica';
                $badgeColor = 'critica';
                $icono = 'fa-exclamation-triangle';
            } elseif ($diasRestantes <= 3) {
                $prioridad = 'Alta';
                $color = 'alta';
                $badgeColor = 'alta';
                $icono = 'fa-bell';
            } elseif ($diasRestantes <= 7) {
                $prioridad = 'Media';
                $color = 'media';
                $badgeColor = 'media';
                $icono = 'fa-clock';
            } elseif ($diasRestantes <= 14) {
                $prioridad = 'Baja';
                $color = 'baja';
                $badgeColor = 'baja';
                $icono = 'fa-calendar-alt';
            } else {
                $prioridad = 'Programada';
                $color = 'programada';
                $badgeColor = 'programada';
                $icono = 'fa-calendar-check';
            }

            $alertas[] = [
                'id' => $siembra->id,
                'cultivo' => $siembra->cultivo->nombre,
                'dias_restantes' => $diasRestantes,
                'fecha_estimada' => $fechaEstimada->format('d/m/Y'),
                'prioridad' => $prioridad,
                'color' => $color,
                'badgeColor' => $badgeColor,
                'icono' => $icono,
                'progreso' => $progreso,
                'modulo' => $siembra->modulo->nombre ?? 'N/A',
                'charola' => $siembra->charola,
                'fecha_siembra' => $fechaSiembra->format('d/m/Y'),
                'dias_totales' => $diasTotales,
                'dias_transcurridos' => $diasTranscurridos,
            ];
        }

        // Estadísticas
        $stats = [
            'total' => count($alertas),
            'criticas' => count(array_filter($alertas, fn($a) => $a['prioridad'] == 'Crítica')),
            'altas' => count(array_filter($alertas, fn($a) => $a['prioridad'] == 'Alta')),
            'medias' => count(array_filter($alertas, fn($a) => $a['prioridad'] == 'Media')),
        ];

        return view('Alertas.alertas', compact('alertas', 'stats'));
    }
}
