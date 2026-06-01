<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Modulo;
use App\Models\Sensor;
use App\Models\Siembra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Generar alertas automáticas basadas en lecturas de sensores
        $this->generarAlertasAutomaticas($user->id);

        $alertas = Alerta::where('user_id', $user->id)
            ->orderByRaw("FIELD(prioridad, 'Critica', 'Alta', 'Media', 'Baja')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Agregar nombre del cultivo a cada alerta
        foreach ($alertas as $alerta) {
            if ($alerta->siembra_id) {
                $siembra = Siembra::with('cultivo')->find($alerta->siembra_id);
                $alerta->cultivo_nombre = $siembra->cultivo->nombre ?? null;
            }
        }

        $stats = [
            'pendientes' => Alerta::where('user_id', $user->id)->where('estado', 'Pendiente')->count(),
            'resueltas_hoy' => Alerta::where('user_id', $user->id)
                ->whereDate('fecha_resolucion', now()->toDateString())
                ->count(),
            'criticas' => Alerta::where('user_id', $user->id)
                ->where('prioridad', 'Critica')
                ->where('estado', 'Pendiente')
                ->count(),
            'total_mes' => Alerta::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        $modulos = Modulo::where('user_id', $user->id)->get();

        return view('Alertas.alertas', compact('alertas', 'stats', 'modulos'));
    }

    private function generarAlertasAutomaticas($userId)
    {
        // Obtener el módulo del usuario
        $modulo = Modulo::where('user_id', $userId)->first();
        if (!$modulo) return;

        // 1. Alertas de Temperatura
        $tempSensor = Sensor::where('modulo_id', $modulo->id)
            ->where('tipo', 'Temperatura')
            ->first();

        if ($tempSensor && $tempSensor->ultima_lectura) {
            $temp = floatval($tempSensor->ultima_lectura);

            if ($temp > 28) {
                $this->crearAlertaSiNoExiste($userId, $tempSensor->id, null, 'temperatura_critica',
                    'Temperatura Critica',
                    "La temperatura en el invernadero es de {$temp}°C, por encima del nivel critico (28°C).",
                    'Critica');
            } elseif ($temp > 25) {
                $this->crearAlertaSiNoExiste($userId, $tempSensor->id, null, 'temperatura_alta',
                    'Temperatura Alta',
                    "La temperatura en el invernadero es de {$temp}°C, por encima del rango optimo (18-25°C).",
                    'Alta');
            } elseif ($temp < 18) {
                $this->crearAlertaSiNoExiste($userId, $tempSensor->id, null, 'temperatura_baja',
                    'Temperatura Baja',
                    "La temperatura en el invernadero es de {$temp}°C, por debajo del rango optimo (18-25°C).",
                    'Media');
            }
        }

        // 2. Alertas de Humedad del Sustrato por cultivo
        $sensoresHumedad = Sensor::where('modulo_id', $modulo->id)
            ->where('tipo', 'Humedad')
            ->where('nombre', 'like', '%Humedad Sustrato%')
            ->get();

        foreach ($sensoresHumedad as $sensor) {
            if ($sensor->ultima_lectura) {
                $humedad = floatval($sensor->ultima_lectura);
                $cultivoNombre = str_replace('Humedad Sustrato - ', '', $sensor->nombre);

                // Obtener la siembra correspondiente
                $siembra = Siembra::where('user_id', $userId)
                    ->whereHas('cultivo', function($q) use ($cultivoNombre) {
                        $q->where('nombre', $cultivoNombre);
                    })
                    ->first();

                if ($humedad < 45) {
                    $this->crearAlertaSiNoExiste($userId, $sensor->id, $siembra->id ?? null, 'humedad_critica',
                        'Humedad Critica - ' . $cultivoNombre,
                        "La humedad del sustrato de {$cultivoNombre} es de {$humedad}%. Riego urgente!",
                        'Critica');
                } elseif ($humedad < 55) {
                    $this->crearAlertaSiNoExiste($userId, $sensor->id, $siembra->id ?? null, 'humedad_baja',
                        'Humedad Baja - ' . $cultivoNombre,
                        "La humedad del sustrato de {$cultivoNombre} es de {$humedad}%. Se recomienda regar.",
                        'Alta');
                } elseif ($humedad > 85) {
                    $this->crearAlertaSiNoExiste($userId, $sensor->id, $siembra->id ?? null, 'humedad_alta',
                        'Humedad Alta - ' . $cultivoNombre,
                        "La humedad del sustrato de {$cultivoNombre} es de {$humedad}%. Reducir riego para evitar hongos.",
                        'Media');
                }
            }
        }

        // 3. Alertas de pH
        $phSensor = Sensor::where('modulo_id', $modulo->id)
            ->where('tipo', 'pH')
            ->first();

        if ($phSensor && $phSensor->ultima_lectura) {
            $ph = floatval($phSensor->ultima_lectura);

            if ($ph < 5.0 || $ph > 7.5) {
                $this->crearAlertaSiNoExiste($userId, $phSensor->id, null, 'ph_critico',
                    'pH Critico',
                    "El pH de la solucion nutritiva es de {$ph}. Ajuste urgente a rango 5.5-6.8.",
                    'Critica');
            } elseif ($ph < 5.5) {
                $this->crearAlertaSiNoExiste($userId, $phSensor->id, null, 'ph_bajo',
                    'pH Bajo',
                    "El pH de la solucion nutritiva es de {$ph}. Se recomienda ajustar a 5.5-6.8.",
                    'Media');
            } elseif ($ph > 7.0) {
                $this->crearAlertaSiNoExiste($userId, $phSensor->id, null, 'ph_alto',
                    'pH Alto',
                    "El pH de la solucion nutritiva es de {$ph}. Se recomienda ajustar a 5.5-6.8.",
                    'Media');
            }
        }

        // 4. Alertas de Luz
        $luzSensor = Sensor::where('modulo_id', $modulo->id)
            ->where('tipo', 'Luz')
            ->first();

        if ($luzSensor && $luzSensor->ultima_lectura) {
            $luz = floatval($luzSensor->ultima_lectura);
            $hora = intval(date('H'));

            if ($hora >= 8 && $hora <= 18 && $luz < 2000) {
                $this->crearAlertaSiNoExiste($userId, $luzSensor->id, null, 'luz_insuficiente',
                    'Luz Insuficiente',
                    "El nivel de luz es de {$luz} lux, muy por debajo de lo recomendado (3000-8000 lux).",
                    'Media');
            } elseif ($hora >= 8 && $hora <= 18 && $luz < 3000) {
                $this->crearAlertaSiNoExiste($userId, $luzSensor->id, null, 'luz_baja',
                    'Luz Baja',
                    "El nivel de luz es de {$luz} lux, por debajo de lo optimo (3000-8000 lux).",
                    'Baja');
            }
        }
    }

    private function crearAlertaSiNoExiste($userId, $sensorId, $siembraId, $tipo, $titulo, $mensaje, $prioridad)
    {
        // Validar que la prioridad sea un valor permitido (sin acentos)
        $prioridadesPermitidas = ['Baja', 'Media', 'Alta', 'Critica'];
        if (!in_array($prioridad, $prioridadesPermitidas)) {
            $prioridad = 'Media';
        }

        // Verificar si ya existe una alerta similar en las últimas 6 horas
        $existe = Alerta::where('user_id', $userId)
            ->where('tipo', $tipo)
            ->where('estado', 'Pendiente')
            ->where('created_at', '>=', now()->subHours(6))
            ->exists();

        if (!$existe) {
            try {
                Alerta::create([
                    'user_id' => $userId,
                    'sensor_id' => $sensorId,
                    'siembra_id' => $siembraId,
                    'tipo' => $tipo,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'prioridad' => $prioridad,
                    'estado' => 'Pendiente',
                ]);
            } catch (\Exception $e) {
                // Si hay error, intentar con prioridad Media
                Alerta::create([
                    'user_id' => $userId,
                    'sensor_id' => $sensorId,
                    'siembra_id' => $siembraId,
                    'tipo' => $tipo,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'prioridad' => 'Media',
                    'estado' => 'Pendiente',
                ]);
            }
        }
    }

    public function resolver(string $id)
    {
        $alerta = Alerta::where('user_id', auth()->id())->findOrFail($id);
        $alerta->update([
            'estado' => 'Resuelta',
            'fecha_resolucion' => now()
        ]);

        return redirect()->back()->with('success', 'Alerta resuelta correctamente');
    }

    public function marcarTodasComoLeidas()
    {
        Alerta::where('user_id', auth()->id())
            ->where('estado', 'Pendiente')
            ->update([
                'estado' => 'Resuelta',
                'fecha_resolucion' => now()
            ]);

        return redirect()->back()->with('success', 'Todas las alertas fueron marcadas como resueltas');
    }

    public function destroy(string $id)
    {
        $alerta = Alerta::where('user_id', auth()->id())->findOrFail($id);
        $alerta->delete();

        return redirect()->back()->with('success', 'Alerta eliminada correctamente');
    }

    public function show(string $id)
    {
        $alerta = Alerta::with(['siembra.cultivo', 'sensor'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($alerta);
    }
}
