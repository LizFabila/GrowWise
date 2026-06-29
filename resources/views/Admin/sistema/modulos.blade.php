@extends('Admin.layouts.app')

@section('title', 'Módulos y Sensores')
@section('header-title', 'Módulos y Sensores')
@section('header-subtitle', 'Vista global de la infraestructura IoT del sistema')

@push('styles')
    <style>
        .stats-modulos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 26px;
        }
        .modulo-stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px 26px;
            box-shadow: var(--sombra-suave);
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: var(--transicion);
        }
        .modulo-stat-card:hover { transform: translateY(-4px); box-shadow: var(--sombra-media); }
        .modulo-stat-card .num { font-size: 1.9rem; font-weight: 800; line-height: 1; }
        .modulo-stat-card .lbl { font-size: 0.75rem; color: #888; font-weight: 500; margin-top: 3px; }
        .msc-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            flex-shrink: 0;
        }
        .modulo-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(46,125,50,0.07);
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
            overflow: hidden;
            margin-bottom: 16px;
        }
        .modulo-card:hover { transform: translateY(-3px); box-shadow: var(--sombra-media); }
        .modulo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            gap: 14px;
        }
        .modulo-header-left { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
        .modulo-num {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--verde-menta), var(--verde-hoja));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            flex-shrink: 0;
        }
        .modulo-num.inactivo { background: linear-gradient(135deg, #CFD8DC, #90A4AE); }
        .modulo-info .nombre { font-weight: 700; font-size: 0.92rem; color: #333; }
        .modulo-info .owner { font-size: 0.75rem; color: #888; margin-top: 2px; }
        .modulo-header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .sensor-count-badge {
            background: rgba(46,125,50,0.08);
            color: var(--verde-hoja);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .chevron-icon {
            color: #bbb;
            transition: transform 0.3s;
            font-size: 0.85rem;
        }
        .modulo-card.open .chevron-icon { transform: rotate(180deg); }
        .sensores-grid {
            display: none;
            padding: 16px 22px 20px;
            background: rgba(245,247,250,0.6);
            gap: 12px;
        }
        .modulo-card.open .sensores-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        .sensor-chip {
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(46,125,50,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .sensor-chip:hover { border-color: var(--verde-menta); transform: translateY(-1px); }
        .sensor-icon-sm {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            flex-shrink: 0;
        }
        .sensor-chip .s-nombre { font-size: 0.8rem; font-weight: 600; color: #444; }
        .sensor-chip .s-tipo { font-size: 0.7rem; color: #aaa; }
        .empty-sensores { padding: 20px; text-align: center; color: #bbb; font-size: 0.82rem; }
        .sin-modulos { padding: 60px 20px; text-align: center; }
        .sin-modulos i { font-size: 3rem; color: #e0e0e0; margin-bottom: 14px; }
        @media(max-width:768px) {
            .stats-modulos { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')

    {{-- Stats --}}
    <div class="stats-modulos" data-aos="fade-up">
        <div class="modulo-stat-card">
            <div class="msc-icon icon-verde"><i class="fas fa-microchip"></i></div>
            <div>
                <div class="num" style="color:#2E7D32;">{{ $statsModulos['total'] }}</div>
                <div class="lbl">Total módulos</div>
            </div>
        </div>
        <div class="modulo-stat-card">
            <div class="msc-icon icon-naranja"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="num" style="color:#FF9800;">{{ $statsModulos['activos'] }}</div>
                <div class="lbl">Módulos activos</div>
            </div>
        </div>
        <div class="modulo-stat-card">
            <div class="msc-icon icon-rojo"><i class="fas fa-pause-circle"></i></div>
            <div>
                <div class="num" style="color:#E53935;">{{ $statsModulos['inactivos'] }}</div>
                <div class="lbl">Módulos inactivos</div>
            </div>
        </div>
    </div>

    {{-- Listado de módulos colapsables --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="80">
        <div class="panel-header">
            <h2><i class="fas fa-microchip"></i> Infraestructura IoT</h2>
            <span style="font-size:0.8rem; color:#999;">Haz clic en un módulo para ver sus sensores</span>
        </div>

        @if($modulos->isEmpty())
            <div class="sin-modulos">
                <i class="fas fa-server"></i>
                <p>No hay módulos registrados en el sistema.</p>
            </div>
        @else
            @foreach($modulos as $modulo)
                @php
                    $sensorIcons = [
                        'temperatura' => ['fa-thermometer-half', '#EF5350', '#FFEBEE'],
                        'humedad'     => ['fa-tint',             '#42A5F5', '#E3F2FD'],
                        'ph'          => ['fa-flask',            '#AB47BC', '#F3E5F5'],
                        'luz'         => ['fa-sun',              '#FFA726', '#FFF3E0'],
                        'nutrientes'  => ['fa-leaf',             '#66BB6A', '#E8F5E9'],
                        'suelo'       => ['fa-water',            '#26A69A', '#E0F2F1'],
                    ];
                @endphp
                <div class="modulo-card {{ !$modulo->activo ? '' : '' }}" id="mc_{{ $modulo->id }}">
                    <div class="modulo-header" onclick="toggleModulo({{ $modulo->id }})">
                        <div class="modulo-header-left">
                            <div class="modulo-num {{ !$modulo->activo ? 'inactivo' : '' }}">
                                {{ $modulo->numero_modulo ?? $loop->iteration }}
                            </div>
                            <div class="modulo-info">
                                <div class="nombre">{{ $modulo->nombre ?? 'Módulo '.$loop->iteration }}</div>
                                <div class="owner">
                                    <i class="fas fa-user" style="font-size:0.65rem;"></i>
                                    {{ $modulo->user ? $modulo->user->nombre.' '.$modulo->user->apellido : 'Sin propietario' }}
                                    @if($modulo->cultivo_actual ?? false)
                                        &nbsp;·&nbsp;<i class="fas fa-seedling" style="font-size:0.65rem; color:#2E7D32;"></i> {{ $modulo->cultivo_actual }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modulo-header-right">
                    <span class="sensor-count-badge">
                        <i class="fas fa-rss me-1"></i>{{ $modulo->sensores->count() }} sensores
                    </span>
                            <span class="badge-pill badge-{{ $modulo->activo ? 'activa' : 'inactiva' }}">
                        {{ $modulo->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                            <i class="fas fa-chevron-down chevron-icon"></i>
                        </div>
                    </div>
                    <div class="sensores-grid">
                        @if($modulo->sensores->isEmpty())
                            <div class="empty-sensores" style="grid-column:1/-1;">
                                <i class="fas fa-microchip" style="margin-right:6px;"></i>Sin sensores registrados.
                            </div>
                        @else
                            @foreach($modulo->sensores as $sensor)
                                @php
                                    $tipo = strtolower($sensor->tipo ?? '');
                                    $matched = ['fa-rss', '#78909C', '#ECEFF1'];
                                    foreach($sensorIcons as $key => [$ic, $clr, $bg]) {
                                        if(str_contains($tipo, $key)) { $matched = [$ic, $clr, $bg]; break; }
                                    }
                                    [$ic, $clr, $bg] = $matched;
                                @endphp
                                <div class="sensor-chip">
                                    <div class="sensor-icon-sm" style="background:{{ $bg }};">
                                        <i class="fas {{ $ic }}" style="color:{{ $clr }};"></i>
                                    </div>
                                    <div>
                                        <div class="s-nombre">{{ $sensor->nombre ?? 'Sensor' }}</div>
                                        <div class="s-tipo">{{ ucfirst($sensor->tipo ?? 'Desconocido') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleModulo(id) {
            const card = document.getElementById('mc_' + id);
            card.classList.toggle('open');
        }
        // Abrir automáticamente el primero
        document.addEventListener('DOMContentLoaded', () => {
            const first = document.querySelector('.modulo-card');
            if (first) first.classList.add('open');
        });
    </script>
@endpush
