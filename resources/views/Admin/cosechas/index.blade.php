@extends('Admin.layouts.app')
@section('title', 'Cosechas Global')
@section('header-title', 'Cosechas')
@section('header-subtitle', $usuarioSeleccionado ? 'Cosechas de '.$usuarioSeleccionado->nombre.' '.$usuarioSeleccionado->apellido : 'Vista global — todos los usuarios')

@push('styles')
    <style>
        .selector-usuario{background:#fff;border-radius:16px;padding:20px 24px;box-shadow:0 10px 30px rgba(0,0,0,0.08);margin-bottom:20px;border:1px solid rgba(46,125,50,0.06);}
        .selector-usuario h6{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#FF9800;margin-bottom:12px;}
        .user-cards{display:flex;gap:10px;flex-wrap:wrap;}
        .user-card-btn{display:flex;align-items:center;gap:9px;padding:8px 14px;border-radius:50px;border:2px solid #e8e8e8;background:#fff;cursor:pointer;text-decoration:none;transition:all .2s;font-size:.8rem;font-weight:600;color:#555;}
        .user-card-btn:hover{border-color:#2E7D32;color:#2E7D32;transform:translateY(-2px);box-shadow:0 6px 18px rgba(46,125,50,.12);}
        .user-card-btn.active{border-color:#2E7D32;background:#E8F5E9;color:#2E7D32;}
        .user-card-btn img{width:26px;height:26px;border-radius:50%;object-fit:cover;}
        .user-card-btn .count{background:#2E7D32;color:#fff;font-size:.65rem;padding:1px 6px;border-radius:50px;font-weight:700;}
        .all-btn{border-color:#FF9800!important;}.all-btn.active{background:#FFF3E0!important;color:#E65100!important;}
        .all-btn .count{background:#FF9800!important;}
        .filtros-bar{background:#fff;border-radius:14px;padding:14px 18px;box-shadow:0 6px 20px rgba(0,0,0,.06);margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .filtros-bar .form-control,.filtros-bar .form-select{border:1.5px solid #e0e0e0;border-radius:50px;font-size:.79rem;padding:7px 15px;font-family:'Poppins',sans-serif;transition:all .2s;}
        .filtros-bar .form-control:focus,.filtros-bar .form-select:focus{border-color:#2E7D32;box-shadow:0 0 0 3px rgba(46,125,50,.1);outline:none;}
        .kg-val{font-weight:800;font-size:.92rem;color:#2E7D32;}
    </style>
@endpush

@section('content')

    {{-- Stats --}}
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['total_cosechas'] }}</h3><p>Total cosechas</p></div>
            <div class="stat-icon icon-verde"><i class="fas fa-carrot"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ number_format($stats['kg_totales'],1) }} kg</h3><p>Kg totales</p></div>
            <div class="stat-icon icon-naranja"><i class="fas fa-weight"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['este_mes'] }}</h3><p>Este mes</p></div>
            <div class="stat-icon icon-teal"><i class="fas fa-calendar-alt"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ number_format($stats['kg_mes'],1) }} kg</h3><p>Kg este mes</p></div>
            <div class="stat-icon icon-azul"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    {{-- Selector usuario --}}
    <div class="selector-usuario" data-aos="fade-up" data-aos-delay="40">
        <h6><i class="fas fa-user me-1"></i> Filtrar por usuario</h6>
        <div class="user-cards">
            <a href="{{ route('admin.cosechas.index') }}"
               class="user-card-btn all-btn {{ !request('usuario_id') ? 'active':'' }}">
                <i class="fas fa-globe" style="font-size:1rem;"></i> Todos
                <span class="count">{{ $stats['total_cosechas'] }}</span>
            </a>
            @foreach($usuarios as $u)
                @php $cnt = \App\Models\Cosecha::where('user_id', $u->id)->count(); @endphp
                <a href="{{ route('admin.cosechas.index', ['usuario_id'=>$u->id]) }}"
                   class="user-card-btn {{ request('usuario_id') == $u->id ? 'active':'' }}">
                    <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->nombre.' '.$u->apellido).'&background=2E7D32&color=fff&size=26' }}" alt="">
                    {{ $u->nombre }} {{ $u->apellido }}
                    <span class="count">{{ $cnt }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.cosechas.index') }}" class="filtros-bar" data-aos="fade-up" data-aos-delay="60">
        @if(request('usuario_id'))<input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">@endif
        <select name="calidad" class="form-select" style="max-width:150px;">
            <option value="">Toda calidad</option>
            @foreach(['Excelente','Buena','Regular','Mala'] as $c)
                <option value="{{ $c }}" {{ request('calidad')===$c?'selected':'' }}>{{ $c }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" class="form-control" style="max-width:155px;" value="{{ request('desde') }}" placeholder="Desde">
        <input type="date" name="hasta" class="form-control" style="max-width:155px;" value="{{ request('hasta') }}" placeholder="Hasta">
        <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Aplicar</button>
        @if(request()->hasAny(['calidad','desde','hasta']))
            <a href="{{ route('admin.cosechas.index', request('usuario_id')?['usuario_id'=>request('usuario_id')]:[]) }}"
               class="btn-admin-rojo" style="padding:8px 14px;"><i class="fas fa-times"></i></a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="80">
        <div class="panel-header">
            <h2><i class="fas fa-carrot"></i>
                {{ $usuarioSeleccionado ? 'Cosechas de '.$usuarioSeleccionado->nombre.' '.$usuarioSeleccionado->apellido : 'Todas las cosechas' }}
            </h2>
            <span style="font-size:.8rem;color:#999;">{{ $cosechas->total() }} registro(s)</span>
        </div>

        @if($cosechas->isEmpty())
            <div class="empty-state"><i class="fas fa-carrot"></i><p>Sin cosechas registradas.</p></div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr>
                        @if(!$usuarioSeleccionado)<th>Usuario</th>@endif
                        <th>Cultivo</th>
                        <th>Módulo / Charola</th>
                        <th>Fecha cosecha</th>
                        <th>Cantidad</th>
                        <th>Calidad</th>
                        <th>Observaciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cosechas as $c)
                        <tr>
                            @if(!$usuarioSeleccionado)
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $c->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(($c->user->nombre??'U').' '.($c->user->apellido??'')).'&background=2E7D32&color=fff&size=28' }}"
                                             style="width:28px;height:28px;border-radius:50%;" alt="">
                                        <div style="font-size:.8rem;font-weight:600;">{{ $c->user->nombre ?? '—' }} {{ $c->user->apellido ?? '' }}</div>
                                    </div>
                                </td>
                            @endif
                            <td style="font-weight:600;font-size:.83rem;">{{ $c->siembra->cultivo->nombre ?? '—' }}</td>
                            <td style="font-size:.79rem;color:#666;">
                                {{ $c->siembra->modulo->nombre ?? '—' }}
                                @if($c->siembra->charola ?? false)
                                    <span style="background:#f0f0f0;padding:2px 7px;border-radius:50px;font-size:.7rem;font-weight:700;margin-left:4px;">Ch.{{ $c->siembra->charola }}</span>
                                @endif
                            </td>
                            <td style="font-size:.78rem;color:#555;">{{ \Carbon\Carbon::parse($c->fecha_cosecha)->format('d/m/Y') }}</td>
                            <td class="kg-val">{{ number_format($c->cantidad_kg, 2) }} kg</td>
                            <td>
                                @php $qc=['Excelente'=>'badge-resuelta','Buena'=>'badge-vendedor','Regular'=>'badge-media','Mala'=>'badge-critica']; @endphp
                                <span class="badge-pill {{ $qc[$c->calidad] ?? 'badge-baja' }}">{{ $c->calidad }}</span>
                            </td>
                            <td style="font-size:.75rem;color:#999;max-width:160px;">
                                {{ $c->observaciones ? \Illuminate\Support\Str::limit($c->observaciones,55) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($cosechas->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $cosechas->links() }}</div>
            @endif
        @endif
    </div>
@endsection
