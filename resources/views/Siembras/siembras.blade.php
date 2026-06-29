@extends('layouts.app')

@section('header-title')
    <h1>Listado de Siembras</h1>
    <p>Gestión de tus siembras activas y completadas</p>
@endsection

@section('content')
    <div class="container">
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-sprout"></i> Siembras</h2>
                <a href="{{ route('siembras.create') }}" class="btn-naranja">
                    <i class="fas fa-plus-circle"></i> Nueva Siembra
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Cultivo</th>
                        <th>Fecha Siembra</th>
                        <th>Módulo</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                        <th>Fecha Estimada Cosecha</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($siembras as $siembra)
                        @php
                            // Determinar estado real
                            $estado = 'Activa';
                            if ($siembra->cosecha || $siembra->fecha_estimada_cosecha <= now()) {
                                $estado = 'Completada';
                            }

                            // Calcular progreso
                            $diasTotales = $siembra->cultivo->dias_cosecha ?? 30;
                            $diasTranscurridos = now()->diffInDays($siembra->fecha_siembra);
                            $progreso = min(100, round(($diasTranscurridos / $diasTotales) * 100));
                        @endphp
                        <tr>
                            <td><strong>{{ $siembra->cultivo->nombre }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($siembra->fecha_siembra)->format('d/m/Y') }}</td>
                            <td>{{ $siembra->modulo->nombre ?? 'N/A' }}</td>
                            <td>
                                @if($estado == 'Completada')
                                    <span class="badge bg-success">Completada</span>
                                @else
                                    <span class="badge bg-warning text-dark">Activa</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 80px; height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $progreso }}%;"></div>
                                    </div>
                                    <small>{{ $progreso }}%</small>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($siembra->fecha_estimada_cosecha)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('siembras.edit', $siembra->id) }}" class="action-btn editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('siembras.destroy', $siembra->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta siembra?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $siembras->links() }}
            </div>
        </div>
    </div>
@endsection
