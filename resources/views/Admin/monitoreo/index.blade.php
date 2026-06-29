@extends('Admin.layouts.app')
@section('title', 'Monitoreo IoT — Global')
@section('header-title', 'Monitoreo IoT Global')
@section('header-subtitle', 'Estado en tiempo real de todos los módulos y sensores del sistema')

@push('styles')
    <style>
        .modulo-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:20px; margin-bottom:24px; }
        .modulo-card-mon { background:#fff; border-radius:18px; padding:22px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid rgba(46,125,50,0.07); transition:all 0.3s; }
        .modulo-card-mon:hover { transform:translateY(-4px); box-shadow:0 15px 40px rgba(0,0,0,0.14); }
        .modulo-card-header { display:flex; align-items:center; gap:12px; margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid #f5f5f5; }
        .modulo-avatar { width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#81C784,#2E7D32); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1rem; flex-shrink:0; }
        .modulo-avatar.inactivo { background:linear-gradient(135deg,#CFD8DC,#90A4AE); }
        .modulo-owner { font-size:0.7rem; color:#aaa; margin-top:2px; display:flex; align-items:center; gap:5px; }
        .modulo-owner img { width:16px; height:16px; border-radius:50%; }
        .sensor-row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #fafafa; }
        .sensor-row:last-child { border-bottom:none; }
        .sensor-label { display:flex; align-items:center; gap:8px; font-size:0.78rem; color:#555; }
        .sensor-icon-xs { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.72rem; }
        .sensor-value { font-weight:800; font-size:0.9rem; }
        .sensor-value.normal { color:#2E7D32; }
        .sensor-value.alerta { color:#E53935; }
        .sensor-value.sin-dato { color:#ccc; }
        .live-dot { width:8px; height:8px; border-radius:50%; background:#4CAF50; display:inline-block; margin-right:5px; animation:pulse-dot 1.5s infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }
        .no-modulos { text-align:center; padding:60px 20px; }
        .no-modulos i { font-size:3.5rem; color:#e0e0e0; margin-bottom:16px; }
    </style>
@endpush

@section('content')

    {{-- Stats globales --}}
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $statsGlobal['total_modulos'] }}</h3><p>Total módulos</p></div>
            <div class="stat-icon icon-verde"><i class="fas fa-microchip"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $statsGlobal['modulos_activos'] }}</h3><p>Módulos activos</p></div>
            <div class="stat-icon icon-teal"><i class="fas fa-wifi"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $statsGlobal['total_sensores'] }}</h3><p>Sensores totales</p></div>
            <div class="stat-icon icon-naranja"><i class="fas fa-rss"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $statsGlobal['lecturas_hoy'] }}</h3><p>Lecturas hoy</p></div>
            <div class="stat-icon icon-azul"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    {{-- Módulos por usuario --}}
    <div class="card-panel mb-4" data-aos="fade-up" data-aos-delay="60">
        <div class="panel-header">
            <h2><i class="fas fa-broadcast-tower"></i> Estado de Módulos IoT</h2>
            <span style="font-size:0.8rem;color:#999;">
            <span class="live-dot"></span>Actualización en tiempo real
        </span>
        </div>

        @if($modulos->isEmpty())
            <div class="no-modulos">
                <i class="fas fa-server"></i>
                <p>No hay módulos registrados en el sistema.</p>
            </div>
        @else
            <div class="modulo-grid">
                @foreach($modulos as $modulo)
                    @php
                        $sensorIconMap = [
                            'Temperatura'=>['fa-thermometer-half','#EF5350','#FFEBEE'],
                            'Humedad'=>['fa-tint','#42A5F5','#E3F2FD'],
                            'pH'=>['fa-flask','#AB47BC','#F3E5F5'],
                            'Luz'=>['fa-sun','#FFA726','#FFF3E0'],
                            'Nutrientes'=>['fa-leaf','#66BB6A','#E8F5E9'],
                        ];
                    @endphp
                    <div class="modulo-card-mon">
                        <div class="modulo-card-header">
                            <div class="modulo-avatar {{ !$modulo->activo ? 'inactivo':'' }}">
                                {{ $loop->iteration }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:0.9rem;color:#333;">{{ $modulo->nombre }}</div>
                                <div class="modulo-owner">
                                    <img src="{{ $modulo->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($modulo->user->nombre ?? 'U').'&background=2E7D32&color=fff&size=16' }}" alt="">
                                    {{ $modulo->user->nombre ?? '—' }} {{ $modulo->user->apellido ?? '' }}
                                </div>
                            </div>
                            <span class="badge-pill {{ $modulo->activo ? 'badge-activa':'badge-inactiva' }}">
                    {{ $modulo->activo ? 'Activo':'Inactivo' }}
                </span>
                        </div>

                        @if($modulo->sensores->isEmpty())
                            <p style="font-size:0.78rem;color:#ccc;text-align:center;padding:12px 0;">Sin sensores registrados</p>
                        @else
                            @foreach($modulo->sensores->take(6) as $sensor)
                                @php
                                    [$ic,$clr,$bg] = $sensorIconMap[$sensor->tipo] ?? ['fa-rss','#78909C','#ECEFF1'];
                                    $val = $sensor->ultima_lectura;
                                    $cls = $val ? 'normal' : 'sin-dato';
                                    $display = $val ? $val.' '.($sensor->unidad ?? '') : '—';
                                @endphp
                                <div class="sensor-row">
                                    <div class="sensor-label">
                                        <div class="sensor-icon-xs" style="background:{{ $bg }};">
                                            <i class="fas {{ $ic }}" style="color:{{ $clr }};"></i>
                                        </div>
                                        {{ $sensor->nombre }}
                                    </div>
                                    <div class="sensor-value {{ $cls }}">{{ $display }}</div>
                                </div>
                            @endforeach
                            @if($modulo->sensores->count() > 6)
                                <div style="font-size:0.72rem;color:#bbb;text-align:center;margin-top:8px;">
                                    +{{ $modulo->sensores->count() - 6 }} sensores más
                                </div>
                            @endif
                        @endif

                        @if($modulo->ubicacion)
                            <div style="font-size:0.7rem;color:#bbb;margin-top:10px;padding-top:10px;border-top:1px solid #f5f5f5;">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $modulo->ubicacion }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Tabla lecturas recientes globales --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="120">
        <div class="panel-header">
            <h2><i class="fas fa-history"></i> Últimas lecturas globales</h2>
        </div>
        @if($lecturasRecientes->isEmpty())
            <div class="empty-state"><i class="fas fa-chart-line"></i><p>Sin lecturas recientes.</p></div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr><th>Módulo</th><th>Usuario</th><th>Sensor</th><th>Tipo</th><th>Valor</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>
                    @foreach($lecturasRecientes as $l)
                        <tr>
                            <td style="font-size:0.8rem;font-weight:600;">{{ $l->modulo_nombre ?? '—' }}</td>
                            <td style="font-size:0.75rem;color:#888;">{{ $l->usuario ?? '—' }}</td>
                            <td style="font-size:0.78rem;color:#555;">{{ $l->sensor_nombre }}</td>
                            <td><span class="badge-pill badge-baja" style="font-size:0.65rem;">{{ $l->tipo }}</span></td>
                            <td style="font-weight:700;color:#2E7D32;">{{ $l->valor }} {{ $l->unidad }}</td>
                            <td style="font-size:0.75rem;color:#999;">{{ \Carbon\Carbon::parse($l->created_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
