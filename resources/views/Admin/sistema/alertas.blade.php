@extends('Admin.layouts.app')

@section('title', 'Alertas del Sistema')
@section('header-title', 'Alertas del Sistema')
@section('header-subtitle', 'Monitoreo global de alertas de todos los usuarios')

@push('styles')
    <style>
        .resumen-alertas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .resumen-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--sombra-suave);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.04);
            transition: var(--transicion);
            cursor: default;
        }
        .resumen-card:hover { transform: translateY(-4px); box-shadow: var(--sombra-media); }
        .resumen-card .num {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }
        .resumen-card .lbl { font-size: 0.72rem; color: #888; font-weight: 500; }
        .rc-total   .num { color: #555; }
        .rc-critica .num { color: #C62828; }
        .rc-alta    .num { color: #E53935; }
        .rc-media   .num { color: #E65100; }
        .rc-resuelta .num { color: #2E7D32; }

        .filtros-alertas {
            background: #fff;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: var(--sombra-suave);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            border: 1px solid rgba(46,125,50,0.05);
        }
        .filtros-alertas .form-select,
        .filtros-alertas .form-control {
            border: 1.5px solid #e0e0e0;
            border-radius: 50px;
            font-size: 0.8rem;
            padding: 7px 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.25s;
        }
        .filtros-alertas .form-select:focus,
        .filtros-alertas .form-control:focus {
            border-color: var(--verde-hoja);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
            outline: none;
        }
        .alerta-row {
            transition: background 0.2s;
        }
        .alerta-row:hover { background: rgba(46,125,50,0.02); }
        .prioridad-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            flex-shrink: 0;
        }
        .dot-critica  { background: #C62828; box-shadow: 0 0 0 3px rgba(198,40,40,0.2); }
        .dot-alta     { background: #E53935; }
        .dot-media    { background: #FF8F00; }
        .dot-baja     { background: #81C784; }
        .tipo-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .alerta-titulo { font-weight: 600; font-size: 0.85rem; color: #333; }
        .alerta-msg { font-size: 0.76rem; color: #888; margin-top: 2px; }
        .user-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(46,125,50,0.07);
            border-radius: 50px;
            padding: 4px 10px;
            font-size: 0.73rem;
            font-weight: 600;
            color: var(--verde-hoja);
        }
        .user-chip img { width: 20px; height: 20px; border-radius: 50%; }
    </style>
@endpush

@section('content')

    {{-- Resumen numérico --}}
    <div class="resumen-alertas" data-aos="fade-up">
        <div class="resumen-card rc-total">
            <div class="num">{{ $resumen['total'] }}</div>
            <div class="lbl">Total histórico</div>
        </div>
        <div class="resumen-card rc-critica">
            <div class="num">{{ $resumen['criticas'] }}</div>
            <div class="lbl">Críticas pendientes</div>
        </div>
        <div class="resumen-card rc-alta">
            <div class="num">{{ $resumen['altas'] }}</div>
            <div class="lbl">Altas pendientes</div>
        </div>
        <div class="resumen-card rc-media">
            <div class="num">{{ $resumen['medias'] }}</div>
            <div class="lbl">Medias pendientes</div>
        </div>
        <div class="resumen-card rc-resuelta">
            <div class="num">{{ $resumen['resueltas'] }}</div>
            <div class="lbl">Resueltas</div>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.sistema.alertas') }}" class="filtros-alertas" data-aos="fade-up" data-aos-delay="50">
        <i class="fas fa-filter" style="color:#bbb;"></i>
        <select name="estado" class="form-select" style="max-width:160px;">
            <option value=""       {{ !request('estado') ? 'selected' : '' }}>Pendientes</option>
            <option value="Resuelta" {{ request('estado') === 'Resuelta' ? 'selected' : '' }}>Resueltas</option>
            <option value="Ignorada" {{ request('estado') === 'Ignorada' ? 'selected' : '' }}>Ignoradas</option>
            <option value="todas"    {{ request('estado') === 'todas'    ? 'selected' : '' }}>Todas</option>
        </select>
        <select name="prioridad" class="form-select" style="max-width:150px;">
            <option value="">Toda prioridad</option>
            <option value="Critica" {{ request('prioridad') === 'Critica' ? 'selected' : '' }}>Crítica</option>
            <option value="Alta"    {{ request('prioridad') === 'Alta'    ? 'selected' : '' }}>Alta</option>
            <option value="Media"   {{ request('prioridad') === 'Media'   ? 'selected' : '' }}>Media</option>
            <option value="Baja"    {{ request('prioridad') === 'Baja'    ? 'selected' : '' }}>Baja</option>
        </select>
        <input type="text" name="usuario" class="form-control" style="max-width:200px;"
               value="{{ request('usuario') }}" placeholder="Filtrar por usuario…">
        <button type="submit" class="btn-admin-primary">
            <i class="fas fa-search"></i> Aplicar
        </button>
        @if(request()->hasAny(['estado', 'prioridad', 'usuario']))
            <a href="{{ route('admin.sistema.alertas') }}" class="btn-admin-rojo" style="padding:8px 16px;">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- Tabla de alertas --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="100">
        <div class="panel-header">
            <h2><i class="fas fa-bell"></i> Alertas activas</h2>
            <span style="font-size:0.8rem; color:#999;">{{ $alertas->total() }} alerta(s)</span>
        </div>

        @if($alertas->isEmpty())
            <div class="empty-state">
                <i class="fas fa-check-circle" style="color:#a5d6a7;"></i>
                <p style="color:#81C784; font-weight:600;">Sin alertas pendientes</p>
                <p>El sistema está operando con normalidad.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr>
                        <th>Prioridad</th>
                        <th>Alerta</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($alertas as $alerta)
                        <tr class="alerta-row">
                            <td>
                            <span class="badge-pill badge-{{ strtolower($alerta->prioridad) }}">
                                <span class="prioridad-dot dot-{{ strtolower($alerta->prioridad) }}"></span>
                                {{ $alerta->prioridad }}
                            </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    <div class="tipo-icon" style="background: rgba(46,125,50,0.08);">
                                        @php
                                            $iconos = [
                                                'temperatura' => 'fa-thermometer-half',
                                                'humedad'     => 'fa-tint',
                                                'ph'          => 'fa-flask',
                                                'luz'         => 'fa-sun',
                                            ];
                                            $icono = 'fa-exclamation-triangle';
                                            foreach($iconos as $key => $val) {
                                                if(str_contains($alerta->tipo, $key)) { $icono = $val; break; }
                                            }
                                        @endphp
                                        <i class="fas {{ $icono }}" style="color:#FF9800;"></i>
                                    </div>
                                    <div>
                                        <div class="alerta-titulo">{{ $alerta->titulo }}</div>
                                        <div class="alerta-msg">{{ Str::limit($alerta->mensaje, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($alerta->user)
                                    <div class="user-chip">
                                        <img src="{{ $alerta->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($alerta->user->nombre).'&background=2E7D32&color=fff&size=20' }}"
                                             alt="{{ $alerta->user->nombre }}">
                                        {{ $alerta->user->nombre }}
                                    </div>
                                @else
                                    <span style="color:#ddd; font-size:0.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-pill badge-{{ strtolower($alerta->estado) }}">{{ $alerta->estado }}</span>
                            </td>
                            <td style="font-size:0.78rem; color:#888; white-space:nowrap;">
                                {{ $alerta->created_at->diffForHumans() }}
                            </td>
                            <td class="text-center">
                                @if($alerta->estado === 'Pendiente')
                                    <form action="{{ route('admin.sistema.alertas.resolver', $alerta->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="action-btn resolver" title="Marcar como resuelta">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:0.75rem; color:#bbb;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($alertas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $alertas->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
