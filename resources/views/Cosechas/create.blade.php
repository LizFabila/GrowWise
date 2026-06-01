@extends('layouts.app')

@section('header-title')
    <h1>Registrar Nueva Cosecha</h1>
    <p>Registra la cosecha de tus cultivos</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-carrot"></i> Registrar Cosecha</h2>
                    </div>

                    <form action="{{ route('cosechas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="siembra_id" class="form-label">Siembra *</label>
                            <select name="siembra_id" id="siembra_id" class="filtro-select w-100" required>
                                <option value="">Selecciona una siembra</option>
                                @foreach($siembras as $siembra)
                                    <option value="{{ $siembra->id }}" {{ old('siembra_id') == $siembra->id ? 'selected' : '' }}>
                                        {{ $siembra->cultivo->nombre }} - Sembrado: {{ \Carbon\Carbon::parse($siembra->fecha_siembra)->format('d/m/Y') }}
                                        ({{ $siembra->modulo->nombre ?? 'Sin módulo' }} - Charola {{ $siembra->charola }})
                                    </option>
                                @endforeach
                            </select>
                            @error('siembra_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_cosecha" class="form-label">Fecha de Cosecha *</label>
                                <input type="date" name="fecha_cosecha" id="fecha_cosecha" class="filtro-select w-100" value="{{ old('fecha_cosecha', date('Y-m-d')) }}" required>
                                @error('fecha_cosecha')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cantidad_kg" class="form-label">Cantidad (kg) *</label>
                                <input type="number" step="0.01" name="cantidad_kg" id="cantidad_kg" class="filtro-select w-100" value="{{ old('cantidad_kg') }}" placeholder="Ej: 1.5" required>
                                @error('cantidad_kg')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="calidad" class="form-label">Calidad *</label>
                                <select name="calidad" id="calidad" class="filtro-select w-100" required>
                                    <option value="">Selecciona una calidad</option>
                                    <option value="Excelente" {{ old('calidad') == 'Excelente' ? 'selected' : '' }}>⭐ Excelente</option>
                                    <option value="Buena" {{ old('calidad') == 'Buena' ? 'selected' : '' }}>👍 Buena</option>
                                    <option value="Regular" {{ old('calidad') == 'Regular' ? 'selected' : '' }}>👌 Regular</option>
                                    <option value="Mala" {{ old('calidad') == 'Mala' ? 'selected' : '' }}>⚠️ Mala</option>
                                </select>
                                @error('calidad')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="filtro-select w-100" rows="4" placeholder="Notas sobre la cosecha...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('cosechas.index') }}" class="btn-outline-verde">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn-naranja">
                                <i class="fas fa-save"></i> Registrar Cosecha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
