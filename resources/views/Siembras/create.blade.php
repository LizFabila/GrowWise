@extends('layouts.app')

@section('header-title')
    <h1>Nueva Siembra</h1>
    <p>Registra una nueva siembra en tu invernadero</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-sprout"></i> Registrar Siembra</h2>
                    </div>

                    <form action="{{ route('siembras.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cultivo_id" class="form-label">Cultivo *</label>
                                <select name="cultivo_id" id="cultivo_id" class="form-control" required>
                                    <option value="">Selecciona un cultivo</option>
                                    @foreach($cultivos as $cultivo)
                                        <option value="{{ $cultivo->id }}" {{ old('cultivo_id') == $cultivo->id ? 'selected' : '' }}>
                                            {{ $cultivo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cultivo_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modulo_id" class="form-label">Módulo *</label>
                                <select name="modulo_id" id="modulo_id" class="form-control" required>
                                    <option value="">Selecciona un módulo</option>
                                    @foreach($modulos as $modulo)
                                        <option value="{{ $modulo->id }}" {{ old('modulo_id') == $modulo->id ? 'selected' : '' }}>
                                            {{ $modulo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('modulo_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="charola" class="form-label">Número de Charola *</label>
                                <input type="number" name="charola" id="charola" class="form-control" value="{{ old('charola') }}" required min="1">
                                @error('charola')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_siembra" class="form-label">Fecha de Siembra *</label>
                                <input type="date" name="fecha_siembra" id="fecha_siembra" class="form-control" value="{{ old('fecha_siembra', now()->format('Y-m-d')) }}" required>
                                @error('fecha_siembra')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="fecha_estimada_cosecha" class="form-label">Fecha Estimada de Cosecha</label>
                                <input type="date" name="fecha_estimada_cosecha" id="fecha_estimada_cosecha" class="form-control" value="{{ old('fecha_estimada_cosecha') }}">
                                <small class="text-muted">Dejar en blanco para calcular automáticamente según los días del cultivo.</small>
                                @error('fecha_estimada_cosecha')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('siembras.index') }}" class="btn-outline-verde">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn-naranja">
                                <i class="fas fa-save"></i> Guardar Siembra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
