@extends('layouts.app')

@section('header-title')
    <h1>Registrar Nueva Evaluación</h1>
    <p>Evalúa el rendimiento de una siembra completada</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-chart-bar"></i> Nueva Evaluación</h2>
                    </div>

                    @if($siembras->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No hay siembras completadas disponibles para evaluar.
                            <a href="{{ route('siembras.index') }}" class="btn-outline-verde btn-sm ms-2">Ver siembras</a>
                        </div>
                    @else
                        <form action="{{ route('evaluaciones.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="siembra_id" class="form-label">Siembra a evaluar *</label>
                                <select name="siembra_id" id="siembra_id" class="filtro-select w-100" required>
                                    <option value="">Selecciona una siembra</option>
                                    @foreach($siembras as $siembra)
                                        <option value="{{ $siembra->id }}" {{ old('siembra_id') == $siembra->id ? 'selected' : '' }}>
                                            {{ $siembra->cultivo->nombre }} - Módulo {{ $siembra->modulo->nombre ?? 'N/A' }} ({{ \Carbon\Carbon::parse($siembra->fecha_siembra)->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('siembra_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_evaluacion" class="form-label">Fecha de Evaluación *</label>
                                    <input type="date" name="fecha_evaluacion" id="fecha_evaluacion" class="filtro-select w-100" value="{{ old('fecha_evaluacion', date('Y-m-d')) }}" required>
                                    @error('fecha_evaluacion')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="rendimiento" class="form-label">Rendimiento (0-10) *</label>
                                    <input type="number" step="0.1" name="rendimiento" id="rendimiento" class="filtro-select w-100" value="{{ old('rendimiento') }}" placeholder="Ej: 8.5" required min="0" max="10">
                                    @error('rendimiento')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">0-4: Bajo | 5-7: Medio | 8-10: Alto</small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="eficiencia" class="form-label">Eficiencia (%) <span class="text-muted">(opcional)</span></label>
                                    <input type="number" name="eficiencia" id="eficiencia" class="filtro-select w-100" value="{{ old('eficiencia') }}" placeholder="Ej: 85" min="0" max="100">
                                    @error('eficiencia')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea name="observaciones" id="observaciones" class="filtro-select w-100" rows="4" placeholder="Notas sobre el rendimiento, problemas encontrados, etc...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('evaluaciones.index') }}" class="btn-outline-verde">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn-naranja">
                                    <i class="fas fa-save"></i> Registrar Evaluación
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
