@extends('layouts.app')

@section('header-title')
    <h1>Evaluaciones de Rendimiento</h1>
    <p>Analiza el rendimiento de tus siembras y cultivos</p>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Evaluaciones totales</p>
                    <small>Histórico</small>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-info">
                    <h3>{{ number_format($stats['promedio'], 1) }}</h3>
                    <p>Promedio rendimiento</p>
                    <small>/10</small>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="150">
                <div class="stat-info">
                    <h3>{{ $stats['pendientes'] }}</h3>
                    <p>Evaluaciones pendientes</p>
                    <small>Este mes</small>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-info">
                    <h3>{{ $stats['eficiencia'] ? round($stats['eficiencia']) . '%' : '0%' }}</h3>
                    <p>Eficiencia general</p>
                    <small>Excelente</small>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-card" data-aos="fade-up" data-aos-delay="100">
            <div class="filtros-grid">
                <form method="GET" action="{{ route('evaluaciones.index') }}" class="d-flex flex-wrap gap-3 w-100">
                    <select name="cultivo" class="filtro-select">
                        <option value="">Todos los cultivos</option>
                        @foreach($cultivos as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ request('cultivo') == $cultivo->id ? 'selected' : '' }}>
                                {{ $cultivo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="desde" class="filtro-select" value="{{ request('desde', now()->startOfMonth()->format('Y-m-d')) }}">
                    <input type="date" name="hasta" class="filtro-select" value="{{ request('hasta', now()->format('Y-m-d')) }}">
                    <select name="rendimiento_min" class="filtro-select">
                        <option value="">Todos los rendimientos</option>
                        <option value="8" {{ request('rendimiento_min') == 8 ? 'selected' : '' }}>Alto (8-10)</option>
                        <option value="5" {{ request('rendimiento_min') == 5 ? 'selected' : '' }}>Medio (5-7)</option>
                        <option value="0" {{ request('rendimiento_min') == 0 ? 'selected' : '' }}>Bajo (0-4)</option>
                    </select>
                    <button type="submit" class="btn-naranja"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('evaluaciones.index') }}" class="btn-outline-verde"><i class="fas fa-undo"></i> Limpiar</a>
                </form>
            </div>
        </div>

        <!-- Tabla de evaluaciones -->
        <div class="table-container" data-aos="fade-up" data-aos-delay="200">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Listado de evaluaciones</h2>
                <a href="{{ route('evaluaciones.create') }}" class="btn-naranja">
                    <i class="fas fa-plus-circle"></i> Nueva Evaluación
                </a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cultivo</th>
                        <th>Siembra</th>
                        <th>Fecha evaluación</th>
                        <th>Rendimiento</th>
                        <th>Eficiencia</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($evaluaciones as $evaluacion)
                        <tr>
                            <td>#{{ str_pad($evaluacion->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $evaluacion->siembra->cultivo->nombre ?? 'N/A' }}</strong></td>
                            <td>#S-{{ str_pad($evaluacion->siembra_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ \Carbon\Carbon::parse($evaluacion->fecha_evaluacion)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge-alerta
                                    @if($evaluacion->rendimiento >= 8) badge-resuelta
                                    @elseif($evaluacion->rendimiento >= 5) badge-media
                                    @else badge-pendiente
                                    @endif">
                                    {{ $evaluacion->rendimiento }}/10
                                </span>
                            </td>
                            <td>{{ $evaluacion->eficiencia ?? 'N/A' }}%</td>
                            <td>{{ \Illuminate\Support\Str::limit($evaluacion->observaciones, 30) }}</td>
                            <td>
                                <a href="{{ route('evaluaciones.show', $evaluacion->id) }}" class="action-btn resolver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('evaluaciones.edit', $evaluacion->id) }}" class="action-btn editar" style="background: #FF9800;"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('evaluaciones.destroy', $evaluacion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta evaluación?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-chart-bar"></i>
                                    <h3>No hay evaluaciones registradas</h3>
                                    <p>Registra tu primera evaluación</p>
                                    <a href="{{ route('evaluaciones.create') }}" class="btn-naranja">Nueva Evaluación</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $evaluaciones->links() }}
            </div>
        </div>
    </div>
@endsection
