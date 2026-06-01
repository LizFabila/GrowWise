@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-edit"></i> Editar dirección</h1>
            <p class="text-muted">Modifica los datos de tu dirección de envío</p>
        </div>

        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('cliente.direcciones.update', $direccion->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="calle" class="form-label">Calle *</label>
                                <input type="text" name="calle" id="calle" class="form-control rounded-pill" value="{{ old('calle', $direccion->calle) }}" required>
                                @error('calle') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="numero" class="form-label">Número *</label>
                                <input type="text" name="numero" id="numero" class="form-control rounded-pill" value="{{ old('numero', $direccion->numero) }}" required>
                                @error('numero') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="colonia" class="form-label">Colonia *</label>
                                <input type="text" name="colonia" id="colonia" class="form-control rounded-pill" value="{{ old('colonia', $direccion->colonia) }}" required>
                                @error('colonia') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <input type="text" name="ciudad" id="ciudad" class="form-control rounded-pill" value="{{ old('ciudad', $direccion->ciudad) }}" required>
                                @error('ciudad') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <input type="text" name="estado" id="estado" class="form-control rounded-pill" value="{{ old('estado', $direccion->estado) }}" required>
                                @error('estado') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="codigo_postal" class="form-label">Código Postal *</label>
                                <input type="text" name="codigo_postal" id="codigo_postal" class="form-control rounded-pill" value="{{ old('codigo_postal', $direccion->codigo_postal) }}" required>
                                @error('codigo_postal') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="referencias" class="form-label">Referencias (opcional)</label>
                                <textarea name="referencias" id="referencias" class="form-control rounded-4" rows="3">{{ old('referencias', $direccion->referencias) }}</textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="principal" id="principal" class="form-check-input" value="1" {{ old('principal', $direccion->principal) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="principal">
                                        Establecer como dirección principal
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-naranja">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                            <a href="{{ route('cliente.direcciones.index') }}" class="btn-outline-verde ms-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
