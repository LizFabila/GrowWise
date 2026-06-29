<?php

namespace App\Http\Controllers;

use App\Models\Siembra;
use App\Models\LecturaSensor;
use Illuminate\Http\Request;

class MonitoreoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Mostrar TODAS las siembras (activas y completadas)
        $siembras = Siembra::with(['cultivo', 'modulo'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($siembras as $siembra) {
            // Buscar la última lectura asociada a esta siembra
            $ultimaLectura = LecturaSensor::where('siembra_id', $siembra->id)
                ->latest('created_at')
                ->first();

            $siembra->ultima_humedad = $ultimaLectura ? number_format($ultimaLectura->valor, 2) : '--';
            $siembra->ultima_fecha = $ultimaLectura ? $ultimaLectura->created_at->format('d/m/Y H:i') : 'Sin datos';

            // Calcular progreso
            $fechaSiembra = \Carbon\Carbon::parse($siembra->fecha_siembra)->startOfDay();
            $fechaEstimada = \Carbon\Carbon::parse($siembra->fecha_estimada_cosecha)->startOfDay();
            $hoy = \Carbon\Carbon::now()->startOfDay();

            $diasTotales = $fechaSiembra->diffInDays($fechaEstimada);
            if ($hoy->greaterThanOrEqualTo($fechaSiembra)) {
                $diasTranscurridos = $fechaSiembra->diffInDays($hoy);
            } else {
                $diasTranscurridos = 0;
            }

            if ($diasTotales > 0) {
                $siembra->progreso = min(100, round(($diasTranscurridos / $diasTotales) * 100));
            } else {
                $siembra->progreso = 0;
            }

            if ($hoy->greaterThanOrEqualTo($fechaEstimada)) {
                $siembra->progreso = 100;
            }
        }

        return view('Monitoreo.monitoreo', compact('siembras'));
    }
}
