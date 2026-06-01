@extends('vendedor.layouts.app')

@section('header-title', '➕ Publicar nuevo producto')
@section('header-subtitle', 'Ofrece tus cultivos en la tienda')

@section('content')
    <div class="table-container">
        <form action="{{ route('vendedor.productos.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cultivo_id" class="form-label">Cultivo *</label>
                    <select name="cultivo_id" id="cultivo_id" class="form-select rounded-pill" required>
                        <option value="">Selecciona un cultivo</option>
                        @foreach($cultivos as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ old('cultivo_id') == $cultivo->id ? 'selected' : '' }}>
                                {{ $cultivo->nombre }} ({{ $cultivo->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('cultivo_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cosecha_id" class="form-label">Cosecha (opcional)</label>
                    <select name="cosecha_id" id="cosecha_id" class="form-select rounded-pill">
                        <option value="">Selecciona una cosecha</option>
                        @foreach($cosechas as $cosecha)
                            <option value="{{ $cosecha->id }}">
                                {{ $cosecha->cultivo->nombre ?? 'Cultivo' }} - {{ $cosecha->fecha_cosecha }} ({{ $cosecha->cantidad_kg }} kg)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="cantidad" class="form-label">Cantidad *</label>
                    <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control rounded-pill" value="{{ old('cantidad', 1) }}" required>
                    @error('cantidad') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="unidad" class="form-label">Unidad *</label>
                    <select name="unidad" id="unidad" class="form-select rounded-pill" required>
                        <option value="kg" {{ old('unidad') == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                        <option value="g" {{ old('unidad') == 'g' ? 'selected' : '' }}>Gramos (g)</option>
                        <option value="pieza" {{ old('unidad') == 'pieza' ? 'selected' : '' }}>Pieza</option>
                    </select>
                    @error('unidad') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="precio_unitario" class="form-label">Precio unitario (MXN) *</label>
                    <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" class="form-control rounded-pill" value="{{ old('precio_unitario') }}" required>
                    @error('precio_unitario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="stock" class="form-label">Stock disponible *</label>
                    <input type="number" name="stock" id="stock" class="form-control rounded-pill" value="{{ old('stock', 1) }}" required>
                    @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-naranja">
                    <i class="fas fa-check"></i> Publicar producto
                </button>
                <a href="{{ route('vendedor.productos.index') }}" class="btn-outline-verde ms-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
