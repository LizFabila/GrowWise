@extends('layouts.app')

@section('header-title')
    <h1>🔔 Alertas de Cosecha</h1>
    <p>Monitoreo de todas tus próximas cosechas</p>
@endsection

@section('content')
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Próximas Cosechas</p>
                    <small>Total de cultivos activos</small>
                </div>
                <div class="stat-icon"><i class="fas fa-seedling"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100" style="border-left: 4px solid #dc3545;">
                <div class="stat-info">
                    <h3 class="text-danger">{{ $stats['criticas'] }}</h3>
                    <p>Críticas</p>
                    <small>0-1 días</small>
                </div>
                <div class="stat-icon" style="background: #dc3545;"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="150" style="border-left: 4px solid #E67E22;">
                <div class="stat-info">
                    <h3 class="text-warning" style="color: #E67E22 !important;">{{ $stats['altas'] }}</h3>
                    <p>Altas</p>
                    <small>2-3 días</small>
                </div>
                <div class="stat-icon" style="background: #E67E22;"><i class="fas fa-bell"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200" style="border-left: 4px solid #2E7D32;">
                <div class="stat-info">
                    <h3 class="text-success">{{ $stats['medias'] }}</h3>
                    <p>Medias</p>
                    <small>4-7 días</small>
                </div>
                <div class="stat-icon" style="background: #2E7D32;"><i class="fas fa-clock"></i></div>
            </div>
        </div>

        <!-- Listado de alertas -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Próximas Cosechas</h2>
                <div>
                    <button onclick="location.reload()" class="btn-outline-verde me-2">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="{{ route('cosechas.proximas') }}" class="btn-naranja">
                        <i class="fas fa-calendar-week"></i> Ver Próximas
                    </a>
                </div>
            </div>

            @if(count($alertas) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="min-width: 200px;">Cultivo</th>
                            <th>Prioridad</th>
                            <th>Días Restantes</th>
                            <th>Fecha Estimada</th>
                            <th>Progreso</th>
                            <th>Ubicación</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($alertas as $alerta)
                            <tr class="alerta-fila alerta-{{ $alerta['color'] }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Mini imagen del cultivo -->
                                        <div style="width: 38px; height: 38px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: #f0f4f0; display: flex; align-items: center; justify-content: center; border: 2px solid #2E7D32;">
                                            @php
                                                $nombreCultivo = $alerta['cultivo'];
                                                $imagenes = [
                                                    'Lechuga' => url('images/cultivos/lechuga.jpg'),
                                                    'Rábano' => url('images/cultivos/rabano.jpg'),
                                                    'Cilantro' => url('images/cultivos/cilantro.jpg'),
                                                    'Espinaca' => url('images/cultivos/espinacas.jpg'),
                                                    'Zanahoria' => url('images/cultivos/zanahoria.jpg'),
                                                ];
                                                $imgUrl = $imagenes[$nombreCultivo] ?? null;
                                                $imgExiste = $imgUrl && file_exists(public_path(str_replace(url(''), '', $imgUrl)));
                                            @endphp
                                            @if($imgExiste)
                                                <img src="{{ $imgUrl }}"
                                                     alt="{{ $nombreCultivo }}"
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <i class="fas fa-seedling text-success" style="font-size: 1.2rem;"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $alerta['cultivo'] }}</strong>
                                            <br>
                                            <small class="text-muted">Sembrado: {{ $alerta['fecha_siembra'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-prioridad badge-{{ $alerta['badgeColor'] }}">
                                        <i class="fas {{ $alerta['icono'] }}"></i>
                                        {{ $alerta['prioridad'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-dias badge-dias-{{ $alerta['badgeColor'] }}">
                                        @if($alerta['dias_restantes'] <= 1)
                                            <i class="fas fa-exclamation-circle"></i> {{ $alerta['dias_restantes'] }} día
                                        @else
                                            {{ $alerta['dias_restantes'] }} días
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $alerta['fecha_estimada'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 100px; height: 8px; background: #e9ecef; border-radius: 10px; overflow: hidden;">
                                            <div class="progress-bar"
                                                 style="width: {{ $alerta['progreso'] }}%;
                                                        height: 100%;
                                                        @if($alerta['progreso'] >= 80) background: #2E7D32;
                                                        @elseif($alerta['progreso'] >= 50) background: #E67E22;
                                                        @else background: #81C784; @endif
                                                        border-radius: 10px;
                                                        transition: width 0.5s ease;">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $alerta['progreso'] }}%</small>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">
                                        Día {{ $alerta['dias_transcurridos'] }}/{{ $alerta['dias_totales'] }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <i class="fas fa-layer-group text-muted"></i> Charola {{ $alerta['charola'] }}
                                        <br>
                                        <span class="text-muted">{{ $alerta['modulo'] }}</span>
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje resumen -->
                @if($stats['criticas'] > 0)
                    <div class="alert alert-danger mt-3" style="border-radius: 15px; border-left: 4px solid #dc3545;">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            ¡Atención! Tienes {{ $stats['criticas'] }} cosecha(s) crítica(s) para MAÑANA.
                        </h5>
                    </div>
                @elseif($stats['altas'] > 0)
                    <div class="alert alert-warning mt-3" style="border-radius: 15px; border-left: 4px solid #E67E22; background: rgba(230, 126, 34, 0.08);">
                        <h5 class="mb-0">
                            <i class="fas fa-bell" style="color: #E67E22;"></i>
                            Tienes {{ $stats['altas'] }} cosecha(s) de alta prioridad en los próximos 3 días.
                        </h5>
                    </div>
                @elseif($stats['medias'] > 0)
                    <div class="alert alert-success mt-3" style="border-radius: 15px; border-left: 4px solid #2E7D32; background: rgba(46, 125, 50, 0.06);">
                        <h5 class="mb-0">
                            <i class="fas fa-clock" style="color: #2E7D32;"></i>
                            Tienes {{ $stats['medias'] }} cosecha(s) en los próximos 7 días.
                        </h5>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h3>No hay cultivos activos</h3>
                    <p>No tienes siembras activas programadas para cosecha.</p>
                    <a href="{{ route('siembras.create') }}" class="btn-naranja mt-3">
                        <i class="fas fa-plus-circle"></i> Nueva Siembra
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: #2E7D32;
        }

        .stat-info p {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .stat-info small {
            color: #999;
            font-size: 0.8rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: #2E7D32;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2E7D32;
            margin: 0;
        }

        /* ========== BADGES DE PRIORIDAD ========== */
        .badge-prioridad {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-critica {
            background: #dc3545;
            color: white;
        }

        .badge-alta {
            background: #E67E22;
            color: white;
        }

        .badge-media {
            background: #2E7D32;
            color: white;
        }

        .badge-baja {
            background: #6c757d;
            color: white;
        }

        .badge-programada {
            background: #e9ecef;
            color: #495057;
        }

        /* ========== BADGES DE DÍAS RESTANTES ========== */
        .badge-dias {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
        }

        .badge-dias-critica {
            background: #fef0f0;
            color: #dc3545;
            border-color: #dc3545;
        }

        .badge-dias-alta {
            background: #fef5e8;
            color: #E67E22;
            border-color: #E67E22;
        }

        .badge-dias-media {
            background: #e8f5e9;
            color: #2E7D32;
            border-color: #2E7D32;
        }

        .badge-dias-baja {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #ced4da;
        }

        .badge-dias-programada {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }

        /* ========== FILAS DE ALERTA ========== */
        .alerta-fila {
            transition: all 0.3s ease;
        }

        .alerta-fila:hover {
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .alerta-critica {
            border-left: 4px solid #dc3545;
            background: rgba(220, 53, 69, 0.03);
        }

        .alerta-alta {
            border-left: 4px solid #E67E22;
            background: rgba(230, 126, 34, 0.03);
        }

        .alerta-media {
            border-left: 4px solid #2E7D32;
            background: rgba(46, 125, 50, 0.03);
        }

        .alerta-baja {
            border-left: 4px solid #6c757d;
            background: rgba(108, 117, 125, 0.02);
        }

        .alerta-programada {
            border-left: 4px solid #dee2e6;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state h3 {
            color: #333;
            margin: 15px 0 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .btn-outline-verde {
            background: transparent;
            border: 2px solid #2E7D32;
            color: #2E7D32;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-verde:hover {
            background: #2E7D32;
            color: white;
        }

        .btn-naranja {
            background: #E67E22;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-naranja:hover {
            background: #D35400;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(230,126,34,0.3);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .table-header .btn {
                text-align: center;
            }

            .table td:first-child .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
        }
    </style>
@endsection
