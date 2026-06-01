@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-box"></i> Mis Pedidos</h1>
            <p class="text-muted">Historial de tus compras</p>
        </div>

        @forelse($pedidos as $pedido)
            <div class="col-12 mb-3">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>Pedido #{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</strong><br>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted">Vendedor:</span><br>
                                <strong>{{ $pedido->vendedor->nombre }} {{ $pedido->vendedor->apellido }}</strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted">Total:</span><br>
                                <strong class="text-success">${{ number_format($pedido->total_final, 2) }}</strong>
                            </div>
                            <div class="col-md-2">
                        <span class="badge bg-{{ $pedido->estado == 'confirmado' ? 'success' : ($pedido->estado == 'pendiente' ? 'warning' : 'secondary') }} rounded-pill">
                            {{ ucfirst($pedido->estado) }}
                        </span>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ route('cliente.pedidos.show', $pedido->id) }}" class="btn-outline-verde btn-sm">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h3>No has realizado compras</h3>
                    <a href="{{ route('cliente.tienda.index') }}" class="btn-naranja mt-3">Ir a la tienda</a>
                </div>
            </div>
        @endforelse

        <div class="col-12 mt-3">
            {{ $pedidos->links() }}
        </div>
    </div>
@endsection
