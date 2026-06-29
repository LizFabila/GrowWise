@extends('Admin.layouts.app')
@section('title', 'Productos Global')
@section('header-title', 'Productos en Venta')
@section('header-subtitle', $usuarioSeleccionado ? 'Productos de '.$usuarioSeleccionado->nombre.' '.$usuarioSeleccionado->apellido : 'Vista global — todos los vendedores')
@push('styles')
    <style>
        .selector-usuario{background:#fff;border-radius:16px;padding:20px 24px;box-shadow:0 10px 30px rgba(0,0,0,0.08);margin-bottom:20px;border:1px solid rgba(46,125,50,0.06);}
        .selector-usuario h6{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#FF9800;margin-bottom:12px;}
        .user-cards{display:flex;gap:10px;flex-wrap:wrap;}
        .user-card-btn{display:flex;align-items:center;gap:9px;padding:8px 14px;border-radius:50px;border:2px solid #e8e8e8;background:#fff;cursor:pointer;text-decoration:none;transition:all .2s;font-size:.8rem;font-weight:600;color:#555;}
        .user-card-btn:hover{border-color:#2E7D32;color:#2E7D32;transform:translateY(-2px);}
        .user-card-btn.active{border-color:#2E7D32;background:#E8F5E9;color:#2E7D32;}
        .user-card-btn img{width:26px;height:26px;border-radius:50%;}
        .user-card-btn .count{background:#2E7D32;color:#fff;font-size:.65rem;padding:1px 6px;border-radius:50px;font-weight:700;}
        .all-btn{border-color:#FF9800!important;}.all-btn.active{background:#FFF3E0!important;color:#E65100!important;}
        .filtros-bar{background:#fff;border-radius:14px;padding:14px 18px;box-shadow:0 6px 20px rgba(0,0,0,.06);margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .filtros-bar .form-select,.filtros-bar .form-control{border:1.5px solid #e0e0e0;border-radius:50px;font-size:.79rem;padding:7px 15px;font-family:'Poppins',sans-serif;}
    </style>
@endpush
@section('content')
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card"><div class="stat-info"><h3>{{ $stats['total'] }}</h3><p>Total productos</p></div><div class="stat-icon icon-verde"><i class="fas fa-tags"></i></div></div>
        <div class="stat-card"><div class="stat-info"><h3>{{ $stats['disponibles'] }}</h3><p>Disponibles</p></div><div class="stat-icon icon-teal"><i class="fas fa-check-circle"></i></div></div>
        <div class="stat-card"><div class="stat-info"><h3>{{ $stats['agotados'] }}</h3><p>Agotados</p></div><div class="stat-icon icon-rojo"><i class="fas fa-times-circle"></i></div></div>
        <div class="stat-card"><div class="stat-info"><h3>{{ $stats['total'] - $stats['disponibles'] - $stats['agotados'] }}</h3><p>Eliminados</p></div><div class="stat-icon icon-naranja"><i class="fas fa-trash"></i></div></div>
    </div>
    <div class="selector-usuario" data-aos="fade-up" data-aos-delay="40">
        <h6><i class="fas fa-store me-1"></i> Filtrar por vendedor</h6>
        <div class="user-cards">
            <a href="{{ route('admin.productos.index') }}" class="user-card-btn all-btn {{ !request('usuario_id')?'active':'' }}">
                <i class="fas fa-globe" style="font-size:1rem;"></i> Todos
                <span class="count" style="background:#FF9800;">{{ $stats['total'] }}</span>
            </a>
            @foreach($usuarios as $u)
                @php $cnt = \Illuminate\Support\Facades\DB::table('productos_venta')->where('user_id',$u->id)->count(); @endphp
                <a href="{{ route('admin.productos.index', ['usuario_id'=>$u->id]) }}"
                   class="user-card-btn {{ request('usuario_id')==$u->id?'active':'' }}">
                    <img src="{{ $u->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($u->nombre.' '.$u->apellido).'&background=2E7D32&color=fff&size=26' }}" alt="">
                    {{ $u->nombre }} {{ $u->apellido }}
                    <span class="count">{{ $cnt }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <form method="GET" action="{{ route('admin.productos.index') }}" class="filtros-bar" data-aos="fade-up" data-aos-delay="60">
        @if(request('usuario_id'))<input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">@endif
        <i class="fas fa-search" style="color:#bbb;"></i>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por cultivo…" value="{{ request('buscar') }}" style="flex:1;min-width:160px;">
        <select name="estado" class="form-select" style="max-width:150px;">
            <option value="">Todo estado</option>
            @foreach(['disponible','agotado','eliminado'] as $e)
                <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-admin-primary"><i class="fas fa-filter"></i> Aplicar</button>
        @if(request()->hasAny(['buscar','estado']))
            <a href="{{ route('admin.productos.index', request('usuario_id')?['usuario_id'=>request('usuario_id')]:[]) }}" class="btn-admin-rojo" style="padding:8px 14px;"><i class="fas fa-times"></i></a>
        @endif
    </form>
    <div class="card-panel" data-aos="fade-up" data-aos-delay="80">
        <div class="panel-header">
            <h2><i class="fas fa-tags"></i> {{ $usuarioSeleccionado ? 'Productos de '.$usuarioSeleccionado->nombre : 'Todos los productos' }}</h2>
            <span style="font-size:.8rem;color:#999;">{{ $productos->total() }} registro(s)</span>
        </div>
        @if($productos->isEmpty())
            <div class="empty-state"><i class="fas fa-tags"></i><p>Sin productos registrados.</p></div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead><tr>
                        @if(!$usuarioSeleccionado)<th>Vendedor</th>@endif
                        <th>Cultivo</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Precio/u</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Publicado</th>
                    </tr></thead>
                    <tbody>
                    @foreach($productos as $p)
                        <tr>
                            @if(!$usuarioSeleccionado)
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $p->vendedor_avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($p->vendedor_nombre.' '.$p->vendedor_apellido).'&background=2E7D32&color=fff&size=26' }}"
                                             style="width:26px;height:26px;border-radius:50%;" alt="">
                                        <span style="font-size:.8rem;font-weight:600;">{{ $p->vendedor_nombre }} {{ $p->vendedor_apellido }}</span>
                                    </div>
                                </td>
                            @endif
                            <td style="font-weight:600;font-size:.83rem;">{{ $p->cultivo_nombre }}</td>
                            <td><span class="badge-pill badge-baja" style="font-size:.68rem;">{{ $p->cultivo_tipo }}</span></td>
                            <td style="font-size:.82rem;">{{ number_format($p->cantidad,2) }} {{ $p->unidad }}</td>
                            <td style="font-weight:700;color:#2E7D32;">${{ number_format($p->precio_unitario,2) }}</td>
                            <td style="font-size:.82rem;">{{ $p->stock }}</td>
                            <td>
                                @php $es=['disponible'=>'badge-resuelta','agotado'=>'badge-critica','eliminado'=>'badge-ignorada']; @endphp
                                <span class="badge-pill {{ $es[$p->estado] ?? 'badge-baja' }}">{{ ucfirst($p->estado) }}</span>
                            </td>
                            <td style="font-size:.75rem;color:#aaa;">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($productos->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $productos->links() }}</div>
            @endif
        @endif
    </div>
@endsection
