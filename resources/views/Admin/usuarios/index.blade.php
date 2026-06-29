@extends('Admin.layouts.app')

@section('title', 'Gestión de Usuarios')
@section('header-title', 'Gestión de Usuarios')
@section('header-subtitle', 'Administra todos los usuarios del sistema GrowWise')

@push('styles')
    <style>
        .filtros-bar {
            background: #fff;
            border-radius: 16px;
            padding: 18px 22px;
            box-shadow: var(--sombra-suave);
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            border: 1px solid rgba(46,125,50,0.06);
        }
        .filtros-bar .form-control,
        .filtros-bar .form-select {
            border: 1.5px solid #e0e0e0;
            border-radius: 50px;
            font-size: 0.82rem;
            padding: 8px 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.25s;
            min-width: 160px;
        }
        .filtros-bar .form-control:focus,
        .filtros-bar .form-select:focus {
            border-color: var(--verde-hoja);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
            outline: none;
        }
        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #e8f5e9;
            object-fit: cover;
        }
        .user-name-block .name { font-weight: 600; font-size: 0.85rem; color: #333; }
        .user-name-block .email { font-size: 0.75rem; color: #999; }
        .counter-pills { display: flex; gap: 10px; flex-wrap: wrap; }
        .counter-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            border: 2px solid transparent;
        }
        .counter-pill:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.1); }
        .counter-pill.admin-pill   { background: rgba(123,31,162,0.08); color: #7B1FA2; border-color: rgba(123,31,162,0.15); }
        .counter-pill.vend-pill    { background: rgba(46,125,50,0.08);  color: #2E7D32; border-color: rgba(46,125,50,0.15); }
        .counter-pill.cliente-pill { background: rgba(21,101,192,0.08); color: #1565C0; border-color: rgba(21,101,192,0.15); }
        .counter-pill .num { font-size: 1.1rem; font-weight: 800; }
        .modal-role-select .form-select {
            border-radius: 12px;
            border: 1.5px solid #e0e0e0;
            font-family: 'Poppins', sans-serif;
        }
        .modal-role-select .form-select:focus {
            border-color: var(--verde-hoja);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }
        .table-admin td { vertical-align: middle; }
        .actions-cell { white-space: nowrap; }
    </style>
@endpush

@section('content')

    {{-- Contadores por rol --}}
    <div class="counter-pills mb-4" data-aos="fade-up">
        <a href="{{ route('admin.usuarios.index') }}" class="counter-pill" style="background:rgba(100,100,100,0.07);color:#555;border-color:rgba(0,0,0,0.1);">
            <span class="num">{{ $totales['admin'] + $totales['vendedor'] + $totales['cliente'] }}</span>
            <span>Total</span>
        </a>
        <a href="{{ route('admin.usuarios.index', ['rol' => 'admin']) }}" class="counter-pill admin-pill">
            <i class="fas fa-user-shield"></i>
            <span class="num">{{ $totales['admin'] }}</span>
            <span>Admins</span>
        </a>
        <a href="{{ route('admin.usuarios.index', ['rol' => 'vendedor']) }}" class="counter-pill vend-pill">
            <i class="fas fa-store"></i>
            <span class="num">{{ $totales['vendedor'] }}</span>
            <span>Vendedores</span>
        </a>
        <a href="{{ route('admin.usuarios.index', ['rol' => 'cliente']) }}" class="counter-pill cliente-pill">
            <i class="fas fa-user"></i>
            <span class="num">{{ $totales['cliente'] }}</span>
            <span>Clientes</span>
        </a>
    </div>

    {{-- Barra de filtros --}}
    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="filtros-bar" data-aos="fade-up" data-aos-delay="50">
        <i class="fas fa-search" style="color:#bbb;"></i>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o email…"
               value="{{ request('buscar') }}" style="flex:1; min-width:200px;">
        <select name="rol" class="form-select" style="max-width:160px;">
            <option value="">Todos los roles</option>
            <option value="admin"    {{ request('rol') === 'admin'    ? 'selected' : '' }}>Admin</option>
            <option value="vendedor" {{ request('rol') === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
            <option value="cliente"  {{ request('rol') === 'cliente'  ? 'selected' : '' }}>Cliente</option>
        </select>
        <button type="submit" class="btn-admin-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        @if(request('buscar') || request('rol'))
            <a href="{{ route('admin.usuarios.index') }}" class="btn-admin-rojo" style="padding:9px 16px;">
                <i class="fas fa-times"></i>
            </a>
        @endif
        <a href="{{ route('admin.usuarios.create') }}" class="btn-admin-naranja ms-auto">
            <i class="fas fa-user-plus"></i> Nuevo Usuario
        </a>
    </form>

    {{-- Tabla de usuarios --}}
    <div class="card-panel" data-aos="fade-up" data-aos-delay="100">
        <div class="panel-header">
            <h2><i class="fas fa-users"></i> Usuarios del Sistema</h2>
            <span style="font-size:0.8rem; color:#999;">{{ $usuarios->total() }} resultado(s)</span>
        </div>

        @if($usuarios->isEmpty())
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <p>No se encontraron usuarios con los filtros aplicados.</p>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-admin-primary mt-3">Ver todos</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td style="color:#bbb; font-size:0.75rem;">{{ $usuario->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $usuario->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($usuario->nombre.' '.$usuario->apellido).'&background=2E7D32&color=fff&size=40' }}"
                                         class="user-avatar-sm" alt="{{ $usuario->nombre }}">
                                    <div class="user-name-block">
                                        <div class="name">{{ $usuario->nombre }} {{ $usuario->apellido }}
                                            @if($usuario->id === auth()->id())
                                                <span style="font-size:0.65rem; background:#e8f5e9; color:#2E7D32; padding:2px 7px; border-radius:50px; font-weight:700;">Tú</span>
                                            @endif
                                        </div>
                                        <div class="email">{{ $usuario->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                            <span class="badge-pill badge-{{ $usuario->role }}">
                                @if($usuario->role === 'admin') <i class="fas fa-user-shield me-1"></i>
                                @elseif($usuario->role === 'vendedor') <i class="fas fa-store me-1"></i>
                                @else <i class="fas fa-user me-1"></i> @endif
                                {{ ucfirst($usuario->role) }}
                            </span>
                            </td>
                            <td style="font-size:0.8rem; color:#666;">
                                {{ $usuario->telefono ?? '<span style="color:#ddd;">—</span>' }}
                            </td>
                            <td style="font-size:0.78rem; color:#888;">
                                {{ $usuario->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-center actions-cell">
                                <div class="d-flex gap-2 justify-content-center">
                                    {{-- Cambiar rol rápido --}}
                                    @if($usuario->id !== auth()->id())
                                        <button class="action-btn editar" title="Cambiar rol"
                                                onclick="abrirModalRol({{ $usuario->id }}, '{{ $usuario->role }}', '{{ $usuario->nombre }} {{ $usuario->apellido }}')">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    @endif
                                    {{-- Editar --}}
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="action-btn ver" title="Editar usuario">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    {{-- Eliminar --}}
                                    @if($usuario->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar a {{ $usuario->nombre }}? Esta acción no se puede deshacer.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn eliminar" title="Eliminar usuario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($usuarios->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $usuarios->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modal cambio de rol --}}
    <div class="modal fade" id="modalRol" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:18px; border:none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                <div class="modal-header" style="border-bottom:1px solid #f0f0f0; padding:20px 24px 16px;">
                    <h5 class="modal-title" style="font-weight:700; color:#2E7D32; font-size:0.95rem;">
                        <i class="fas fa-exchange-alt me-2" style="color:#FF9800;"></i>Cambiar Rol
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRol" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body modal-role-select" style="padding:20px 24px;">
                        <p id="modalNombreUsuario" style="font-size:0.85rem; color:#555; margin-bottom:14px;"></p>
                        <select name="role" id="selectRol" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="vendedor">Vendedor</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:16px 24px; gap:10px;">
                        <button type="button" class="btn-admin-rojo" data-bs-dismiss="modal" style="padding:8px 18px;">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-check"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function abrirModalRol(id, rolActual, nombre) {
            document.getElementById('formRol').action = `/admin/usuarios/${id}/rol`;
            document.getElementById('selectRol').value = rolActual;
            document.getElementById('modalNombreUsuario').textContent = `Usuario: ${nombre}`;
            new bootstrap.Modal(document.getElementById('modalRol')).show();
        }
    </script>
@endpush
