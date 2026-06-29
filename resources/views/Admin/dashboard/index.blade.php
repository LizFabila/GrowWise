@extends('Admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('header-title', 'Panel de Administración')
@section('header-subtitle', 'Vista global del sistema — todos los usuarios y módulos')

@push('styles')
<style>
    .progress-bar-verde { background: linear-gradient(90deg, #81C784, #2E7D32); border-radius: 50px; }
    .chart-bar {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        height: 120px;
        padding: 0 4px;
    }
    .chart-bar-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .chart-bar-fill {
        width: 100%;
        border-radius: 6px 6px 0 0;
        background: linear-gradient(180deg, #81C784, #2E7D32);
        transition: all 0.5s ease;
        min-height: 6px;
        position: relative;
    }
    .chart-bar-fill:hover { filter: brightness(1.15); }
    .chart-bar-label { font-size: 0.6rem; color: #999; font-weight: 500; }
    .chart-bar-val { font-size: 0.65rem; font-weight: 700; color: #555; }

    .top-vendor-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .top-vendor-item:last-child { border-bottom: none; }
    .top-vendor-item:hover { background: rgba(46,125,50,0.03); border-radius: 10px; padding-left: 8px; }
    .vendor-rank {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        flex-shrink: 0;
    }
    .rank-1 { background: linear-gradient(135deg,#FFD700,#FFA000); color:#fff; }
    .rank-2 { background: linear-gradient(135deg,#CFD8DC,#90A4AE); color:#fff; }
    .rank-3 { background: linear-gradient(135deg,#FFCC80,#FF7043); color:#fff; }
    .rank-n { background: #f0f0f0; color: #777; }
    .vendor-amount { margin-left: auto; font-weight: 700; color: #2E7D32; font-size: 0.88rem; }

    .alert-urgente {
        background: rgba(198,40,40,0.05);
        border-left: 4px solid #C62828;
        border-radius: 0 12px 12px 0;
        padding: 12px 16px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }
    .alert-urgente:hover { background: rgba(198,40,40,0.09); transform: translateX(3px); }
    .alert-urgente.alta {
        background: rgba(230,57,35,0.05);
        border-left-color: #E53935;
    }
</style>
@endpush

@section('content')

{{-- =========================================
     FILA 1: STATS GLOBALES
========================================= --}}
<div class="stats-grid" data-aos="fade-up">
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['total_usuarios'] }}</h3>
            <p>Usuarios Totales</p>
        </div>
        <div class="stat-icon icon-verde"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['total_vendedores'] }}</h3>
            <p>Vendedores</p>
        </div>
        <div class="stat-icon icon-naranja"><i class="fas fa-store"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['total_clientes'] }}</h3>
            <p>Clientes</p>
        </div>
        <div class="stat-icon icon-azul"><i class="fas fa-user"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['alertas_criticas'] }}</h3>
            <p>Alertas Críticas</p>
        </div>
        <div class="stat-icon icon-rojo"><i class="fas fa-exclamation-triangle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['siembras_activas'] }}</h3>
            <p>Siembras Activas</p>
        </div>
        <div class="stat-icon icon-teal"><i class="fas fa-sprout"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>${{ number_format($stats['total_ventas'], 0) }}</h3>
            <p>Ventas Totales</p>
        </div>
        <div class="stat-icon icon-morado"><i class="fas fa-dollar-sign"></i></div>
    </div>
</div>

{{-- =========================================
     FILA 2: VENTAS + ALERTAS URGENTES
========================================= --}}
<div class="row g-4 mb-4" data-aos="fade-up" data-aos-delay="80">

    {{-- Gráfica de ventas por mes --}}
    <div class="col-lg-7">
        <div class="card-panel h-100">
            <div class="panel-header">
                <h5><i class="fas fa-chart-bar"></i> Ventas — Últimos 6 meses</h5>
                <a href="{{ route('vendedor.ventas.index') }}" class="btn-admin-naranja" style="font-size:0.75rem;padding:7px 16px;">
                    <i class="fas fa-arrow-right"></i> Ver todo
                </a>
            </div>
            @if($ventasPorMes->count() > 0)
                @php
                    $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                    $maxVenta = $ventasPorMes->max('total') ?: 1;
                @endphp
                <div class="chart-bar mt-3">
                    @foreach($ventasPorMes as $v)
                        @php $pct = round(($v->total / $maxVenta) * 100); @endphp
                        <div class="chart-bar-item">
                            <div class="chart-bar-val">${{ number_format($v->total,0) }}</div>
                            <div class="chart-bar-fill" style="height: {{ $pct }}%;"></div>
                            <div class="chart-bar-label">{{ $meses[$v->mes - 1] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-top d-flex gap-4">
                    <div>
                        <div class="fw-700 text-success" style="font-size:1.1rem;">${{ number_format($stats['total_ventas'],2) }}</div>
                        <div style="font-size:0.73rem;color:#999;">Total acumulado</div>
                    </div>
                    <div>
                        <div class="fw-700" style="font-size:1.1rem;color:#E67E22;">{{ $stats['total_pedidos'] }}</div>
                        <div style="font-size:0.73rem;color:#999;">Pedidos totales</div>
                    </div>
                    <div>
                        <div class="fw-700" style="font-size:1.1rem;color:#1565C0;">{{ $stats['productos_activos'] }}</div>
                        <div style="font-size:0.73rem;color:#999;">Productos activos</div>
                    </div>
                </div>
            @else
                <div class="empty-state"><i class="fas fa-chart-bar"></i><p>Sin datos de ventas aún</p></div>
            @endif
        </div>
    </div>

    {{-- Alertas urgentes --}}
    <div class="col-lg-5">
        <div class="card-panel h-100">
            <div class="panel-header">
                <h5><i class="fas fa-bell"></i> Alertas Urgentes</h5>
                <a href="{{ route('admin.sistema.alertas') }}" class="btn-admin-rojo" style="font-size:0.75rem;padding:7px 16px;">
                    <i class="fas fa-arrow-right"></i> Ver todas
                </a>
            </div>
            @forelse($alertasCriticas as $alerta)
                <div class="alert-urgente {{ $alerta->prioridad == 'Alta' ? 'alta' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:0.8rem;font-weight:700;color:#333;">{{ $alerta->titulo }}</div>
                            <div style="font-size:0.72rem;color:#777;margin-top:2px;">
                                {{ $alerta->user->nombre ?? 'Sin usuario' }} · {{ $alerta->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="badge-pill {{ $alerta->prioridad == 'Critica' ? 'badge-critica' : 'badge-alta' }}">
                            {{ $alerta->prioridad }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-state"><i class="fas fa-check-circle" style="color:#81C784;"></i><p>Sin alertas críticas pendientes</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- =========================================
     FILA 3: TOP VENDEDORES + SIEMBRAS RECIENTES
========================================= --}}
<div class="row g-4 mb-4" data-aos="fade-up" data-aos-delay="140">

    {{-- Top vendedores --}}
    <div class="col-lg-4">
        <div class="card-panel h-100">
            <div class="panel-header">
                <h5><i class="fas fa-trophy"></i> Top Vendedores</h5>
            </div>
            @forelse($topVendedores as $i => $v)
                <div class="top-vendor-item">
                    <div class="vendor-rank {{ $i == 0 ? 'rank-1' : ($i == 1 ? 'rank-2' : ($i == 2 ? 'rank-3' : 'rank-n')) }}">
                        #{{ $i + 1 }}
                    </div>
                    <img src="{{ $v->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($v->nombre) . '&background=2E7D32&color=fff&size=32' }}"
                         style="width:32px;height:32px;border-radius:50%;border:2px solid #e0e0e0;">
                    <div>
                        <div style="font-size:0.82rem;font-weight:600;color:#333;">{{ $v->nombre }}</div>
                        <div style="font-size:0.7rem;color:#999;">{{ $v->num_ventas }} ventas</div>
                    </div>
                    <div class="vendor-amount">${{ number_format($v->total_ventas,0) }}</div>
                </div>
            @empty
                <div class="empty-state"><i class="fas fa-store-slash"></i><p>Sin datos de ventas</p></div>
            @endforelse
        </div>
    </div>

    {{-- Siembras recientes --}}
    <div class="col-lg-8">
        <div class="card-panel h-100">
            <div class="panel-header">
                <h5><i class="fas fa-sprout"></i> Siembras Recientes — Global</h5>
                <a href="{{ route('siembras.index') }}" class="btn-admin-primary" style="font-size:0.75rem;padding:7px 16px;">
                    <i class="fas fa-arrow-right"></i> Ver todas
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-admin mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Cultivo</th>
                            <th>Módulo</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siembrasRecientes as $siembra)
                            <tr>
                                <td>
                                    <span style="font-weight:600;">
                                        {{ $siembra->user->nombre ?? '—' }} {{ $siembra->user->apellido ?? '' }}
                                    </span>
                                </td>
                                <td>{{ $siembra->cultivo->nombre ?? '—' }}</td>
                                <td>{{ $siembra->modulo->nombre ?? '—' }}</td>
                                <td>
                                    <span class="badge-pill {{ $siembra->estado == 'Activa' ? 'badge-activa' : 'badge-inactiva' }}">
                                        {{ $siembra->estado }}
                                    </span>
                                </td>
                                <td style="min-width:110px;">
                                    <div style="height:6px;background:#eee;border-radius:50px;overflow:hidden;">
                                        <div class="progress-bar-verde" style="height:100%;width:{{ $siembra->progreso }}%;"></div>
                                    </div>
                                    <div style="font-size:0.68rem;color:#999;margin-top:3px;">{{ $siembra->progreso }}%</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Sin siembras registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- =========================================
     FILA 4: USUARIOS RECIENTES
========================================= --}}
<div class="card-panel" data-aos="fade-up" data-aos-delay="200">
    <div class="panel-header">
        <h5><i class="fas fa-users"></i> Usuarios Recientes</h5>
        <a href="{{ route('admin.usuarios.create') }}" class="btn-admin-primary">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-admin mb-0">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuariosRecientes as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->nombre) . '&background=2E7D32&color=fff&size=32' }}"
                                     style="width:32px;height:32px;border-radius:50%;">
                                <span style="font-weight:600;">{{ $u->nombre }} {{ $u->apellido }}</span>
                            </div>
                        </td>
                        <td style="color:#777;font-size:0.8rem;">{{ $u->email }}</td>
                        <td>
                            <span class="badge-pill {{ $u->role == 'admin' ? 'badge-admin' : ($u->role == 'vendedor' ? 'badge-vendedor' : 'badge-cliente') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td style="color:#999;font-size:0.78rem;">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="action-btn editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
