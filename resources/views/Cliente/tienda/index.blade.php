@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success">🌱 Tienda de productos</h1>
            <p class="text-muted">Cultivos frescos directamente de los productores</p>
        </div>

        @forelse($productos as $producto)
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="producto-card">
                    <div class="producto-imagen">
                        <i class="fas fa-seedling"></i>
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
                            <a href="{{ route('cliente.checkout.index', $producto->id) }}" class="btn-outline-verde w-100 text-center mt-2 d-block">
                                <i class="fas fa-bolt"></i> Comprar ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-store-slash fa-4x text-muted mb-3"></i>
                    <h3>No hay productos disponibles</h3>
                    <p>Pronto habrá nuevos productos</p>
                </div>
            </div>
        @endforelse

        <div class="col-12 mt-4">
            {{ $productos->links() }}
        </div>
    </div>
@endsection
