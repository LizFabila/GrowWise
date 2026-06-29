@extends('Admin.layouts.app')
@section('title', 'Ventas Global')
@section('header-title', 'Ventas')
@section('header-subtitle', $usuarioSeleccionado ? 'Ventas de '.$usuarioSeleccionado->nombre.' '.$usuarioSeleccionado->apellido : 'Vista global — todos los vendedores')

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
        .filtros-bar{background:#fff;border-radius:14px;padding:14px 18px;box-shadow:0 6px 20px rgba(0,0,0,.06);margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .filtros-bar .form-control,.filtros-bar .form-select{border:1.5px solid #e0e0e0;border-radius:50px;font-size:.79rem;padding:7px 15px;font-family:'Poppins',sans-serif;}
        .filtros-bar .form-control:focus,.filtros-bar .form-select:focus{border-color:#2E7D32;box-shadow:0 0 0 3px rgba(46,125,50,.1);outline:none;}
        .total-val{font-weight:800;font-size:.9rem;color:#2E7D32;}
    </style>
@endpush

@section('content')

    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['total_ventas'] }}</h3><p>Total ventas</p></div>
            <div class="stat-icon icon-verde"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>${{ number_format($stats['ingresos_total'],2) }}</h3><p>Ingresos totales</p></div>
            <div class="stat-icon icon-naranja"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>{{ $stats['este_mes'] }}</h3><p>Ventas este mes</p></div>
            <div class="stat-icon icon-teal"><i class="fas fa-calendar-alt"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3>${{ number_format($stats['ingresos_mes'],2) }}</h3><p>Ingresos mes</p></div>
            <div class="stat-icon icon-azul"><i class="fas fa-chart-bar"></i></div>
        </div>
    </div>

    <div class="selector-usuario" data-aos="fade-up" data-aos-delay="40">
        <h6><i class="fas fa-store me-1"></i> Filtrar por vendedor</h6>
        <div class="user-cards">
            <a href="{{ route('admin.ventas.index') }}"
               class="user-card-btn all-btn {{ !request('usuario_id') ? 'active':'' }}">
                <i class="fas fa-globe" style="font-size:1rem;"></i> Todos
                <span class="count" style="background:#FF9800;">{{ $stats['total_ventas'] }}</span>
            </a>
            @foreach($usuarios as $u)
                @php $cnt = \Illuminate\Support\Facades\DB::table('ventas')->where('user_id_vendedor',$u->id)->count(); @endphp
                <a href="{{ route('admin.ventas.index', ['usuario_id'=>$u->id]) }}"
                   class="user-card-btn {{ request('usuario_id')==$u->id?'active':'' }}">
                    <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->nombre.' '.$u->apellido).'&background=2E7D32&color=fff&size=26' }}" alt="">
                    {{ $u->nombre }} {{ $u->apellido }}
                    <span class="count">{{ $cnt }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <form method="GET" action="{{ route('admin.ventas.index') }}" class="filtros-bar" data-aos="fade-up" data-aos-delay="60">
        @if(request('usuario_id'))<input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">@endif
        <select name="estado" class="form-select" style="max-width:150px;">
            <option value="">Todo estado</option>
            <option value="completada" {{ request('estado')==='completada'?'selected':'' }}>Completada</option>
            <option value="cancelada"  {{ request('estado')==='cancelada'?'selected':'' }}>Cancelada</option>
        </select>
        <input type="date" name="desde" class="form-control" style="max-width:155px;" value="{{ request('desde') }}">
        <input type="date" name="hasta" class="form-control" style="max-width:155px;" value="{{ request('hasta') }}">
        <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Aplicar</button>
        @if(request()->hasAny(['estado','desde','hasta']))
            <a href="{{ route('admin.ventas.index', request('usuario_id')?['usuario_id'=>request('usuario_id')]:[]) }}"
               class="btn-admin-rojo" style="padding:8px 14px;"><i class="fas fa-times"></i></a>
        @endif
    </form>

    <div class="card-panel" data-aos="fade-up" data-aos-delay="80">
        <div class="panel-header">
            <h2><i class="fas fa-shopping-cart"></i>
                {{ $usuarioSeleccionado ? 'Ventas de '.$usuarioSeleccionado->nombre : 'Todas las ventas' }}
            </h2>
            <span style="font-size:.8rem;color:#999;">{{ $ventas->total() }} registro(s)</span>
        </div>

        @if($ventas->isEmpty())
            <div class="empty-state"><i class="fas fa-shopping-cart"></i><p>Sin ventas registradas.</p></div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr>
                        <th>#</th>
                        @if(!$usuarioSeleccionado)<th>Vendedor</th>@endif
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado pedido</th>
                        <th>Estado venta</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ventas as $v)
                        <tr>
                            <td style="color:#bbb;font-size:.74rem;">{{ $v->id }}</td>
                            @if(!$usuarioSeleccionado)
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $v->vendedor_avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($v->vendedor_nombre.' '.$v->vendedor_apellido).'&background=2E7D32&color=fff&size=26' }}"
                                             style="width:26px;height:26px;border-radius:50%;" alt="">
                                        <span style="font-size:.8rem;font-weight:600;">{{ $v->vendedor_nombre }} {{ $v->vendedor_apellido }}</span>
                                    </div>
                                </td>
                            @endif
                            <td style="font-size:.8rem;color:#555;">{{ $v->cliente_nombre }} {{ $v->cliente_apellido }}</td>
                            <td style="font-size:.78rem;color:#777;">{{ \Carbon\Carbon::parse($v->fecha_venta)->format('d/m/Y') }}</td>
                            <td class="total-val">${{ number_format($v->total,2) }}</td>
                            <td>
                                @php $pe=['pendiente'=>'badge-media','confirmado'=>'badge-baja','enviado'=>'badge-vendedor','entregado'=>'badge-resuelta','cancelado'=>'badge-critica']; @endphp
                                <span class="badge-pill {{ $pe[$v->pedido_estado] ?? 'badge-baja' }}">{{ ucfirst($v->pedido_estado) }}</span>
                            </td>
                            <td>
                                <span class="badge-pill {{ $v->estado==='completada'?'badge-resuelta':'badge-critica' }}">{{ ucfirst($v->estado) }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($ventas->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $ventas->links() }}</div>
            @endif
        @endif
    </div>
@endsection
