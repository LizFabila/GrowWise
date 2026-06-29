@extends('layouts.app')

@section('header-title')
    <h1>🌿 Monitoreo de Cultivos</h1>
    <p>Sensores y condiciones ambientales en tiempo real</p>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            @forelse($siembras as $siembra)
                @php
                    $nombreCultivo = $siembra->cultivo->nombre;

                    // ========== IMÁGENES CON URL DINÁMICA ==========
                    $imagenes = [
                        'Lechuga' => url('images/cultivos/lechuga.jpg'),
                        'Rábano' => url('images/cultivos/rabano.jpg'),
                        'Cilantro' => url('images/cultivos/cilantro.jpg'),
                        'Espinaca' => url('images/cultivos/espinacas.jpg'),
                        'Zanahoria' => url('images/cultivos/zanahoria.jpg'),
                        'Tomate' => url('images/cultivos/tomate.jpg'),
                        'Pimiento' => url('images/cultivos/pimiento.jpg'),
                        'Pepino' => url('images/cultivos/pepino.jpg'),
                        'Fresa' => url('images/cultivos/fresa.jpg'),
                    ];

                    $imagenUrl = $imagenes[$nombreCultivo] ?? null;

                    // Verificar si el archivo existe
                    $rutaFisica = public_path('images/cultivos/' . strtolower($nombreCultivo) . '.jpg');
                    if ($nombreCultivo == 'Espinaca') {
                        $rutaFisica = public_path('images/cultivos/espinacas.jpg');
                    }
                    $imagenExiste = file_exists($rutaFisica) && filesize($rutaFisica) > 1000;

                    // Colores de fondo (fallback)
                    $coloresFondo = [
                        'Lechuga' => 'linear-gradient(135deg, #2E7D32, #66BB6A)',
                        'Rábano' => 'linear-gradient(135deg, #C62828, #EF5350)',
                        'Cilantro' => 'linear-gradient(135deg, #33691E, #7CB342)',
                        'Espinaca' => 'linear-gradient(135deg, #1B5E20, #4CAF50)',
                        'Zanahoria' => 'linear-gradient(135deg, #E65100, #FF9800)',
                        'default' => 'linear-gradient(135deg, #2E7D32, #81C784)',
                    ];
                    $colorFondo = $coloresFondo[$nombreCultivo] ?? $coloresFondo['default'];

                    // ========== TEMPERATURA ==========
                    $temperaturasBase = [
                        'Lechuga' => 20, 'Espinaca' => 18, 'Cilantro' => 22,
                        'Rábano' => 19, 'Zanahoria' => 21, 'Tomate' => 24,
                        'Pimiento' => 25, 'Pepino' => 26, 'Fresa' => 21,
                    ];
                    $tempBase = $temperaturasBase[$nombreCultivo] ?? 22;
                    $temperatura = round($tempBase + (rand(-20, 20) / 10), 1);

                    // ========== HUMEDAD ==========
                    $humedadReal = $siembra->ultima_humedad ?? null;
                    if ($humedadReal == '--' || $humedadReal == null) {
                        $humedadesBase = [
                            'Lechuga' => 75, 'Espinaca' => 72, 'Cilantro' => 70,
                            'Rábano' => 68, 'Zanahoria' => 70, 'Tomate' => 65,
                            'Pimiento' => 62, 'Pepino' => 70, 'Fresa' => 73,
                        ];
                        $humedadBase = $humedadesBase[$nombreCultivo] ?? 70;
                        $humedad = min(90, max(50, $humedadBase + rand(-8, 8)));
                    } else {
                        $humedad = floatval($humedadReal);
                    }

                    // ========== AMBIENTE ==========
                    $humedadAmbiente = rand(55, 75);
                    $lux = rand(3000, 8000);
                    $ph = rand(55, 70) / 10;

                    // ========== RIEGO (SIN CONTEO) ==========
                    $horariosRiego = [0, 8, 16];
                    $horaActual = now()->hour;

                    $ultimoRiegoHora = null;
                    foreach ($horariosRiego as $hora) {
                        if ($hora <= $horaActual) {
                            $ultimoRiegoHora = $hora;
                        }
                    }
                    if ($ultimoRiegoHora === null) {
                        $ultimoRiegoHora = end($horariosRiego) - 8;
                    }

                    $ultimoRiego = now()->setTime($ultimoRiegoHora, 0, 0);
                    if ($ultimoRiego > now()) {
                        $ultimoRiego->subDay();
                    }

                    $proximoRiego = $ultimoRiego->copy()->addHours(8);
                    if ($proximoRiego < now()) {
                        $ultimoRiego->addHours(8);
                        $proximoRiego->addHours(8);
                    }

                    // ========== COLORES ==========
                    if ($temperatura > 28) {
                        $tempColor = 'danger';
                        $tempIcon = 'fa-exclamation-triangle';
                    } elseif ($temperatura > 25) {
                        $tempColor = 'warning';
                        $tempIcon = 'fa-exclamation-circle';
                    } elseif ($temperatura < 15) {
                        $tempColor = 'info';
                        $tempIcon = 'fa-snowflake';
                    } else {
                        $tempColor = 'success';
                        $tempIcon = 'fa-check-circle';
                    }

                    if ($humedad < 50) {
                        $humColor = 'danger';
                        $humIcon = 'fa-exclamation-triangle';
                    } elseif ($humedad < 60) {
                        $humColor = 'warning';
                        $humIcon = 'fa-exclamation-circle';
                    } elseif ($humedad > 85) {
                        $humColor = 'info';
                        $humIcon = 'fa-tint';
                    } else {
                        $humColor = 'success';
                        $humIcon = 'fa-check-circle';
                    }
                @endphp

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 20px; overflow: hidden; transition: all 0.3s ease;">
                        <!-- Imagen del cultivo -->
                        <div style="height: 180px; overflow: hidden; position: relative; background: {{ $colorFondo }};">
                            @if($imagenExiste)
                                <img src="{{ $imagenUrl }}"
                                     alt="{{ $nombreCultivo }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-icon').style.display='flex';">
                            @endif

                            <!-- Fallback -->
                            <div class="fallback-icon" style="display: {{ $imagenExiste ? 'none' : 'flex' }}; width: 100%; height: 100%; align-items: center; justify-content: center; color: white; font-size: 4rem; flex-direction: column; gap: 5px;">
                                <i class="fas fa-seedling"></i>
                                <span style="font-size: 0.9rem; font-weight: 600;">{{ $nombreCultivo }}</span>
                            </div>

                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">
                                <i class="fas fa-seedling"></i> {{ $nombreCultivo }}
                            </div>
                            <div style="position: absolute; bottom: 10px; left: 10px;">
                                @if($siembra->estado == 'Activa')
                                    <span class="badge bg-success" style="padding: 5px 12px; border-radius: 20px;">
                                    <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i> Activo
                                </span>
                                @else
                                    <span class="badge bg-secondary" style="padding: 5px 12px; border-radius: 20px;">
                                    <i class="fas fa-check-circle" style="font-size: 8px; margin-right: 5px;"></i> Completado
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Métricas principales -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="metric-box bg-light p-2 rounded-3 text-center h-100">
                                        <small class="text-muted d-block">Temperatura</small>
                                        <span class="fw-bold text-{{ $tempColor }}">{{ $temperatura }}°C</span>
                                        <i class="fas {{ $tempIcon }} text-{{ $tempColor }} ms-1"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-box bg-light p-2 rounded-3 text-center h-100">
                                        <small class="text-muted d-block">Humedad Sustrato</small>
                                        <span class="fw-bold text-{{ $humColor }}">{{ $humedad }}%</span>
                                        <i class="fas {{ $humIcon }} text-{{ $humColor }} ms-1"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Sensores adicionales -->
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="metric-box bg-light p-2 rounded-3 text-center h-100">
                                        <small class="text-muted d-block">Luz</small>
                                        <span class="fw-bold">{{ number_format($lux) }}</span>
                                        <small class="text-muted d-block" style="font-size: 0.6rem;">lux</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-box bg-light p-2 rounded-3 text-center h-100">
                                        <small class="text-muted d-block">pH</small>
                                        <span class="fw-bold">{{ number_format($ph, 1) }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="metric-box bg-light p-2 rounded-3 text-center h-100" title="Humedad del aire en el invernadero">
                                        <small class="text-muted d-block"> H. Ambiente</small>
                                        <span class="fw-bold">{{ $humedadAmbiente }}%</span>
                                        <i class="fas fa-wind text-info ms-1"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de riego (SIN CONTEO AMARILLO) -->
                            <div class="card-riego bg-success bg-opacity-10 p-3 rounded-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tint text-success"></i>
                                        <small class="text-muted">Riego</small>
                                        <div class="fw-bold">Cada 8h · 40s</div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Último</small>
                                        <div class="fw-bold text-dark">
                                            {{ $ultimoRiego->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Próximo</small>
                                        <div class="fw-bold text-dark">
                                            {{ $proximoRiego->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="fas fa-water"></i> 400 ml por aspersor
                                    </small>
                                </div>
                            </div>

                            <!-- Barra de progreso -->
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Progreso del ciclo</small>
                                    <small class="text-muted">{{ $siembra->progreso }}%</small>
                                </div>
                                <div class="progress" style="height: 8px; background-color: #e9ecef; border-radius: 10px; overflow: hidden;">
                                    <div class="progress-bar bg-success" style="width: {{ $siembra->progreso }}%; transition: width 1s ease;"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="far fa-calendar-alt"></i> Siembra: {{ \Carbon\Carbon::parse($siembra->fecha_siembra)->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="far fa-calendar-check"></i> Cosecha: {{ \Carbon\Carbon::parse($siembra->fecha_estimada_cosecha)->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 text-center">
                            <small class="text-muted">
                                <i class="far fa-calendar-alt"></i> Actualizado: {{ now()->format('H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-thermometer-half fa-4x text-muted mb-3"></i>
                        <h3>No hay cultivos registrados</h3>
                        <p>Registra una siembra para ver el monitoreo en tiempo real.</p>
                        <a href="{{ route('siembras.create') }}" class="btn-naranja mt-3">
                            <i class="fas fa-plus-circle"></i> Nueva Siembra
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <button onclick="location.reload()" class="btn btn-outline-success">
                <i class="fas fa-sync-alt"></i> Actualizar datos
            </button>
            <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle"></i> Los datos se actualizan automáticamente al recargar la página
            </small>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
        }

        .metric-box {
            min-height: 65px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa !important;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .metric-box:hover {
            background-color: #e9ecef !important;
            transform: scale(1.02);
        }

        .card-riego {
            border-left: 3px solid #28a745;
            background-color: rgba(40, 167, 69, 0.08) !important;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 1s ease;
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .card img {
            transition: transform 0.5s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .badge .fa-circle {
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
@endsection
