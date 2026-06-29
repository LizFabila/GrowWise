@extends('layouts.app')

@section('header-title')
    <h1>📊 Reportes</h1>
    <p>Genera y visualiza tus reportes en PDF</p>
@endsection

@section('content')
    <div class="container">
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-file-alt"></i> Reportes Generados</h2>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus-circle"></i> Generar Reporte
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reportes.generar') }}">
                                    <i class="fas fa-seedling"></i> Todos los cultivos activos
                                </a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($cultivosActivos as $cultivo)
                                <li><a class="dropdown-item" href="{{ route('reportes.generar', $cultivo->id) }}">
                                        <i class="fas fa-leaf"></i> {{ $cultivo->nombre }}
                                    </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Formato</th>
                        <th>Tamaño</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reportes as $reporte)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $reporte->nombre }}</td>
                            <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </span>
                            </td>
                            <td>{{ $reporte->tamaño_kb ?? 'N/A' }} KB</td>
                            <td>
                                @if($reporte->archivo_url)
                                    @if($reporte->descargado == 1)
                                        <!-- Ya descargado → Botón "Ver" -->
                                        <a href="{{ route('reportes.ver', $reporte->id) }}" class="btn btn-sm btn-success" target="_blank">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @else
                                        <!-- No descargado → Botón "Descargar" -->
                                        <a href="{{ route('reportes.descargar', $reporte->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-download"></i> Descargar
                                        </a>
                                    @endif
                                @endif
                                <form action="{{ route('reportes.eliminar', $reporte->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este reporte?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-file-pdf fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay reportes generados todavía.</p>
                                <p class="text-muted small">Genera tu primer reporte usando el botón "Generar Reporte".</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $reportes->links() }}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
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

        .btn-outline-success {
            border-color: #2E7D32;
            color: #2E7D32;
        }

        .btn-outline-success:hover {
            background: #2E7D32;
            color: white;
        }

        .dropdown-item:hover {
            background: rgba(46, 125, 50, 0.05);
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2E7D32;
        }

        .alert {
            border-radius: 15px;
            border: none;
            margin-bottom: 15px;
            padding: 12px 20px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2E7D32;
            border-left: 4px solid #2E7D32;
        }

        .alert-danger {
            background: #fef0f0;
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: #fef5e8;
            color: #E67E22;
            border-left: 4px solid #E67E22;
        }

        .btn-success {
            background: #2E7D32;
            border-color: #2E7D32;
        }

        .btn-success:hover {
            background: #1B5E20;
            border-color: #1B5E20;
        }
    </style>
@endsection
