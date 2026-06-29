@extends('Admin.layouts.app')
@section('title', 'Siembras Global')
@section('header-title', 'Siembras')
@section('header-subtitle', $usuarioSeleccionado ? 'Siembras de '.$usuarioSeleccionado->nombre.' '.$usuarioSeleccionado->apellido : 'Vista global — todos los usuarios')

@push('styles')
    <style>
        .selector-usuario { background:#fff; border-radius:16px; padding:20px 24px; box-shadow:0 10px 30px rgba(0,0,0,0.08); margin-bottom:20px; border:1px solid rgba(46,125,50,0.06); }
        .selector-usuario h6 { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#FF9800; margin-bottom:12px; }
        .user-cards { display:flex; gap:10px; flex-wrap:wrap; }
        .user-card-btn { display:flex; align-items:center; gap:9px; padding:8px 14px; border-radius:50px; border:2px solid #e8e8e8; background:#fff; cursor:pointer; text-decoration:none; transition:all .2s; font-size:0.8rem; font-weight:600; color:#555; }
        .user-card-btn:hover { border-color:#2E7D32; color:#2E7D32; transform:translateY(-2px); box-shadow:0 6px 18px rgba(46,125,50,0.12); }
        .user-card-btn.active { border-color:#2E7D32; background:#E8F5E9; color:#2E7D32; box-shadow:0 6px 18px rgba(46,125,50,0.15); }
        .user-card-btn img { width:26px; height:26px; border-radius:50%; object-fit:cover; }
        .user-card-btn .count { background:#2E7D32; color:#fff; font-size:0.65rem; padding:1px 6px; border-radius:50px; font-weight:700; }
        .user-card-btn.all-btn { border-color:#FF9800; }
        .user-card-btn.all-btn.active { border-color:#FF9800; background:#FFF3E0; color:#E65100; }
        .user-card-btn.all-btn .count { background:#FF9800; }
        .filtros-bar { background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:18px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .filtros-bar .form-control,.filtros-bar .form-select { border:1.5px solid #e0e0e0; border-radius:50px; font-size:0.79rem; padding:7px 15px; font-family:'Poppins',sans-serif; transition:all .2s; }
        .filtros-bar .form-control:focus,.filtros-bar .form-select:focus { border-color:#2E7D32; box-shadow:0 0 0 3px rgba(46,125,50,0.1); outline:none; }
        .progress-mini { height:5px; background:#eee; border-radius:50px; overflow:hidden; min-width:70px; }
        .progress-mini-fill { height:100%; background:linear-gradient(90deg,#81C784,#2E7D32); border-radius:50px; }
    </style>
@endpush

@section('content')

    {{-- Stats globales --}}
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['total'] }}</h3><p>Total siembras</p><small>En todo el sistema</small></div>
            <div class="stat-icon icon-verde"><i class="fas fa-seedling"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['activas'] }}</h3><p>Activas</p></div>
            <div class="stat-icon icon-teal"><i class="fas fa-leaf"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['por_cosechar'] }}</h3><p>Listas en 15 días</p></div>
            <div class="stat-icon icon-naranja"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['con_problemas'] }}</h3><p>Con problemas</p></div>
            <div class="stat-icon icon-rojo"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>

    {{-- Selector de usuario --}}
    <div class="selector-usuario" data-aos="fade-up" data-aos-delay="40">
        <h6><i class="fas fa-user-filter me-1"></i> Filtrar por usuario</h6>
        <div class="user-cards">
            <a href="{{ route('admin.siembras.index') }}"
               class="user-card-btn all-btn {{ !request('usuario_id') ? 'active' : '' }}">
                <i class="fas fa-globe" style="font-size:1rem;"></i>
                Todos
                <span class="count" style="{{ !request('usuario_id') ? '' : 'background:#FF9800;' }}">{{ $stats['total'] }}</span>
            </a>
            @foreach($usuarios as $u)
                @php $cnt = \App\Models\Siembra::where('user_id', $u->id)->count(); @endphp
                <a href="{{ route('admin.siembras.index', ['usuario_id' => $u->id]) }}"
                   class="user-card-btn {{ request('usuario_id') == $u->id ? 'active' : '' }}">
                    <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->nombre.' '.$u->apellido).'&background=2E7D32&color=fff&size=26' }}" alt="">
                    {{ $u->nombre }} {{ $u->apellido }}
                    <span class="count">{{ $cnt }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Filtros secundarios --}}
    <form method="GET" action="{{ route('admin.siembras.index') }}" class="filtros-bar" data-aos="fade-up" data-aos-delay="60">
        @if(request('usuario_id'))
            <input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">
        @endif
        <i class="fas fa-search" style="color:#bbb;"></i>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar cultivo o módulo…"
               value="{{ request('buscar') }}" style="flex:1;min-width:180px;">
        <select name="estado" class="form-select" style="max-width:160px;">
            <option value="">Todos los estados</option>
            @foreach(['Activa','Completada','Problema','Cancelada'] as $e)
                <option value="{{ $e }}" {{ request('estado') === $e ? 'selected':'' }}>{{ $e }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Aplicar</button>
        @if(request('buscar') || request('estado'))
            <a href="{{ route('admin.siembras.index', request('usuario_id') ? ['usuario_id'=>request('usuario_id')] : []) }}"
               class="btn-admin-rojo" style="padding:8px 14px;"><i class="fas fa-times"></i></a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="80">
        <div class="panel-header">
            <h2>
                <i class="fas fa-seedling"></i>
                @if($usuarioSeleccionado)
                    Siembras de {{ $usuarioSeleccionado->nombre }} {{ $usuarioSeleccionado->apellido }}
                @else
                    Todas las siembras
                @endif
            </h2>
            <span style="font-size:0.8rem;color:#999;">{{ $siembras->total() }} registro(s)</span>
        </div>

        @if($siembras->isEmpty())
            <div class="empty-state">
                <i class="fas fa-seedling"></i>
                <p>No hay siembras{{ $usuarioSeleccionado ? ' para este usuario' : '' }}{{ request('estado') ? ' con estado '.request('estado') : '' }}.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr>
                        @if(!$usuarioSeleccionado)<th>Usuario</th>@endif
                        <th>Cultivo</th>
                        <th>Módulo</th>
                        <th>Charola</th>
                        <th>Semillas</th>
                        <th>Fecha siembra</th>
                        <th>Cosecha est.</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($siembras as $s)
                        <tr>
                            @if(!$usuarioSeleccionado)
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $s->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(($s->user->nombre ?? 'U').' '.($s->user->apellido ?? '')).'&background=2E7D32&color=fff&size=28' }}"
                                             style="width:28px;height:28px;border-radius:50%;object-fit:cover;" alt="">
                                        <div>
                                            <div style="font-size:0.8rem;font-weight:600;color:#333;">{{ $s->user->nombre ?? '—' }} {{ $s->user->apellido ?? '' }}</div>
                                            <div style="font-size:0.7rem;color:#aaa;">{{ $s->user->role ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            <td style="font-weight:600;font-size:0.83rem;">{{ $s->cultivo->nombre ?? '—' }}</td>
                            <td style="font-size:0.79rem;color:#666;">{{ $s->modulo->nombre ?? '—' }}</td>
                            <td style="text-align:center;">
                                <span style="background:#f0f7f0;padding:3px 10px;border-radius:50px;font-size:0.74rem;font-weight:700;color:#2E7D32;">{{ $s->charola }}</span>
                            </td>
                            <td style="font-size:0.79rem;color:#666;text-align:center;">{{ $s->cantidad_semillas ?? '—' }}</td>
                            <td style="font-size:0.78rem;color:#555;">{{ \Carbon\Carbon::parse($s->fecha_siembra)->format('d/m/Y') }}</td>
                            <td style="font-size:0.78rem;color:#888;">
                                @if($s->fecha_estimada_cosecha)
                                    @php $fe = \Carbon\Carbon::parse($s->fecha_estimada_cosecha); @endphp
                                    <span style="{{ $fe->isPast() && $s->estado === 'Activa' ? 'color:#E53935;font-weight:700;' : '' }}">
                                {{ $fe->format('d/m/Y') }}
                            </span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @php $bc=['Activa'=>'badge-activa','Completada'=>'badge-resuelta','Problema'=>'badge-critica','Cancelada'=>'badge-ignorada']; @endphp
                                <span class="badge-pill {{ $bc[$s->estado] ?? 'badge-baja' }}">{{ $s->estado }}</span>
                            </td>
                            <td>
                                @php
                                    $inicio = \Carbon\Carbon::parse($s->fecha_siembra);
                                    $total  = $s->cultivo->dias_cosecha ?? 30;
                                    $dias   = max(0, $inicio->diffInDays(now(), false));
                                    $pct    = $total > 0 ? min(round(($dias / $total) * 100), 100) : 0;
                                    $color  = $pct < 60 ? '#81C784' : ($pct < 90 ? '#FF9800' : '#2E7D32');
                                @endphp
                                <div style="min-width:80px;">
                                    <div class="progress-mini">
                                        <div class="progress-mini-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                    </div>
                                    <div style="font-size:0.65rem;color:#999;margin-top:2px;">{{ $pct }}% · día {{ $dias }}/{{ $total }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($siembras->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $siembras->links() }}</div>
            @endif
        @endif
    </div>
@endsection
