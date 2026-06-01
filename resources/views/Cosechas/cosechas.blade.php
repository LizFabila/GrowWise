@extends('layouts.app')

@section('header-title')
    <h1>Gestión de Cosechas</h1>
    <p>Registra y monitorea tus cosechas</p>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Cosechas</p>
                    <small>Registradas</small>
                </div>
                <div class="stat-icon"><i class="fas fa-carrot"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-info">
                    <h3>{{ number_format($stats['peso_total'], 2) }} kg</h3>
                    <p>Total Cosechado</p>
                    <small>Este mes</small>
                </div>
                <div class="stat-icon"><i class="fas fa-weight-hanging"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="150">
                <div class="stat-info">
                    <h3>{{ $stats['pendientes'] }}</h3>
                    <p>Próximas Cosechas</p>
                    <small>Próximos 7 días</small>
                </div>
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-info">
                    <h3>
                        @php
                            $calidades = ['Excelente' => 5, 'Buena' => 4, 'Regular' => 3, 'Mala' => 2];
                            $promedioCalidad = \App\Models\Cosecha::where('user_id', auth()->id())->avg('calidad');
                            $promedioNum = $calidades[$promedioCalidad] ?? 0;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $promedioNum) ★ @else ☆ @endif
                        @endfor
                    </h3>
                    <p>Calidad Promedio</p>
                    <small>{{ $promedioCalidad ?? 'Sin evaluaciones' }}</small>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-card" data-aos="fade-up" data-aos-delay="100">
            <div class="filtros-grid">
                <form method="GET" action="{{ route('cosechas.index') }}" class="d-flex flex-wrap gap-3 w-100">
                    <select name="cultivo" class="filtro-select">
                        <option value="">Todos los cultivos</option>
                        @foreach(\App\Models\Cultivo::where('activo', 1)->get() as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ request('cultivo') == $cultivo->id ? 'selected' : '' }}>
                                {{ $cultivo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="desde" class="filtro-select" value="{{ request('desde') }}" placeholder="Desde">
                    <input type="date" name="hasta" class="filtro-select" value="{{ request('hasta') }}" placeholder="Hasta">
                    <select name="calidad" class="filtro-select">
                        <option value="">Todas las calidades</option>
                        <option value="Excelente" {{ request('calidad') == 'Excelente' ? 'selected' : '' }}>Excelente</option>
                        <option value="Buena" {{ request('calidad') == 'Buena' ? 'selected' : '' }}>Buena</option>
                        <option value="Regular" {{ request('calidad') == 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Mala" {{ request('calidad') == 'Mala' ? 'selected' : '' }}>Mala</option>
                    </select>
                    <button type="submit" class="btn-naranja"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('cosechas.index') }}" class="btn-outline-verde"><i class="fas fa-undo"></i> Limpiar</a>
                </form>
            </div>
        </div>

        <!-- Tabla de cosechas -->
        <div class="table-container" data-aos="fade-up" data-aos-delay="200">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Registro de Cosechas</h2>
                <a href="{{ route('cosechas.create') }}" class="btn-naranja">
                    <i class="fas fa-plus-circle"></i> Nueva Cosecha
                </a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cultivo</th>
                        <th>Fecha Cosecha</th>
                        <th>Cantidad</th>
                        <th>Calidad</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cosechas as $cosecha)
                        <tr>
                            <td>#{{ str_pad($cosecha->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $cosecha->siembra->cultivo->nombre ?? 'N/A' }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($cosecha->fecha_cosecha)->format('d/m/Y') }}</td>
                            <td>{{ number_format($cosecha->cantidad_kg, 2) }} kg</td>
                            <td>
                            <span class="badge-alerta
                                @if($cosecha->calidad == 'Excelente') badge-resuelta
                                @elseif($cosecha->calidad == 'Buena') badge-media
                                @elseif($cosecha->calidad == 'Regular') badge-pendiente
                                @else badge-ignorada
                                @endif">
                                {{ $cosecha->calidad }}
                            </span>
                            </td>
                            <td>
                                <a href="{{ route('cosechas.show', $cosecha->id) }}" class="action-btn resolver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('cosechas.edit', $cosecha->id) }}" class="action-btn editar" style="background: #FF9800;"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('cosechas.destroy', $cosecha->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta cosecha?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-carrot"></i>
                                    <h3>No hay cosechas registradas</h3>
                                    <p>Registra tu primera cosecha</p>
                                    <a href="{{ route('cosechas.create') }}" class="btn-naranja">Nueva Cosecha</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $cosechas->links() }}
            </div>
        </div>
    </div>
@endsection
