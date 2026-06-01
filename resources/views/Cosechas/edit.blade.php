@extends('layouts.app')

@section('header-title')
    <h1>Editar Cosecha</h1>
    <p>Modifica los datos de la cosecha</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container" data-aos="fade-up">
                    <div class="table-header">
                        <h2><i class="fas fa-edit"></i> Editar Cosecha #{{ str_pad($cosecha->id, 3, '0', STR_PAD_LEFT) }}</h2>
                    </div>

                    <!-- Información de la siembra -->
                    <div class="alert-info p-3 rounded-3 mb-4">
                        <h5><i class="fas fa-seedling"></i> Información de la siembra</h5>
                        <p class="mb-1"><strong>Cultivo:</strong> {{ $cosecha->siembra->cultivo->nombre }}</p>
                        <p class="mb-1"><strong>Módulo:</strong> {{ $cosecha->siembra->modulo->nombre ?? 'N/A' }} - Charola {{ $cosecha->siembra->charola }}</p>
                        <p class="mb-0"><strong>Fecha de siembra:</strong> {{ \Carbon\Carbon::parse($cosecha->siembra->fecha_siembra)->format('d/m/Y') }}</p>
                    </div>

                    <form action="{{ route('cosechas.update', $cosecha->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_cosecha" class="form-label">Fecha de Cosecha *</label>
                                <input type="date" name="fecha_cosecha" id="fecha_cosecha" class="filtro-select w-100" value="{{ old('fecha_cosecha', \Carbon\Carbon::parse($cosecha->fecha_cosecha)->format('Y-m-d')) }}" required>
                                @error('fecha_cosecha')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cantidad_kg" class="form-label">Cantidad (kg) *</label>
                                <input type="number" step="0.01" name="cantidad_kg" id="cantidad_kg" class="filtro-select w-100" value="{{ old('cantidad_kg', $cosecha->cantidad_kg) }}" required>
                                @error('cantidad_kg')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="calidad" class="form-label">Calidad *</label>
                                <select name="calidad" id="calidad" class="filtro-select w-100" required>
                                    <option value="Excelente" {{ old('calidad', $cosecha->calidad) == 'Excelente' ? 'selected' : '' }}>Excelente</option>
                                    <option value="Buena" {{ old('calidad', $cosecha->calidad) == 'Buena' ? 'selected' : '' }}>Buena</option>
                                    <option value="Regular" {{ old('calidad', $cosecha->calidad) == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Mala" {{ old('calidad', $cosecha->calidad) == 'Mala' ? 'selected' : '' }}>Mala</option>
                                </select>
                                @error('calidad')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="filtro-select w-100" rows="4" placeholder="Notas sobre la cosecha...">{{ old('observaciones', $cosecha->observaciones) }}</textarea>
                                @error('observaciones')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <a href="{{ route('cosechas.index') }}" class="btn-outline-verde">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="button" class="btn-outline-verde ms-2" style="border-color: #dc3545; color: #dc3545;" onclick="if(confirm('¿Eliminar esta cosecha?')) { document.getElementById('delete-form').submit(); }">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                            <button type="submit" class="btn-naranja">
                                <i class="fas fa-save"></i> Actualizar Cosecha
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('cosechas.destroy', $cosecha->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
