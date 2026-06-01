@extends('layouts.app')

@section('header-title')
    <h1>Detalle de Evaluación</h1>
    <p>Información completa de la evaluación</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-chart-bar"></i> Evaluación #{{ str_pad($evaluacion->id, 3, '0', STR_PAD_LEFT) }}</h2>
                        <span class="badge-alerta badge-resuelta">
                        <i class="fas fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($evaluacion->fecha_evaluacion)->format('d/m/Y') }}
                    </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <h5><i class="fas fa-seedling text-success"></i> Siembra evaluada</h5>
                                <p class="mb-1"><strong>Cultivo:</strong> {{ $evaluacion->siembra->cultivo->nombre }}</p>
                                <p class="mb-1"><strong>Módulo:</strong> {{ $evaluacion->siembra->modulo->nombre ?? 'N/A' }} - Charola {{ $evaluacion->siembra->charola }}</p>
                                <p class="mb-0"><strong>Fecha de siembra:</strong> {{ \Carbon\Carbon::parse($evaluacion->siembra->fecha_siembra)->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <h5><i class="fas fa-star text-success"></i> Datos de evaluación</h5>
                                <p class="mb-1"><strong>Rendimiento:</strong>
                                    <span class="badge-alarta
                                    @if($evaluacion->rendimiento >= 8) badge-resuelta
                                    @elseif($evaluacion->rendimiento >= 5) badge-media
                                    @else badge-pendiente
                                    @endif">
                                    {{ $evaluacion->rendimiento }}/10
                                </span>
                                </p>
                                <p class="mb-0"><strong>Eficiencia:</strong> {{ $evaluacion->eficiencia ?? 'No registrada' }}%</p>
                            </div>
                        </div>

                        @if($evaluacion->observaciones)
                            <div class="col-12 mb-4">
                                <div class="info-card p-3 bg-light rounded-3">
                                    <h5><i class="fas fa-align-left text-success"></i> Observaciones</h5>
                                    <p class="mb-0">{{ $evaluacion->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        @if($evaluacion->siembra->cosecha)
                            <div class="col-12">
                                <div class="info-card p-3 bg-light rounded-3">
                                    <h5><i class="fas fa-carrot text-success"></i> Cosecha relacionada</h5>
                                    <p class="mb-1"><strong>Cantidad:</strong> {{ number_format($evaluacion->siembra->cosecha->cantidad_kg, 2) }} kg</p>
                                    <p class="mb-0"><strong>Calidad:</strong>
                                        <span class="badge-alerta
                                    @if($evaluacion->siembra->cosecha->calidad == 'Excelente') badge-resuelta
                                    @elseif($evaluacion->siembra->cosecha->calidad == 'Buena') badge-media
                                    @elseif($evaluacion->siembra->cosecha->calidad == 'Regular') badge-pendiente
                                    @else badge-ignorada
                                    @endif">
                                    {{ $evaluacion->siembra->cosecha->calidad }}
                                </span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('evaluaciones.index') }}" class="btn-outline-verde">
                            <i class="fas fa-arrow-left"></i> Volver a Evaluaciones
                        </a>
                        <div>
                            <a href="{{ route('evaluaciones.edit', $evaluacion->id) }}" class="btn-naranja">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
