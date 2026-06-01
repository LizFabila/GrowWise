@extends('vendedor.layouts.app')

@section('header-title', '📄 Detalle de Venta')
@section('header-subtitle', 'Información completa de la transacción')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <h5 class="fw-bold mb-3"><i class="fas fa-receipt"></i> Venta #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}</h5>
                <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i:s') }}</p>
                <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</p>
                <p><strong>Estado:</strong> <span class="badge bg-success rounded-pill">{{ $venta->estado }}</span></p>

                <hr>

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
                        @foreach($venta->pedido->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->cultivo->nombre }} ({{ $detalle->producto->unidad }})</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                            <td>${{ number_format($venta->pedido->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>IVA (16%):</strong></td>
                            <td>${{ number_format($venta->pedido->impuesto, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong class="text-success">Total:</strong></td>
                            <td><strong class="text-success">${{ number_format($venta->total, 2) }}</strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>

                <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt"></i> Dirección de envío</h5>
                <p>{{ $venta->pedido->direccion->direccion_completa }}</p>
                <p><strong>Método de pago:</strong> {{ $venta->pedido->metodoPago->nombre }}</p>

                <a href="{{ route('vendedor.ventas.index') }}" class="btn-outline-verde mt-3">
                    <i class="fas fa-arrow-left"></i> Volver a ventas
                </a>
            </div>
        </div>
    </div>
@endsection
