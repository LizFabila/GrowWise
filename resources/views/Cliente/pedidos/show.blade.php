@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-receipt"></i> Pedido #{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-box"></i> Productos</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pedido->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->cultivo->nombre }} ({{ $detalle->producto->unidad }})</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle"></i> Información</h5>
                    <p><strong>Estado:</strong>
                        <span class="badge bg-{{ $pedido->estado == 'confirmado' ? 'success' : ($pedido->estado == 'pendiente' ? 'warning' : 'secondary') }} rounded-pill">
                        {{ ucfirst($pedido->estado) }}
                    </span>
                    </p>
                    <p><strong>Método de pago:</strong> {{ $pedido->metodoPago->nombre }}</p>
                    <hr>
                    <p><strong>Subtotal:</strong> ${{ number_format($pedido->subtotal, 2) }}</p>
                    <p><strong>IVA (16%):</strong> ${{ number_format($pedido->impuesto, 2) }}</p>
                    <p><strong class="text-success">Total:</strong> <strong class="text-success">${{ number_format($pedido->total_final, 2) }}</strong></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt"></i> Dirección de envío</h5>
                    <p>{{ $pedido->direccion->direccion_completa }}</p>
                    @if($pedido->notas)
                        <hr>
                        <h5>Notas:</h5>
                        <p>{{ $pedido->notas }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
