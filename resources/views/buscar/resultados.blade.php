@extends('layouts.app')

@section('header-title')
    <h1>Resultados de búsqueda</h1>
    <p>Mostrando resultados para: <strong>"{{ $busqueda }}"</strong></p>
@endsection

@section('content')
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['total_siembras'] }}</h3>
                    <p>Total Siembras</p>
                    <small>Registros encontrados</small>
                </div>
                <div class="stat-icon"><i class="fas fa-history"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-info">
                    <h3>{{ $stats['activas'] }}</h3>
                    <p>Activas</p>
                    <small>En crecimiento</small>
                </div>
                <div class="stat-icon"><i class="fas fa-sprout"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="150">
                <div class="stat-info">
                    <h3>{{ $stats['completadas'] }}</h3>
                    <p>Completadas</p>
                    <small>Historial</small>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_cosechado'], 2) }} kg</h3>
                    <p>Total Cosechado</p>
                    <small>Producción total</small>
                </div>
                <div class="stat-icon"><i class="fas fa-carrot"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="250">
                <div class="stat-info">
                    <h3>{{ $stats['rendimiento_promedio'] }}/10</h3>
                    <p>Rendimiento Promedio</p>
                    <small>Evaluaciones</small>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>

        <!-- Cultivos encontrados -->
        @if($cultivosEncontrados->count() > 0)
            <div class="table-container mb-4">
                <div class="table-header">
                    <h2><i class="fas fa-seedling"></i> Cultivos relacionados</h2>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Cultivo</th>
                            <th>Tipo</th>
                            <th>Días a cosecha</th>
                            <th>Descripción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cultivosEncontrados as $cultivo)
                            <tr>
                                <td><strong>{{ $cultivo->nombre }}</strong></td>
                                <td>{{ $cultivo->tipo }}</td>
                                <td>{{ $cultivo->dias_cosecha ?? 'N/A' }} días</td>
                                <td>{{ Str::limit($cultivo->descripcion, 50) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Historial de siembras -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Historial de Siembras</h2>
                <a href="{{ route('siembras.create') }}" class="btn-naranja">
                    <i class="fas fa-plus-circle"></i> Nueva Siembra
                </a>
            </div>

            @if($siembras->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cultivo</th>
                            <th>Fecha Siembra</th>
                            <th>Módulo</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Cosecha</th>
                            <th>Rendimiento</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($siembras as $siembra)
                            <tr>
                                <td>#{{ str_pad($siembra->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td><strong>{{ $siembra->cultivo->nombre }}</strong></td>
                                <td>{{ $siembra->fecha_siembra->format('d/m/Y') }}</td>
                                <td>{{ $siembra->modulo->nombre ?? 'N/A' }} -

                                <td>
                            <span class="badge-alerta
                                @if($siembra->estado == 'Activa') badge-pendiente
                                @elseif($siembra->estado == 'Completada') badge-resuelta
                                @else badge-ignorada
                                @endif">
                                {{ $siembra->estado }}
                            </span>
                                </td>
                                <td>
                                    <div class="progress" style="width: 80px; height: 6px;">
                                        @php
                                            $diasTranscurridos = now()->diffInDays($siembra->fecha_siembra);
                                            $diasTotales = $siembra->cultivo->dias_cosecha ?? 30;
                                            $progreso = min(round(($diasTranscurridos / $diasTotales) * 100), 100);
                                        @endphp
                                        <div class="progress-bar bg-success" style="width: {{ $progreso }}%"></div>
                                    </div>
                                    <small>{{ $progreso }}%</small>
                                </td>
                                <td>
                                    @if($siembra->cosecha)
                                        {{ number_format($siembra->cosecha->cantidad_kg, 2) }} kg
                                        <br><small class="text-muted">{{ $siembra->cosecha->calidad }}</small>
                                    @else
                                        <span class="text-muted">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($siembra->evaluacion)
                                        <span class="fw-bold">{{ $siembra->evaluacion->rendimiento }}/10</span>
                                        <br><small>{{ $siembra->evaluacion->eficiencia }}% eficiencia</small>
                                    @else
                                        <span class="text-muted">Sin evaluar</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('siembras.show', $siembra->id) }}" class="action-btn ver"><i class="fas fa-eye"></i></a>
                                    @if($siembra->estado == 'Activa')
                                        <a href="{{ route('siembras.edit', $siembra->id) }}" class="action-btn editar"><i class="fas fa-edit"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron siembras</h3>
                    <p>No hay registros de "{{ $busqueda }}" en tu historial</p>
                    <a href="{{ route('siembras.create') }}" class="btn-naranja mt-3">Comenzar una siembra</a>
                </div>
            @endif
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('buscar.index') }}" class="btn-outline-verde">
                <i class="fas fa-arrow-left"></i> Nueva búsqueda
            </a>
        </div>
    </div>

    <style>
        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
        }
        .progress-bar {
            border-radius: 10px;
        }
        .action-btn.editar {
            background: #FF9800;
        }
    </style>
@endsection
