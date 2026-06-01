@extends('vendedor.layouts.app')

@section('header-title', '📦 Mis Productos')
@section('header-subtitle', 'Gestiona tus productos en venta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold"><i class="fas fa-tags"></i> Listado de productos</h5>
        <a href="{{ route('vendedor.productos.crear') }}" class="btn-naranja">
            <i class="fas fa-plus-circle"></i> + Nueva Publicación
        </a>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" style="border: none; gap: 10px;">
        <li class="nav-item">
            <a class="btn-naranja active" id="tab-en-venta-btn" onclick="mostrarTab('en-venta')" style="cursor: pointer;">En venta</a>
        </li>
        <li class="nav-item">
            <a class="btn-outline-verde" id="tab-inventario-btn" onclick="mostrarTab('inventario')" style="cursor: pointer;">Inventario</a>
        </li>
    </ul>

    <!-- Tab: En Venta -->
    <div id="tab-en-venta">
        <div class="row">
            @forelse($productosEnVenta as $producto)
                <div class="col-md-4 mb-4">
                    <div class="producto-card">
                        <div class="producto-header">
                            <h5 class="mb-0">{{ $producto->cultivo->nombre }}</h5>
                            <span class="badge-venta badge-disponible">En venta</span>
                        </div>
                        <div class="producto-body">
                            <p><strong>Cantidad disponible:</strong> {{ number_format($producto->stock, 2) }} {{ $producto->unidad }}</p>
                            <p><strong>Precio unitario:</strong> ${{ number_format($producto->precio_unitario, 2) }} MXN</p>
                            <p class="text-muted small"><i class="fas fa-calendar-alt"></i> Publicación: {{ $producto->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="producto-footer">
                            <a href="{{ route('vendedor.productos.editar', $producto->id) }}" class="btn-outline-verde w-100 text-center">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('vendedor.productos.destroy', $producto->id) }}" method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 rounded-pill" onclick="return confirm('¿Eliminar este producto?')">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-store fa-4x text-muted mb-3"></i>
                        <h3>No tienes productos en venta</h3>
                        <a href="{{ route('vendedor.productos.crear') }}" class="btn-naranja mt-3">+ Nueva Publicación</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tab: Inventario (Agotados) -->
    <div id="tab-inventario" style="display: none;">
        <div class="row">
            @forelse($productosInventario as $producto)
                <div class="col-md-4 mb-4">
                    <div class="producto-card">
                        <div class="producto-header" style="background: #6c757d;">
                            <h5 class="mb-0">{{ $producto->cultivo->nombre }}</h5>
                            <span class="badge-venta badge-agotado">Agotado</span>
                        </div>
                        <div class="producto-body">
                            <p><strong>Cantidad disponible:</strong> {{ number_format($producto->stock, 2) }} {{ $producto->unidad }}</p>
                            <p><strong>Precio unitario:</strong> ${{ number_format($producto->precio_unitario, 2) }} MXN</p>
                            <p class="text-muted small"><i class="fas fa-calendar-alt"></i> Publicación: {{ $producto->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="producto-footer">
                            <a href="{{ route('vendedor.productos.editar', $producto->id) }}" class="btn-outline-verde w-100 text-center">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('vendedor.productos.destroy', $producto->id) }}" method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 rounded-pill" onclick="return confirm('¿Eliminar este producto?')">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h3>No hay productos agotados</h3>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function mostrarTab(tab) {
            document.getElementById('tab-en-venta').style.display = 'none';
            document.getElementById('tab-inventario').style.display = 'none';
            document.getElementById('tab-' + tab).style.display = 'block';

            const btnEnVenta = document.getElementById('tab-en-venta-btn');
            const btnInventario = document.getElementById('tab-inventario-btn');

            btnEnVenta.className = 'btn-outline-verde';
            btnInventario.className = 'btn-outline-verde';

            if (tab === 'en-venta') btnEnVenta.className = 'btn-naranja';
            if (tab === 'inventario') btnInventario.className = 'btn-naranja';
        }
    </script>
@endsection
