@extends('Admin.layouts.app')

@section('title', 'Crear Usuario')
@section('header-title', 'Nuevo Usuario')
@section('header-subtitle', 'Registra un nuevo usuario en el sistema')

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
        .role-option input:checked + label i { color: var(--verde-hoja); }
        .role-option.admin input:checked + label { border-color: #7B1FA2; background: rgba(123,31,162,0.06); color: #7B1FA2; }
        .role-option.admin input:checked + label i { color: #7B1FA2; }
        .role-option.cliente input:checked + label { border-color: #1565C0; background: rgba(21,101,192,0.06); color: #1565C0; }
        .role-option.cliente input:checked + label i { color: #1565C0; }
        .password-toggle {
            position: relative;
        }
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
    </style>
@endpush

@section('content')
    <div data-aos="fade-up">
        <div class="form-card form-admin">
            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf

                <div class="form-section-title"><i class="fas fa-user-circle"></i> Datos personales</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span style="color:#E53935;">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}" placeholder="Ej: Lizbeth" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido <span style="color:#E53935;">*</span></label>
                        <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                               value="{{ old('apellido') }}" placeholder="Ej: Fabila Guadarrama" required>
                        @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Correo electrónico <span style="color:#E53935;">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono') }}" placeholder="+52 722 000 0000">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-section-title"><i class="fas fa-shield-alt"></i> Seguridad</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contraseña <span style="color:#E53935;">*</span></label>
                        <div class="password-toggle">
                            <input type="password" name="password" id="pw1"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Mínimo 8 caracteres" required>
                            <button type="button" class="toggle-eye" onclick="togglePw('pw1', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password') <div class="form-hint" style="color:#E53935;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña <span style="color:#E53935;">*</span></label>
                        <div class="password-toggle">
                            <input type="password" name="password_confirmation" id="pw2"
                                   class="form-control" placeholder="Repite la contraseña" required>
                            <button type="button" class="toggle-eye" onclick="togglePw('pw2', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-section-title"><i class="fas fa-id-badge"></i> Rol del usuario</div>
                <div class="role-selector">
                    <div class="role-option admin">
                        <input type="radio" name="role" id="role_admin" value="admin"
                            {{ old('role') === 'admin' ? 'checked' : '' }}>
                        <label for="role_admin">
                            <i class="fas fa-user-shield" style="color:#7B1FA2;"></i>
                            Administrador
                        </label>
                    </div>
                    <div class="role-option vendedor">
                        <input type="radio" name="role" id="role_vendedor" value="vendedor"
                            {{ old('role', 'vendedor') === 'vendedor' ? 'checked' : '' }}>
                        <label for="role_vendedor">
                            <i class="fas fa-store" style="color:#2E7D32;"></i>
                            Vendedor
                        </label>
                    </div>
                    <div class="role-option cliente">
                        <input type="radio" name="role" id="role_cliente" value="cliente"
                            {{ old('role') === 'cliente' ? 'checked' : '' }}>
                        <label for="role_cliente">
                            <i class="fas fa-user" style="color:#1565C0;"></i>
                            Cliente
                        </label>
                    </div>
                </div>
                @error('role') <p class="form-hint" style="color:#E53935;">{{ $message }}</p> @enderror

                <div class="btn-group-form">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn-admin-rojo" style="padding:10px 24px;">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-admin-primary" style="padding:10px 28px;">
                        <i class="fas fa-user-plus"></i> Crear Usuario
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
