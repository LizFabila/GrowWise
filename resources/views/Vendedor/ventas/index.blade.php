@extends('vendedor.layouts.app')

@section('header-title', '💰 Historial de Ventas')
@section('header-subtitle', 'Todas las transacciones realizadas')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>${{ number_format($totalVendido, 2) }}</h3>
                <p>Total Vendido</p>
            </div>
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $totalVentas }}</h3>
                <p>Transacciones</p>
            </div>
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h5 class="fw-bold"><i class="fas fa-list"></i> Ventas realizadas</h5>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Venta</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($ventas as $venta)
                    @foreach($venta->pedido->detalles as $detalle)
                        <tr>
                            <td>#{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</td>
                            <td>{{ $detalle->producto->cultivo->nombre }}</td>
                            <td>{{ $detalle->cantidad }} {{ $detalle->producto->unidad }}</td>
                            <td class="text-success fw-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('vendedor.ventas.detalle', $venta->id) }}" class="btn-outline-verde btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-2 d-block"></i>
                            <p>No hay ventas registradas</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $ventas->links() }}
        </div>
    </div>
@endsection
