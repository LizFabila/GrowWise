@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-seedling fa-6x text-success opacity-50" style="font-size: 8rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h1 class="fw-bold text-success">{{ $producto->cultivo->nombre }}</h1>
                    <p class="text-muted">
                        <i class="fas fa-user"></i> Vendedor: {{ $producto->user->nombre }} {{ $producto->user->apellido }}
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-calendar-alt"></i> Publicado: {{ $producto->created_at->format('d/m/Y') }}
                    </p>

                    <h2 class="text-success mt-3">${{ number_format($producto->precio_unitario, 2) }} MXN</h2>
                    <p class="text-muted">por {{ $producto->unidad }}</p>

                    <div class="mt-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="fw-bold">Disponibilidad:</span>
                            <span class="badge bg-success rounded-pill">{{ $producto->stock }} {{ $producto->unidad }} disponibles</span>
                        </div>

                        <form action="{{ route('cliente.carrito.agregar') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                            <div class="row g-3">
                                <div class="col-4">
                                    <input type="number" name="cantidad" class="form-control rounded-pill" value="1" min="1" max="{{ $producto->stock }}">
                                </div>
                                <div class="col-8">
                                    <button type="submit" class="btn-naranja w-100">
                                        <i class="fas fa-cart-plus"></i> Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('cliente.checkout.index', $producto->id) }}" class="btn-outline-verde w-100 text-center mt-2 d-block">
                            <i class="fas fa-bolt"></i> Comprar ahora
                        </a>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold">Descripción</h5>
                    <p>{{ $producto->cultivo->descripcion ?? 'Producto fresco cultivado con métodos hidropónicos. Libre de pesticidas y 100% natural.' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
