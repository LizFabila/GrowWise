@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-tags"></i> Categorías</h1>
            <p class="text-muted">Explora nuestros productos por categoría</p>
        </div>

        <div class="row">
            @foreach($categorias as $categoria)
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 rounded-4 text-center h-100" data-aos="fade-up">
                        <div class="card-body p-4">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-seedling fa-3x text-success"></i>
                            </div>
                            <h5 class="fw-bold">{{ $categoria->tipo }}</h5>
                            <a href="{{ route('cliente.tienda.index') }}?categoria={{ $categoria->tipo }}" class="btn-outline-verde mt-2 d-inline-block">
                                Ver productos
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
