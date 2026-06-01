@extends('layouts.app')

@section('header-title')
    <h1>Detalle de Cosecha</h1>
    <p>Información completa de la cosecha</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-carrot"></i> Cosecha #{{ str_pad($cosecha->id, 3, '0', STR_PAD_LEFT) }}</h2>
                        <span class="badge-alerta badge-resuelta">
                        <i class="fas fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($cosecha->fecha_cosecha)->format('d/m/Y') }}
                    </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <h5><i class="fas fa-seedling text-success"></i> Siembra relacionada</h5>
                                <p class="mb-1"><strong>Cultivo:</strong> {{ $cosecha->siembra->cultivo->nombre }}</p>
                                <p class="mb-1"><strong>Módulo:</strong> {{ $cosecha->siembra->modulo->nombre ?? 'N/A' }} - Charola {{ $cosecha->siembra->charola }}</p>
                                <p class="mb-0"><strong>Fecha de siembra:</strong> {{ \Carbon\Carbon::parse($cosecha->siembra->fecha_siembra)->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <h5><i class="fas fa-weight-scale text-success"></i> Datos de cosecha</h5>
                                <p class="mb-1"><strong>Cantidad:</strong> {{ number_format($cosecha->cantidad_kg, 2) }} kg</p>
                                <p class="mb-0"><strong>Calidad:</strong>
                                    <span class="badge-alerta
                                    @if($cosecha->calidad == 'Excelente') badge-resuelta
                                    @elseif($cosecha->calidad == 'Buena') badge-media
                                    @elseif($cosecha->calidad == 'Regular') badge-pendiente
                                    @else badge-ignorada
                                    @endif">
                                    {{ $cosecha->calidad }}
                                </span>
                                </p>
                            </div>
                        </div>

                        @if($cosecha->observaciones)
                            <div class="col-12 mb-4">
                                <div class="info-card p-3 bg-light rounded-3">
                                    <h5><i class="fas fa-align-left text-success"></i> Observaciones</h5>
                                    <p class="mb-0">{{ $cosecha->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        @if($cosecha->siembra->evaluacion)
                            <div class="col-12">
                                <div class="info-card p-3 bg-light rounded-3">
                                    <h5><i class="fas fa-chart-line text-success"></i> Evaluación de rendimiento</h5>
                                    <p class="mb-1"><strong>Rendimiento:</strong> {{ $cosecha->siembra->evaluacion->rendimiento }}/10</p>
                                    <p class="mb-0"><strong>Fecha evaluación:</strong> {{ \Carbon\Carbon::parse($cosecha->siembra->evaluacion->fecha_evaluacion)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('cosechas.index') }}" class="btn-outline-verde">
                            <i class="fas fa-arrow-left"></i> Volver a Cosechas
                        </a>
                        <div>
                            <a href="{{ route('cosechas.edit', $cosecha->id) }}" class="btn-naranja">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
