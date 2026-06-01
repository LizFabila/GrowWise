@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
            <p class="text-muted">Revisa los productos que has seleccionado</p>
        </div>

        @if(empty($carrito))
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h3>Tu carrito está vacío</h3>
                    <a href="{{ route('cliente.tienda.index') }}" class="btn-naranja mt-3">Ir a la tienda</a>
                </div>
            </div>
        @else
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        @foreach($carrito as $id => $item)
                            <div class="row align-items-center mb-3 pb-3 border-bottom">
                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-0">{{ $item['nombre'] }}</h6>
                                    <small class="text-muted">Vendedor: {{ $item['vendedor_nombre'] }}</small>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-0">${{ number_format($item['precio'], 2) }} / {{ $item['unidad'] }}</p>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control cantidad-input" data-id="{{ $id }}" value="{{ $item['cantidad'] }}" min="1" max="{{ $item['stock'] }}" style="width: 80px;">
                                </div>
                                <div class="col-md-2 text-end">
                                    <form action="{{ route('cliente.carrito.eliminar', $id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <p class="mb-0 text-end"><strong>Subtotal:</strong> ${{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Resumen</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-total">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span>Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success" id="total-total">${{ number_format($total, 2) }}</strong>
                        </div>
                        <a href="{{ route('cliente.checkout.index') }}" class="btn-naranja w-100">
                            <i class="fas fa-credit-card"></i> Proceder al pago
                        </a>

                        <!-- Botón que abre el modal -->
                        <button type="button" id="btnVaciarCarrito" class="btn-outline-verde w-100 mt-2">
                            <i class="fas fa-trash-alt"></i> Vaciar carrito
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Actualizar cantidad con fetch
        document.querySelectorAll('.cantidad-input').forEach(input => {
            input.addEventListener('change', function() {
                const productoId = this.dataset.id;
                const nuevaCantidad = this.value;

                fetch('{{ route("cliente.carrito.actualizar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: productoId,
                        cantidad: nuevaCantidad
                    })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error al actualizar el carrito');
                        location.reload();
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el carrito');
                });
            });
        });
    </script>

    <!-- Modal de confirmación para vaciar carrito -->
    <div class="modal fade" id="modalVaciarCarrito" tabindex="-1" aria-labelledby="modalVaciarCarritoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger" id="modalVaciarCarritoLabel">
                        <i class="fas fa-trash-alt me-2"></i>Vaciar carrito
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3 d-block"></i>
                    <p class="fs-5">¿Estás seguro de que deseas <strong class="text-danger">vaciar todo el carrito</strong>?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                    <button type="button" class="btn-outline-verde px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form id="formVaciarCarritoModal" action="{{ route('cliente.carrito.vaciar') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-naranja px-4">
                            <i class="fas fa-trash-alt me-2"></i>Sí, vaciar carrito
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Evento para el botón de vaciar carrito (abre modal)
        document.getElementById('btnVaciarCarrito')?.addEventListener('click', function(e) {
            e.preventDefault();
            var myModal = new bootstrap.Modal(document.getElementById('modalVaciarCarrito'));
            myModal.show();
        });
    </script>
@endsection
