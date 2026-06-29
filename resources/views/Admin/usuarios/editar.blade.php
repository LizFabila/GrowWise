@extends('Admin.layouts.app')

@section('title', 'Editar Usuario')
@section('header-title', 'Editar Usuario')
@section('header-subtitle', '{{ $usuario->nombre }} {{ $usuario->apellido }}')

@push('styles')
    <style>
        .form-card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 40px;
            box-shadow: var(--sombra-suave);
            border: 1px solid rgba(46,125,50,0.05);
            max-width: 680px;
            margin: 0 auto;
        }
        .user-preview {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            background: linear-gradient(135deg, rgba(46,125,50,0.05), rgba(255,152,0,0.04));
            border-radius: 14px;
            border: 1px solid rgba(46,125,50,0.08);
            margin-bottom: 28px;
        }
        .user-preview img {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 3px solid var(--verde-menta);
        }
        .user-preview .info .name { font-weight: 700; font-size: 1rem; color: #333; }
        .user-preview .info .meta { font-size: 0.78rem; color: #888; margin-top: 3px; }
        .form-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--naranja-vivo);
            margin-bottom: 16px;
            margin-top: 28px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-section-title:first-child { margin-top: 0; }
        .form-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }
        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .role-option input { display: none; }
        .role-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 18px 12px;
            border-radius: 14px;
            border: 2px solid #e8e8e8;
            cursor: pointer;
            transition: all 0.25s;
            font-size: 0.82rem;
            font-weight: 600;
            color: #777;
        }
        .role-option label i { font-size: 1.4rem; }
        .role-option label:hover {
            border-color: var(--verde-menta);
            background: rgba(46,125,50,0.03);
            transform: translateY(-2px);
        }
        .role-option input:checked + label {
            border-color: var(--verde-hoja);
            background: rgba(46,125,50,0.06);
            color: var(--verde-hoja);
            box-shadow: 0 6px 18px rgba(46,125,50,0.15);
        }
        .role-option.admin input:checked + label { border-color: #7B1FA2; background: rgba(123,31,162,0.06); color: #7B1FA2; }
        .role-option.admin input:checked + label i { color: #7B1FA2; }
        .role-option.cliente input:checked + label { border-color: #1565C0; background: rgba(21,101,192,0.06); color: #1565C0; }
        .role-option.cliente input:checked + label i { color: #1565C0; }
        .password-toggle { position: relative; }
        .password-toggle .toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #bbb;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }
        .password-toggle .toggle-eye:hover { color: var(--verde-hoja); }
        .form-hint { font-size: 0.73rem; color: #aaa; margin-top: 5px; }
        .btn-group-form { display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; }
        .pw-optional-note {
            background: rgba(255,152,0,0.07);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #E65100;
            margin-bottom: 16px;
        }
    </style>
@endpush

@section('content')
    <div data-aos="fade-up">
        <div class="form-card form-admin">

            {{-- Preview del usuario --}}
            <div class="user-preview">
                <img src="{{ $usuario->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($usuario->nombre.' '.$usuario->apellido).'&background=2E7D32&color=fff&size=54' }}"
                     alt="{{ $usuario->nombre }}">
                <div class="info">
                    <div class="name">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                    <div class="meta">
                        {{ $usuario->email }} &nbsp;·&nbsp;
                        Registrado el {{ $usuario->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-section-title"><i class="fas fa-user-circle"></i> Datos personales</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span style="color:#E53935;">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $usuario->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido <span style="color:#E53935;">*</span></label>
                        <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                               value="{{ old('apellido', $usuario->apellido) }}" required>
                        @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Correo electrónico <span style="color:#E53935;">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $usuario->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ old('telefono', $usuario->telefono) }}">
                    </div>
                </div>

                <div class="form-section-title"><i class="fas fa-lock"></i> Nueva contraseña</div>
                <div class="pw-optional-note">
                    <i class="fas fa-info-circle me-1"></i>
                    Deja en blanco si no deseas cambiar la contraseña actual.
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña</label>
                        <div class="password-toggle">
                            <input type="password" name="password" id="pw1"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 8 caracteres">
                            <button type="button" class="toggle-eye" onclick="togglePw('pw1', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password') <div class="form-hint" style="color:#E53935;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña</label>
                        <div class="password-toggle">
                            <input type="password" name="password_confirmation" id="pw2"
                                   class="form-control" placeholder="Repite la contraseña">
                            <button type="button" class="toggle-eye" onclick="togglePw('pw2', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @if($usuario->id !== auth()->id())
                    <div class="form-section-title"><i class="fas fa-id-badge"></i> Rol del usuario</div>
                    <div class="role-selector">
                        <div class="role-option admin">
                            <input type="radio" name="role" id="role_admin" value="admin"
                                {{ old('role', $usuario->role) === 'admin' ? 'checked' : '' }}>
                            <label for="role_admin">
                                <i class="fas fa-user-shield" style="color:#7B1FA2;"></i>
                                Administrador
                            </label>
                        </div>
                        <div class="role-option vendedor">
                            <input type="radio" name="role" id="role_vendedor" value="vendedor"
                                {{ old('role', $usuario->role) === 'vendedor' ? 'checked' : '' }}>
                            <label for="role_vendedor">
                                <i class="fas fa-store" style="color:#2E7D32;"></i>
                                Vendedor
                            </label>
                        </div>
                        <div class="role-option cliente">
                            <input type="radio" name="role" id="role_cliente" value="cliente"
                                {{ old('role', $usuario->role) === 'cliente' ? 'checked' : '' }}>
                            <label for="role_cliente">
                                <i class="fas fa-user" style="color:#1565C0;"></i>
                                Cliente
                            </label>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="role" value="{{ $usuario->role }}">
                @endif

                <div class="btn-group-form">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn-admin-rojo" style="padding:10px 24px;">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-admin-primary" style="padding:10px 28px;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePw(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                btn.style.color = '#2E7D32';
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                btn.style.color = '#bbb';
            }
        }
    </script>
@endpush
