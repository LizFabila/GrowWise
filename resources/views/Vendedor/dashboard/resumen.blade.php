@extends('vendedor.layouts.app')

@section('header-title', '📊 Resumen Ejecutivo')
@section('header-subtitle', 'Análisis de costo-beneficio y rentabilidad')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="table-container">
                <div class="table-header">
                    <h5 class="fw-bold"><i class="fas fa-chart-line"></i> 1. Resumen ejecutivo</h5>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>$762.00</h3>
                                <p>TOTAL VENDIDO</p>
                                <small>Ingresos brutos por ciclo</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>$195.00</h3>
                                <p>INVERSIÓN TOTAL</p>
                                <small>Costo estimado de producción por ciclo</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>$567.00</h3>
                                <p>BENEFICIO NETO</p>
                                <small>Ganancia por ciclo</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-leaf"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>75%</h3>
                                <p>RENTABILIDAD</p>
                                <small>(Beneficio neto / Total vendido) × 100 </small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-percent"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>2.3%</h3>
                                <p>ROI</p>
                                <small>Retorno de inversión global</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>Lechuga</h3>
                                <p>CULTIVO / PRODUCTO DESTACADO</p>
                                <small>Más rentable: Lechuga ($336/ciclo)</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>45</h3>
                                <p>CICLOS PARA RECUPERAR</p>
                                <small>Inversión: $25,000</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>4.5 años</h3>
                                <p>TIEMPO DE RECUPERACIÓN</p>
                                <small>Aproximado</small>
                            </div>
                            <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-4 text-center">
                        <h3 class="text-success">{{ $resumen->productos_publicados ?? 4 }}</h3>
                        <p class="text-muted">Productos publicados</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h3 class="text-success">{{ number_format($resumen->stock_restante ?? 20, 2) }} kg</h3>
                        <p class="text-muted">Stock restante</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h3 class="text-success">{{ $resumen->productos_vendidos ?? 8 }}</h3>
                        <p class="text-muted">Productos vendidos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de producción por cultivo -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="table-container">
                <h5 class="fw-bold mb-3"><i class="fas fa-table"></i> Producción por cultivo</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Cultivo</th>
                            <th>Cantidad por cosecha</th>
                            <th>Precio unitario</th>
                            <th>Ingreso por ciclo</th>
                            <th>Costo semilla</th>
                            <th>Días a cosecha</th>
                            <th>Rentabilidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>Lechuga</strong></td>
                            <td>12 manojos</td><td>$28.00</td><td class="text-success">$336.00</td><td>$11.00</td><td>55 días</td><td><span class="badge bg-success">Alta</span></td>
                        </tr>
                        <tr><td><strong>Espinaca</strong></td><td>8 manojos</td><td>$22.00</td><td class="text-success">$176.00</td><td>$11.00</td><td>35 días</td><td><span class="badge bg-success">Alta</span></td></tr>
                        <tr><td><strong>Rábano</strong></td><td>8 manojos</td><td>$20.00</td><td class="text-success">$160.00</td><td>$11.00</td><td>25 días</td><td><span class="badge bg-success">Alta</span></td></tr>
                        <tr><td><strong>Cilantro</strong></td><td>5 manojos</td><td>$18.00</td><td class="text-success">$90.00</td><td>$11.00</td><td>15 días</td><td><span class="badge bg-warning">Media</span></td></tr>
                        </tbody>
                        <tfoot class="fw-bold">
                        <tr><td colspan="3" class="text-end">Total por ciclo:</td><td class="text-success">$762.00</td><td>$44.00</td><td></td><td></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle"></i> Análisis del resumen</h5>
                <p>Este resumen concentra el comportamiento económico del sistema hidropónico gestionado mediante GrowWise. A partir de las ventas registradas, los costos de producción y el beneficio neto, es posible evaluar de manera rápida la rentabilidad del cultivo.</p>
                <p class="mt-2"><strong>Dato clave:</strong> Con una utilidad neta de <strong>$567.00 por ciclo</strong> (cada 45 días), se necesitan aproximadamente <strong>45 ciclos</strong> para recuperar la inversión inicial de <strong>$25,000.00</strong>. Esto equivale a <strong>4.5 años</strong>. A partir de la cosecha 46, todo lo que se vende es ganancia neta.</p>
                <p class="mt-2">Gracias al monitoreo constante de GrowWise, las plantas crecen sin estrés hídrico, lo que significa que prácticamente la totalidad de las cosechas llegan en buenas condiciones para la venta. No hay pérdidas por mal riego o plagas no detectadas.</p>
            </div>
        </div>
    </div>
@endsection
