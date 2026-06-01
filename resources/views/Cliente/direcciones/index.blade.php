@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-map-marker-alt"></i> Mis Direcciones</h1>
            <p class="text-muted">Gestiona tus direcciones de envío</p>
            <a href="{{ route('cliente.direcciones.create') }}" class="btn-naranja">
                <i class="fas fa-plus"></i> Nueva dirección
            </a>
        </div>

        @forelse($direcciones as $direccion)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold">{{ $direccion->direccion_completa }}</h5>
                                @if($direccion->referencias)
                                    <p class="text-muted small mt-2"><i class="fas fa-info-circle"></i> {{ $direccion->referencias }}</p>
                                @endif
                                @if($direccion->principal)
                                    <span class="badge bg-success rounded-pill">Principal</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('cliente.direcciones.edit', $direccion->id) }}" class="btn btn-sm btn-warning rounded-pill">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cliente.direcciones.destroy', $direccion->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('¿Eliminar esta dirección?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if(!$direccion->principal)
                            <div class="mt-3">
                                <form action="{{ route('cliente.direcciones.principal', $direccion->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-outline-verde btn-sm">
                                        <i class="fas fa-star"></i> Establecer como principal
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                    <h3>No tienes direcciones registradas</h3>
                    <a href="{{ route('cliente.direcciones.create') }}" class="btn-naranja mt-3">Agregar dirección</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
