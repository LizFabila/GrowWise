@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-fire"></i> Ofertas especiales</h1>
            <p class="text-muted">Los productos más populares con los mejores precios</p>
        </div>

        @forelse($ofertas as $producto)
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="producto-card">
                    <div class="producto-imagen">
                        <i class="fas fa-seedling"></i>
                        <span class="position-absolute top-0 end-0 bg-danger text-white rounded-pill px-3 py-1 m-2">Oferta</span>
                    </div>
                    <div class="producto-body">
                        <h5 class="fw-bold">{{ $producto->cultivo->nombre }}</h5>
                        <p class="text-muted small">
                            <i class="fas fa-user"></i> {{ $producto->user->nombre }} {{ $producto->user->apellido }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="producto-precio">${{ number_format($producto->precio_unitario, 2) }}</span>
                            <span class="badge bg-success rounded-pill">{{ $producto->stock }} {{ $producto->unidad }}</span>
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('cliente.carrito.agregar') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="number" name="cantidad" class="form-control" style="width: 80px;" value="1" min="1" max="{{ $producto->stock }}">
                                <button type="submit" class="btn-naranja flex-grow-1">
                                    <i class="fas fa-cart-plus"></i> Agregar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-fire fa-4x text-muted mb-3"></i>
                    <h3>No hay ofertas disponibles</h3>
                    <p>Pronto tendremos nuevas promociones</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
