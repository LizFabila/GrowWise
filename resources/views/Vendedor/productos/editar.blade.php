@extends('vendedor.layouts.app')

@section('header-title', '✏️ Editar producto')
@section('header-subtitle', 'Actualiza la información de tu producto')

@section('content')
    <div class="table-container">
        <form action="{{ route('vendedor.productos.update', $producto->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cultivo</label>
                    <input type="text" class="form-control rounded-pill" value="{{ $producto->cultivo->nombre }}" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Publicado el</label>
                    <input type="text" class="form-control rounded-pill" value="{{ $producto->created_at->format('d/m/Y H:i:s') }}" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="precio_unitario" class="form-label">Precio unitario (MXN) *</label>
                    <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" class="form-control rounded-pill" value="{{ $producto->precio_unitario }}" required>
                    @error('precio_unitario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock disponible *</label>
                    <input type="number" name="stock" id="stock" class="form-control rounded-pill" value="{{ $producto->stock }}" required>
                    @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="alert alert-info mt-2">
                <i class="fas fa-info-circle"></i> La unidad ({{ $producto->unidad }}) y la cantidad no se pueden modificar después de publicar.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-naranja">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
                <a href="{{ route('vendedor.productos.index') }}" class="btn-outline-verde ms-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
