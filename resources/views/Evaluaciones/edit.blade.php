@extends('layouts.app')

@section('header-title')
    <h1>Editar Evaluación</h1>
    <p>Modifica los datos de la evaluación</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-edit"></i> Editar Evaluación #{{ str_pad($evaluacion->id, 3, '0', STR_PAD_LEFT) }}</h2>
                    </div>

                    <!-- Información de la siembra -->
                    <div class="alert-info p-3 rounded-3 mb-4">
                        <h5><i class="fas fa-seedling"></i> Información de la siembra</h5>
                        <p class="mb-1"><strong>Cultivo:</strong> {{ $evaluacion->siembra->cultivo->nombre }}</p>
                        <p class="mb-1"><strong>Módulo:</strong> {{ $evaluacion->siembra->modulo->nombre ?? 'N/A' }} - Charola {{ $evaluacion->siembra->charola }}</p>
                        <p class="mb-0"><strong>Fecha de siembra:</strong> {{ \Carbon\Carbon::parse($evaluacion->siembra->fecha_siembra)->format('d/m/Y') }}</p>
                    </div>

                    <form action="{{ route('evaluaciones.update', $evaluacion->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_evaluacion" class="form-label">Fecha de Evaluación *</label>
                                <input type="date" name="fecha_evaluacion" id="fecha_evaluacion" class="filtro-select w-100" value="{{ old('fecha_evaluacion', \Carbon\Carbon::parse($evaluacion->fecha_evaluacion)->format('Y-m-d')) }}" required>
                                @error('fecha_evaluacion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rendimiento" class="form-label">Rendimiento (0-10) *</label>
                                <input type="number" step="0.1" name="rendimiento" id="rendimiento" class="filtro-select w-100" value="{{ old('rendimiento', $evaluacion->rendimiento) }}" required min="0" max="10">
                                @error('rendimiento')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">0-4: Bajo | 5-7: Medio | 8-10: Alto</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="eficiencia" class="form-label">Eficiencia (%) <span class="text-muted">(opcional)</span></label>
                                <input type="number" name="eficiencia" id="eficiencia" class="filtro-select w-100" value="{{ old('eficiencia', $evaluacion->eficiencia) }}" min="0" max="100">
                                @error('eficiencia')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="filtro-select w-100" rows="4" placeholder="Notas sobre el rendimiento...">{{ old('observaciones', $evaluacion->observaciones) }}</textarea>
                                @error('observaciones')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <a href="{{ route('evaluaciones.index') }}" class="btn-outline-verde">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="button" class="btn-outline-verde ms-2" style="border-color: #dc3545; color: #dc3545;" onclick="if(confirm('¿Eliminar esta evaluación?')) { document.getElementById('delete-form').submit(); }">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                            <button type="submit" class="btn-naranja">
                                <i class="fas fa-save"></i> Actualizar Evaluación
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('evaluaciones.destroy', $evaluacion->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
