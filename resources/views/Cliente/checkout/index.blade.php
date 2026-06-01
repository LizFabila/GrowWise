@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-credit-card"></i> Revisar y confirmar tu compra</h1>
            <p class="text-muted">Verifica los detalles de tu pedido</p>
        </div>

        @php $producto = $items[0]['producto']; @endphp

        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-seedling text-success"></i> Producto seleccionado</h5>
                    <h3 class="text-success">{{ $producto->cultivo->nombre }}</h3>
                    <p class="h4">${{ number_format($producto->precio_unitario, 2) }} MXN por {{ $producto->unidad }}</p>

                    <div class="mt-4">
                        <label class="form-label fw-bold">Cantidad ({{ $producto->unidad }})</label>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-outline-secondary rounded-circle" onclick="cambiarCantidad(-1)">-</button>
                            <input type="number" id="cantidad" value="1" min="1" max="{{ $producto->stock }}" class="form-control text-center" style="width: 100px;">
                            <button class="btn btn-outline-secondary rounded-circle" onclick="cambiarCantidad(1)">+</button>
                            <span class="text-muted">Disponibles: {{ number_format($producto->stock, 2) }} {{ $producto->unidad }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-file-invoice-dollar"></i> Resumen de compra</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">${{ number_format($producto->precio_unitario, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-success" id="total">${{ number_format($producto->precio_unitario, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt"></i> Dirección de envío</h5>

                    @if($direcciones->isEmpty())
                        <div class="alert alert-warning">No tienes direcciones registradas.</div>
                        <a href="{{ route('cliente.direcciones.create') }}" class="btn-outline-verde w-100 text-center">+ Agregar nueva dirección</a>
                    @else
                        <select id="direccion_id" class="form-select mb-3 rounded-pill">
                            @foreach($direcciones as $direccion)
                                <option value="{{ $direccion->id }}" {{ $direccion->principal ? 'selected' : '' }}>
                                    {{ $direccion->direccion_completa }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-center">
                            <a href="{{ route('cliente.direcciones.create') }}" class="small">+ Agregar nueva dirección</a>
                        </div>
                    @endif

                    <hr>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Método de pago</label>
                        <select id="metodo_pago_id" class="form-select rounded-pill">
                            @foreach($metodosPago as $metodo)
                                <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn-naranja w-100 mt-4" onclick="confirmarCompra()">
                        <i class="fas fa-check-circle"></i> Confirmar compra
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let precioUnitario = {{ $producto->precio_unitario }};
        let stock = {{ $producto->stock }};
        let cantidadInput = document.getElementById('cantidad');
        let subtotalSpan = document.getElementById('subtotal');
        let totalSpan = document.getElementById('total');

        function actualizarPrecio() {
            let cantidad = parseInt(cantidadInput.value);
            if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
            if (cantidad > stock) cantidad = stock;
            cantidadInput.value = cantidad;
            let subtotal = cantidad * precioUnitario;
            subtotalSpan.innerText = '$' + subtotal.toFixed(2);
            totalSpan.innerText = '$' + subtotal.toFixed(2);
        }

        function cambiarCantidad(delta) {
            let nueva = parseInt(cantidadInput.value) + delta;
            if (nueva >= 1 && nueva <= stock) {
                cantidadInput.value = nueva;
                actualizarPrecio();
            }
        }

        cantidadInput.addEventListener('change', actualizarPrecio);
        cantidadInput.addEventListener('input', actualizarPrecio);

        function confirmarCompra() {
            let cantidad = document.getElementById('cantidad').value;
            let direccionId = document.getElementById('direccion_id')?.value;
            let metodoPagoId = document.getElementById('metodo_pago_id').value;

            if (!direccionId) {
                alert('Selecciona una dirección de envío');
                return;
            }

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            btn.disabled = true;

            fetch('{{ route("cliente.checkout.procesar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    producto_id: {{ $producto->id }},
                    cantidad: cantidad,
                    direccion_id: direccionId,
                    metodo_pago_id: metodoPagoId
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    window.location.href = '{{ route("cliente.pedidos.index") }}';
                } else {
                    alert(data.error || 'Error al procesar la compra');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }).catch(error => {
                alert('Error de conexión');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
@endsection
