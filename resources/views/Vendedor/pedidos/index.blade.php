@extends('vendedor.layouts.app')

@section('header-title', '📦 Pedidos Recibidos')
@section('header-subtitle', 'Gestiona los pedidos de tus clientes')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h5 class="fw-bold"><i class="fas fa-list"></i> Listado de pedidos</h5>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pedidos as $pedido)
                    <tr>
                        <td>#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $pedido->cliente->nombre }} {{ $pedido->cliente->apellido }}</td>
                        <td>
                            @foreach($pedido->detalles as $detalle)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill me-1 mb-1">
                                {{ $detalle->cantidad }} {{ $detalle->producto->unidad }} {{ $detalle->producto->cultivo->nombre }}
                            </span>
                            @endforeach
                        </td>
                        <td class="text-success fw-bold">${{ number_format($pedido->total_final, 2) }}</td>
                        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <select class="form-select form-select-sm rounded-pill" style="width: 120px;" onchange="actualizarEstado({{ $pedido->id }}, this.value)">
                                <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>📋 Pendiente</option>
                                <option value="confirmado" {{ $pedido->estado == 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                                <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>🚚 Enviado</option>
                                <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>📦 Entregado</option>
                                <option value="cancelado" {{ $pedido->estado == 'cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                            </select>
                        </td>
                        <td>
                            <a href="{{ route('vendedor.ventas.detalle', $pedido->id) }}" class="btn-outline-verde btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-truck fa-3x text-muted mb-2 d-block"></i>
                            <p>No hay pedidos recibidos</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pedidos->links() }}
        </div>
    </div>

    <script>
        function actualizarEstado(pedidoId, estado) {
            fetch('{{ url("vendedor/pedidos") }}/' + pedidoId + '/estado', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: estado })
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al actualizar el estado');
                }
            });
        }
    </script>
@endsection
