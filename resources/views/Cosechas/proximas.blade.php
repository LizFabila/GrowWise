@extends('layouts.app')

@section('header-title')
    <h1>Próximas Cosechas</h1>
    <p>Cultivos que estarán listos para cosechar</p>
@endsection

@section('content')
    <div class="container">
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-calendar-week"></i> Listado de Próximas Cosechas</h2>
                <a href="{{ route('cosechas.index') }}" class="btn-outline-verde">
                    <i class="fas fa-arrow-left"></i> Volver a Cosechas
                </a>
            </div>

            @if(isset($proximasCosechas) && $proximasCosechas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Charola</th>
                            <th>Cultivo</th>
                            <th>Fecha Siembra</th>
                            <th>Fecha Estimada</th>
                            <th>Días Restantes</th>
                            <th>Módulo</th>
                            <th>Progreso</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($proximasCosechas as $siembra)
                            @php
                                // Fechas
                                $hoy = \Carbon\Carbon::now()->startOfDay();
                                $fechaSiembra = \Carbon\Carbon::parse($siembra->fecha_siembra)->startOfDay();
                                $fechaEstimada = \Carbon\Carbon::parse($siembra->fecha_estimada_cosecha)->startOfDay();

                                // Días restantes
                                $diasRestantes = (int) $hoy->diffInDays($fechaEstimada, false);

                                if ($diasRestantes < 0) {
                                    continue;
                                }

                                // Días totales del cultivo (desde siembra hasta cosecha estimada)
                                $diasTotales = (int) $fechaSiembra->diffInDays($fechaEstimada);

                                // Días transcurridos REALES (desde siembra hasta hoy)
                                if ($hoy->greaterThanOrEqualTo($fechaSiembra)) {
                                    $diasTranscurridos = (int) $fechaSiembra->diffInDays($hoy);
                                } else {
                                    $diasTranscurridos = 0;
                                }

                                // Progreso REAL basado en fechas
                                if ($diasTotales > 0) {
                                    $progreso = round(($diasTranscurridos / $diasTotales) * 100);
                                    $progresoMostrar = min(100, $progreso);
                                } else {
                                    $progreso = 0;
                                    $progresoMostrar = 0;
                                }

                                // Determinar si está atrasado
                                $estaAtrasado = ($diasTranscurridos > $diasTotales) && ($diasRestantes > 0);

                                // Determinar color de la barra según porcentaje
                                if ($progresoMostrar <= 25) {
                                    $barraColor = 'bg-danger';
                                    $textoColor = 'text-danger';
                                    $iconoProgreso = 'fa-seedling';
                                    $mensajeProgreso = 'Inicio del ciclo';
                                } elseif ($progresoMostrar <= 50) {
                                    $barraColor = 'bg-warning';
                                    $textoColor = 'text-warning';
                                    $iconoProgreso = 'fa-sprout';
                                    $mensajeProgreso = 'Desarrollo temprano';
                                } elseif ($progresoMostrar <= 75) {
                                    $barraColor = 'bg-info';
                                    $textoColor = 'text-info';
                                    $iconoProgreso = 'fa-leaf';
                                    $mensajeProgreso = 'Crecimiento activo';
                                } else {
                                    $barraColor = 'bg-success';
                                    $textoColor = 'text-success';
                                    $iconoProgreso = 'fa-check-circle';
                                    $mensajeProgreso = 'Próximo a cosechar';
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">
                                    <i class="fas fa-layer-group text-success me-1"></i>
                                    #{{ $siembra->charola }}
                                </td>

                                <td>
                                    <strong>{{ $siembra->cultivo->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">Ciclo: {{ $diasTotales }} días</small>
                                </td>

                                <td>{{ $fechaSiembra->format('d/m/Y') }}</td>
                                <td>{{ $fechaEstimada->format('d/m/Y') }}</td>

                                <td>
                                    @if($diasRestantes <= 3)
                                        <span class="badge bg-warning text-dark">⚠️ {{ $diasRestantes }} días</span>
                                    @elseif($diasRestantes <= 7)
                                        <span class="badge bg-info text-dark">📅 {{ $diasRestantes }} días</span>
                                    @else
                                        <span class="badge bg-success">{{ $diasRestantes }} días</span>
                                    @endif
                                </td>

                                <td>
                                    <i class="fas fa-thermometer-half text-success me-1"></i>
                                    {{ $siembra->modulo->nombre ?? 'Invernadero Principal' }}
                                </td>

                                <td style="min-width: 250px;">
                                    <div class="progress-container">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <div class="progress flex-grow-1" style="height: 12px; background-color: #e9ecef; border-radius: 20px; overflow: hidden;">
                                                <div class="progress-bar {{ $barraColor }}"
                                                     style="width: {{ $progresoMostrar }}%; border-radius: 20px; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);"
                                                     role="progressbar"
                                                     aria-valuenow="{{ $progresoMostrar }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="fw-bold {{ $textoColor }} fs-5">{{ $progresoMostrar }}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas {{ $iconoProgreso }} me-1"></i>
                                                {{ $mensajeProgreso }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt"></i> Día {{ $diasTranscurridos }}/{{ $diasTotales }}
                                            </small>
                                        </div>

                                        @if($estaAtrasado)
                                            <div class="alert-atrasado mt-2">
                                                <i class="fas fa-exclamation-triangle"></i> Atrasado {{ $diasTranscurridos - $diasTotales }} días
                                            </div>
                                        @elseif($progresoMostrar >= 75 && $diasRestantes <= 5)
                                            <div class="alert-ready mt-2">
                                                <i class="fas fa-bell"></i> ¡Listo para cosechar en pocos días!
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Mostrando {{ $proximasCosechas->count() }} de {{ $proximasCosechas->total() }} próximas cosechas
                        </small>
                    </div>
                    <div>
                        {{ $proximasCosechas->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                    <h3>No hay próximas cosechas</h3>
                    <p>Todos tus cultivos están cosechados o no hay siembras activas</p>
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
        /* Estilos para las barras de progreso */
        .bg-danger {
            background: linear-gradient(90deg, #dc3545, #ff6b6b);
        }

        .bg-warning {
            background: linear-gradient(90deg, #ffc107, #ffda6a);
        }

        .bg-info {
            background: linear-gradient(90deg, #0dcaf0, #6ee7ff);
        }

        .bg-success {
            background: linear-gradient(90deg, #198754, #2ecc71);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
            color: #000 !important;
        }

        .bg-success {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .alert-ready {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(46, 204, 113, 0.05));
            border-left: 3px solid #28a745;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            color: #28a745;
            font-weight: 600;
            margin-top: 8px;
            animation: slideIn 0.5s ease;
        }

        .alert-atrasado {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
            border-left: 3px solid #dc3545;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            color: #dc3545;
            font-weight: 600;
            margin-top: 8px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .progress-container {
            background: white;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .progress-container:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .progress-bar {
            position: relative;
            cursor: help;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-bar:hover::after {
            content: attr(aria-valuenow) '% completado';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            white-space: nowrap;
            margin-bottom: 5px;
            z-index: 1000;
            pointer-events: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, rgba(46,125,50,0.05), rgba(46,125,50,0.02));
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .progress-container {
                min-width: 180px;
            }

            .badge {
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            .table td {
                vertical-align: middle;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const percentage = bar.getAttribute('aria-valuenow');
                if (percentage) {
                    bar.setAttribute('title', `${percentage}% completado`);
                }
            });

            const rows = document.querySelectorAll('.table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                row.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 50);
            });
        });
    </script>
@endsection
