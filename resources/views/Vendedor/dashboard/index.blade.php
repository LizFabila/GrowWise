@extends('vendedor.layouts.app')

@section('header-title', 'Panel de Control')
@section('header-subtitle', 'Resumen de tu negocio')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>${{ number_format($totalVentas, 2) }}</h3>
                <p>Total Ventas</p>
            </div>
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $totalPedidos }}</h3>
                <p>Pedidos Recibidos</p>
            </div>
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $productosPublicados }}</h3>
                <p>Productos en Venta</p>
            </div>
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $productosAgotados }}</h3>
                <p>Productos Agotados</p>
            </div>
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
        </div>
    </div>

    <!-- Producción por cultivo (datos reales) -->
    <div class="table-container mb-4">
        <h5 class="fw-bold"><i class="fas fa-seedling"></i> Producción por cultivo (por ciclo)</h5>
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                <tr>
                    <th>Cultivo</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Ingreso por ciclo</th>
                    <th>Días a cosecha</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><strong>Lechuga</strong></td>
                    <td>12 manojos</td>
                    <td>$28.00</td>
                    <td class="text-success">$336.00</td>
                    <td>55 días</td>
                </tr>
                <tr>
                    <td><strong>Espinaca</strong></td>
                    <td>8 manojos</td>
                    <td>$22.00</td>
                    <td class="text-success">$176.00</td>
                    <td>35 días</td>
                </tr>
                <tr>
                    <td><strong>Rábano</strong></td>
                    <td>8 manojos</td>
                    <td>$20.00</td>
                    <td class="text-success">$160.00</td>
                    <td>25 días</td>
                </tr>
                <tr>
                    <td><strong>Cilantro</strong></td>
                    <td>5 manojos</td>
                    <td>$18.00</td>
                    <td class="text-success">$90.00</td>
                    <td>15 días</td>
                </tr>
                </tbody>
                <tfoot class="fw-bold">
                <tr><td colspan="3" class="text-end">Total por ciclo:</td><td class="text-success">$762.00</td><td></td></tr>
                <tr><td colspan="3" class="text-end">Costo producción:</td><td class="text-warning">$195.00</td><td></td></tr>
                <tr><td colspan="3" class="text-end">Utilidad neta por ciclo:</td><td class="text-success fw-bold">$567.00</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- GRÁFICA 1: Proyección de ventas por cultivo -->
    <div class="table-container mb-4">
        <div class="table-header">
            <h5 class="fw-bold"><i class="fas fa-chart-bar"></i> Proyección de Ingresos por Cultivo</h5>
        </div>
        <canvas id="ventasPorCultivoChart" height="300"></canvas>
        <div class="legend-container mt-3 d-flex justify-content-center gap-4 flex-wrap" id="legend"></div>
    </div>

    <!-- GRÁFICA 2: Ventas por mes histórico + Pedidos recientes -->
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="table-container">
                <div class="table-header">
                    <h5 class="fw-bold"><i class="fas fa-chart-line"></i> Ventas por Mes (Histórico)</h5>
                </div>
                @if($ventasPorMes->count() > 0)
                    <canvas id="ventasChart" height="250"></canvas>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-2"></i>
                        <p>No hay datos de ventas disponibles</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="table-container">
                <div class="table-header">
                    <h5 class="fw-bold"><i class="fas fa-truck"></i> Pedidos Recientes</h5>
                    <a href="{{ route('vendedor.pedidos.index') }}" class="btn-outline-verde btn-sm">Ver todos</a>
                </div>
                @forelse($pedidosRecientes as $pedido)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <strong>#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</strong><br>
                            <small class="text-muted">{{ $pedido->cliente->nombre }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $pedido->estado == 'confirmado' ? 'success' : 'warning' }} rounded-pill">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </div>
                        <div>
                            <strong>${{ number_format($pedido->total_final, 2) }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3">
                        <p class="text-muted">No hay pedidos recientes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recuperación de inversión -->
    <div class="table-container mb-4">
        <h5 class="fw-bold"><i class="fas fa-chart-line"></i> Recuperación de inversión</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <div class="display-4 fw-bold text-success">$567.00</div>
                    <p>Utilidad neta por ciclo (cada 45 días)</p>
                </div>
                <div class="progress mb-3" style="height: 30px;">
                    @php
                        $porcentajeRecuperado = min(($totalVentas / 25000) * 100, 100);
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeRecuperado }}%;">
                        {{ round($porcentajeRecuperado) }}% recuperado
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row text-center">
                    <div class="col-4">
                        <h4>45</h4>
                        <p class="text-muted">Ciclos necesarios</p>
                    </div>
                    <div class="col-4">
                        <h4>4.5</h4>
                        <p class="text-muted">Años</p>
                    </div>
                    <div class="col-4">
                        <h4>45</h4>
                        <p class="text-muted">Días/ciclo</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Dato clave:</strong> Con una utilidad neta de <strong>$567.00 por ciclo</strong>, se necesitan aproximadamente <strong>45 ciclos</strong> para recuperar la inversión inicial de <strong>$25,000.00</strong>. A partir de la cosecha 46, todo lo que se vende es ganancia neta.
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h5 class="fw-bold"><i class="fas fa-chart-pie"></i> Resumen Ejecutivo</h5>
                    <a href="{{ route('vendedor.resumen') }}" class="btn-naranja btn-sm">Ver reporte detallado</a>
                </div>
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-success">${{ number_format($totalVentas, 2) }}</h4>
                        <p class="text-muted">Ingresos Totales</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $totalPedidos }}</h4>
                        <p class="text-muted">Pedidos Completados</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $productosPublicados }}</h4>
                        <p class="text-muted">Productos Activos</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $productosAgotados }}</h4>
                        <p class="text-muted">Necesitan Stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($ventasPorMes->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // GRÁFICA 1: PROYECCIÓN DE VENTAS POR CULTIVO
            const cultivos = ['Lechuga', 'Espinaca', 'Rábano', 'Cilantro'];
            const ventasPorCultivo = [336, 176, 160, 90];
            const colores = ['#4CAF50', '#2196F3', '#E91E63', '#FF9800'];

            const ctxCultivo = document.getElementById('ventasPorCultivoChart').getContext('2d');
            new Chart(ctxCultivo, {
                type: 'bar',
                data: {
                    labels: cultivos,
                    datasets: [{
                        label: 'Ingreso por ciclo ($)',
                        data: ventasPorCultivo,
                        backgroundColor: colores,
                        borderColor: colores,
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } }, title: { display: true, text: 'Ingresos ($)' } },
                        x: { title: { display: true, text: 'Cultivo' } }
                    }
                }
            });

            // Leyenda
            const legendContainer = document.getElementById('legend');
            cultivos.forEach((cultivo, index) => {
                const legendItem = document.createElement('div');
                legendItem.className = 'd-flex align-items-center gap-2';
                legendItem.innerHTML = `<div style="width: 20px; height: 20px; background: ${colores[index]}; border-radius: 4px;"></div>
                                        <span><strong>${cultivo}</strong>: $${ventasPorCultivo[index].toFixed(2)} por ciclo</span>`;
                legendContainer.appendChild(legendItem);
            });

            // GRÁFICA 2: VENTAS POR MES
            const mesesJS = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const ventasData = @json($ventasPorMes);
            const labels = ventasData.map(item => mesesJS[item.mes - 1] + ' ' + item.año);
            const datos = ventasData.map(item => item.total);
            const coloresHistoricos = ['#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800', '#F57C00', '#FF5722'];

            const ctxHistorial = document.getElementById('ventasChart').getContext('2d');
            new Chart(ctxHistorial, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: datos,
                        backgroundColor: coloresHistoricos.slice(0, datos.length),
                        borderColor: '#2E7D32',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { tooltip: { callbacks: { label: function(context) { return 'Ventas: $' + context.raw.toFixed(2); } } } },
                    scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } }, title: { display: true, text: 'Monto de ventas ($)' } }, x: { title: { display: true, text: 'Mes' } } }
                }
            });
        </script>
    @endif
@endsection
