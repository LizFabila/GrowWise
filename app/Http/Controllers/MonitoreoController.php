<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Sensor;
use App\Models\Siembra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoreoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener o crear módulo del usuario
        $modulo = Modulo::firstOrCreate(
            ['user_id' => $user->id, 'nombre' => 'Invernadero Valle de Bravo'],
            [
                'ubicacion' => 'Valle de Bravo - 160x62x42 cm - Malla 30% sombra',
                'num_charolas' => 4,
                'tipo_riego' => 'Automático',
                'activo' => 1
            ]
        );

        // Obtener las siembras activas del usuario
        $siembras = Siembra::where('user_id', $user->id)
            ->where('estado', 'Activa')
            ->with('cultivo')
            ->get();

        // Crear o actualizar sensores para cada cultivo
        $sensores = collect();

        foreach ($siembras as $siembra) {
            $nombreCultivo = $siembra->cultivo->nombre;
            $sensorNombre = "Humedad Sustrato - " . $nombreCultivo;

            $sensor = Sensor::updateOrCreate(
                [
                    'modulo_id' => $modulo->id,
                    'nombre' => $sensorNombre,
                    'tipo' => 'Humedad'
                ],
                [
                    'unidad' => '%',
                    'ubicacion' => "Charola {$siembra->charola} - {$nombreCultivo} (34.5x43 cm)",
                    'activo' => 1
                ]
            );

            // Generar datos simulados si no hay lecturas recientes
            $ultimaLectura = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensor->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Si no hay lectura en las últimas 8 horas, generar una nueva
            if (!$ultimaLectura || strtotime($ultimaLectura->created_at) < strtotime('-8 hours')) {
                $this->generarLecturaSimulada($sensor, $nombreCultivo, $siembra->charola);
            }

            // Obtener la última lectura actualizada
            $sensor->ultima_lectura = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensor->id)
                ->value('valor');

            $sensor->ultima_lectura_at = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensor->id)
                ->value('created_at');

            $sensores->push($sensor);
        }

        // También agregar sensor de temperatura y luz (generales)
        $this->agregarSensorGeneral($modulo, 'Temperatura Ambiental', '°C', 18, 25);
        $this->agregarSensorGeneral($modulo, 'Luz LED', 'lux', 3500, 6800);
        $this->agregarSensorGeneral($modulo, 'pH Solución', 'pH', 6.0, 6.8);
        $this->agregarSensorGeneral($modulo, 'Nutrientes TDS', 'ppm', 1150, 1250);

        // Agregar sensores generales al listado
        $sensoresGenerales = Sensor::where('modulo_id', $modulo->id)
            ->whereIn('tipo', ['Temperatura', 'Luz', 'pH', 'Nutrientes'])
            ->get();

        foreach ($sensoresGenerales as $sensorGeneral) {
            // Actualizar lectura si es necesario
            $ultimaLectura = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensorGeneral->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$ultimaLectura || strtotime($ultimaLectura->created_at) < strtotime('-8 hours')) {
                $this->generarLecturaGeneral($sensorGeneral);
            }

            $sensorGeneral->ultima_lectura = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensorGeneral->id)
                ->value('valor');

            $sensorGeneral->ultima_lectura_at = DB::table('lecturas_sensores')
                ->where('sensor_id', $sensorGeneral->id)
                ->value('created_at');

            $sensores->push($sensorGeneral);
        }

        // Obtener lecturas recientes (últimas 15)
        $lecturasRecientes = DB::table('lecturas_sensores as ls')
            ->join('sensores as s', 'ls.sensor_id', '=', 's.id')
            ->where('s.modulo_id', $modulo->id)
            ->orderBy('ls.created_at', 'desc')
            ->limit(20)
            ->select(
                'ls.created_at',
                'ls.valor',
                's.nombre as sensor_nombre',
                's.tipo',
                's.unidad'
            )
            ->get();

        // Estadísticas para tarjetas
        $stats = $this->calcularEstadisticas($modulo->id);

        // Datos para gráficos
        $chartData = $this->obtenerDatosGraficos($modulo->id);

        return view('Monitoreo.monitoreo', compact('sensores', 'lecturasRecientes', 'stats', 'chartData', 'modulo', 'siembras'));
    }

    private function generarLecturaSimulada($sensor, $nombreCultivo, $charola)
    {
        // Valores de humedad según el cultivo (óptimo)
        $valoresHumedad = [
            'Rábano' => ['min' => 60, 'max' => 75, 'actual' => 65],
            'Lechuga' => ['min' => 55, 'max' => 70, 'actual' => 60],
            'Espinaca' => ['min' => 70, 'max' => 85, 'actual' => 78],
            'Cilantro' => ['min' => 60, 'max' => 80, 'actual' => 68],
        ];

        $datos = $valoresHumedad[$nombreCultivo] ?? ['min' => 50, 'max' => 80, 'actual' => 65];

        // Simular variación natural
        $hora = date('H');
        $variacion = sin($hora * 15 * M_PI / 180) * 5; // Variación senoidal
        $valor = $datos['actual'] + $variacion;
        $valor = round(max($datos['min'], min($datos['max'], $valor)), 1);

        // Insertar lectura
        DB::table('lecturas_sensores')->insert([
            'sensor_id' => $sensor->id,
            'valor' => $valor,
            'created_at' => now(),
        ]);

        // Actualizar última lectura en sensor
        Sensor::where('id', $sensor->id)->update([
            'ultima_lectura' => $valor,
            'ultima_lectura_at' => now(),
        ]);

        return $valor;
    }

    private function agregarSensorGeneral($modulo, $nombre, $unidad, $min, $max)
    {
        $tipo = explode(' ', $nombre)[0];
        $tipoMap = [
            'Temperatura' => 'Temperatura',
            'Luz' => 'Luz',
            'pH' => 'pH',
            'Nutrientes' => 'Nutrientes'
        ];

        $tipoReal = $tipoMap[$tipo] ?? $tipo;

        $sensor = Sensor::firstOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nombre' => $nombre,
                'tipo' => $tipoReal
            ],
            [
                'unidad' => $unidad,
                'ubicacion' => 'General - Invernadero',
                'activo' => 1
            ]
        );

        // Verificar si necesita nueva lectura
        $ultimaLectura = DB::table('lecturas_sensores')
            ->where('sensor_id', $sensor->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$ultimaLectura || strtotime($ultimaLectura->created_at) < strtotime('-8 hours')) {
            $this->generarLecturaGeneral($sensor);
        }

        return $sensor;
    }

    private function generarLecturaGeneral($sensor)
    {
        // Valores según tipo de sensor
        $valores = [
            'Temperatura' => ['min' => 18, 'max' => 25, 'actual' => 22],
            'Luz' => ['min' => 3000, 'max' => 7000, 'actual' => 5000],
            'pH' => ['min' => 5.5, 'max' => 6.8, 'actual' => 6.3],
            'Nutrientes' => ['min' => 1000, 'max' => 1500, 'actual' => 1200],
        ];

        $tipoBase = explode(' ', $sensor->nombre)[0];
        $datos = $valores[$tipoBase] ?? ['min' => 0, 'max' => 100, 'actual' => 50];

        // Simular variación según hora del día
        $hora = date('H');
        if ($tipoBase == 'Temperatura') {
            // Temperatura: más alta al mediodía
            $variacion = 3 * sin(($hora - 6) * 15 * M_PI / 180);
            $valor = $datos['actual'] + $variacion;
        } elseif ($tipoBase == 'Luz') {
            // Luz: solo de día (6am a 6pm)
            if ($hora < 6 || $hora > 18) {
                $valor = 0;
            } else {
                $variacion = 3000 * sin(($hora - 6) * 15 * M_PI / 180);
                $valor = $datos['actual'] + $variacion;
            }
        } else {
            $valor = $datos['actual'];
        }

        $valor = round(max($datos['min'], min($datos['max'], $valor)), 1);

        // Insertar lectura
        DB::table('lecturas_sensores')->insert([
            'sensor_id' => $sensor->id,
            'valor' => $valor,
            'created_at' => now(),
        ]);

        // Actualizar última lectura en sensor
        Sensor::where('id', $sensor->id)->update([
            'ultima_lectura' => $valor,
            'ultima_lectura_at' => now(),
        ]);

        return $valor;
    }

    private function calcularEstadisticas($moduloId)
    {
        $stats = [
            'temperatura' => ['valor' => '--', 'estado' => 'normal'],
            'humedad' => ['valor' => '--', 'estado' => 'normal'],
            'luz' => ['valor' => '--', 'estado' => 'normal'],
            'ph' => ['valor' => '--', 'estado' => 'normal'],
            'nutrientes' => ['valor' => '--', 'estado' => 'normal'],
        ];

        // Temperatura
        $tempSensor = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Temperatura')
            ->first();
        if ($tempSensor && $tempSensor->ultima_lectura) {
            $stats['temperatura']['valor'] = $tempSensor->ultima_lectura;
            $stats['temperatura']['estado'] = $this->getEstadoTemp($tempSensor->ultima_lectura);
        }

        // Luz
        $luzSensor = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Luz')
            ->first();
        if ($luzSensor && $luzSensor->ultima_lectura) {
            $stats['luz']['valor'] = $luzSensor->ultima_lectura;
        }

        // pH
        $phSensor = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'pH')
            ->first();
        if ($phSensor && $phSensor->ultima_lectura) {
            $stats['ph']['valor'] = $phSensor->ultima_lectura;
        }

        // Nutrientes
        $nutrientesSensor = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Nutrientes')
            ->first();
        if ($nutrientesSensor && $nutrientesSensor->ultima_lectura) {
            $stats['nutrientes']['valor'] = $nutrientesSensor->ultima_lectura;
        }

        // Humedad (promedio de los cultivos)
        $humSensores = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Humedad')
            ->where('nombre', 'like', '%Humedad Sustrato%')
            ->get();

        if ($humSensores->count() > 0) {
            $total = 0;
            $count = 0;
            foreach ($humSensores as $sensor) {
                if ($sensor->ultima_lectura) {
                    $total += $sensor->ultima_lectura;
                    $count++;
                }
            }
            if ($count > 0) {
                $stats['humedad']['valor'] = round($total / $count, 1);
                $stats['humedad']['estado'] = $this->getEstadoHumedad($stats['humedad']['valor']);
            }
        }

        return $stats;
    }

    private function obtenerDatosGraficos($moduloId)
    {
        // Obtener sensor de temperatura
        $tempSensor = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Temperatura')
            ->first();

        $chartData = [
            'temp_24h_labels' => [],
            'temp_24h_values' => [],
            'hum_24h_labels' => [],
            'hum_24h_values' => [],
        ];

        if ($tempSensor) {
            $lecturas24h = DB::table('lecturas_sensores')
                ->where('sensor_id', $tempSensor->id)
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($lecturas24h as $lectura) {
                $chartData['temp_24h_labels'][] = date('H:i', strtotime($lectura->created_at));
                $chartData['temp_24h_values'][] = $lectura->valor;
            }
        }

        // Si no hay datos, usar valores de ejemplo para mostrar gráfica
        if (empty($chartData['temp_24h_values'])) {
            for ($i = 0; $i < 24; $i += 3) {
                $chartData['temp_24h_labels'][] = sprintf("%02d:00", $i);
                $chartData['temp_24h_values'][] = 20 + sin($i * 15 * M_PI / 180) * 3;
            }
        }

        // Humedad del sustrato promedio
        $humSensores = Sensor::where('modulo_id', $moduloId)
            ->where('tipo', 'Humedad')
            ->where('nombre', 'like', '%Humedad Sustrato%')
            ->pluck('id');

        if ($humSensores->count() > 0) {
            $lecturasHum = DB::table('lecturas_sensores')
                ->whereIn('sensor_id', $humSensores)
                ->where('created_at', '>=', now()->subHours(24))
                ->select('valor', 'created_at')
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy(function($item) {
                    return date('H:i', strtotime($item->created_at));
                })
                ->map(function($group) {
                    return round($group->avg('valor'), 1);
                });

            foreach ($lecturasHum as $hora => $valor) {
                $chartData['hum_24h_labels'][] = $hora;
                $chartData['hum_24h_values'][] = $valor;
            }
        }

        // Si no hay datos, usar valores de ejemplo
        if (empty($chartData['hum_24h_values'])) {
            for ($i = 0; $i < 24; $i += 3) {
                $chartData['hum_24h_labels'][] = sprintf("%02d:00", $i);
                $chartData['hum_24h_values'][] = 65 + sin($i * 15 * M_PI / 180) * 5;
            }
        }

        return $chartData;
    }

    private function getEstadoTemp($valor)
    {
        if ($valor < 18) return 'baja';
        if ($valor > 25) return 'alta';
        return 'normal';
    }

    private function getEstadoHumedad($valor)
    {
        if ($valor < 55) return 'baja';
        if ($valor > 75) return 'alta';
        return 'normal';
    }

    private function getEstadoPh($valor)
    {
        if ($valor < 5.5) return 'bajo';
        if ($valor > 6.8) return 'alto';
        return 'normal';
    }

    public function actualizar(Request $request)
    {
        // Método para API del ESP32
        $data = $request->validate([
            'sensor_id' => 'required|exists:sensores,id',
            'valor' => 'required|numeric',
        ]);

        DB::table('lecturas_sensores')->insert([
            'sensor_id' => $data['sensor_id'],
            'valor' => $data['valor'],
            'created_at' => now(),
        ]);

        Sensor::where('id', $data['sensor_id'])->update([
            'ultima_lectura' => $data['valor'],
            'ultima_lectura_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
