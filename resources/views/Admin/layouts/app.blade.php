<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrowWise Admin – @yield('title', 'Panel Administrador')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* =============================================
           VARIABLES DE COLOR — IDÉNTICAS AL SISTEMA
        ============================================= */
        :root {
            --verde-hoja:   #2E7D32;
            --verde-menta:  #81C784;
            --verde-oscuro: #1B5E20;
            --naranja:      #E67E22;
            --naranja-vivo: #FF9800;
            --naranja-dark: #F57C00;
            --azul-info:    #64B5F6;
            --rojo-alerta:  #E53935;
            --fondo:        #F5F7FA;
            --sidebar-bg:   linear-gradient(160deg, #1a2a2f 0%, #0f1a1e 100%);
            --sombra-suave: 0 10px 30px rgba(0,0,0,0.08);
            --sombra-media: 0 15px 40px rgba(0,0,0,0.14);
            --transicion:   all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #dce3ed 100%);
            color: #333;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--verde-hoja); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--verde-oscuro); }

        /* =============================================
           LAYOUT
        ============================================= */
        .admin-layout { display: flex; min-height: 100vh; }

        /* =============================================
           SIDEBAR ADMIN
        ============================================= */
        .sidebar {
            width: 270px;
            background: var(--sidebar-bg);
            box-shadow: 3px 0 20px rgba(0,0,0,0.18);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 200;
            transition: var(--transicion);
            animation: slideInLeft 0.45s ease forwards;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-25px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .sidebar-header {
            padding: 28px 20px 22px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.03);
        }

        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2px;
        }

        .sidebar-logo i { color: var(--naranja-vivo); margin-right: 7px; }

        .sidebar-role-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--naranja-vivo), var(--naranja-dark));
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 50px;
            margin-top: 6px;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 18px;
            margin: 14px 14px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-user img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid var(--verde-menta);
            object-fit: cover;
        }

        .sidebar-user-info .name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-user-info .role {
            font-size: 0.7rem;
            color: var(--naranja-vivo);
            font-weight: 500;
        }

        /* Menú */
        .sidebar-nav { padding: 14px 0 20px; }

        .nav-section-label {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.28);
            padding: 14px 22px 6px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            margin: 3px 12px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.83rem;
            font-weight: 500;
            transition: var(--transicion);
            position: relative;
        }

        .sidebar-nav a i {
            width: 22px;
            font-size: 0.95rem;
            text-align: center;
            color: rgba(255,255,255,0.45);
            transition: var(--transicion);
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.09);
            color: var(--naranja-vivo);
            transform: translateX(4px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }

        .sidebar-nav a:hover i,
        .sidebar-nav a.active i { color: var(--naranja-vivo); }

        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 20%;
            height: 60%;
            width: 3px;
            background: var(--naranja-vivo);
            border-radius: 0 4px 4px 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--rojo-alerta);
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 50px;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.07);
            margin: 8px 18px;
        }

        /* =============================================
           CONTENIDO PRINCIPAL
        ============================================= */
        .main-content {
            flex: 1;
            margin-left: 270px;
            padding: 28px;
            min-height: 100vh;
        }

        /* =============================================
           TOPBAR
        ============================================= */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            padding: 18px 28px;
            border-radius: 18px;
            box-shadow: var(--sombra-suave);
            margin-bottom: 28px;
            border: 1px solid rgba(46,125,50,0.06);
        }

        .topbar-left h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin: 0;
        }

        .topbar-left p {
            font-size: 0.78rem;
            color: #888;
            margin: 2px 0 0;
        }

        .topbar-right { display: flex; align-items: center; gap: 14px; }

        .topbar-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f5f5f5;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transicion);
            text-decoration: none;
        }

        .topbar-icon-btn:hover {
            background: var(--verde-hoja);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46,125,50,0.25);
        }

        .topbar-icon-btn .badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: var(--rojo-alerta);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .topbar-avatar {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 6px 14px 6px 6px;
            border-radius: 50px;
            background: #f5f5f5;
            cursor: pointer;
            transition: var(--transicion);
        }

        .topbar-avatar:hover { background: #e8e8e8; }

        .topbar-avatar img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid var(--verde-menta);
        }

        .topbar-avatar span {
            font-size: 0.82rem;
            font-weight: 600;
            color: #444;
        }

        /* =============================================
           CARDS ESTADÍSTICA
        ============================================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 20px;
            margin-bottom: 26px;
        }

        .stat-card {
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--sombra-suave);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(46,125,50,0.06);
            transition: var(--transicion);
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-media);
        }

        .stat-info h3 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--verde-hoja);
            margin-bottom: 3px;
            line-height: 1;
        }

        .stat-info p {
            font-size: 0.78rem;
            color: #777;
            font-weight: 500;
            margin: 0;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            flex-shrink: 0;
        }

        .icon-verde   { background: linear-gradient(135deg, #81C784, #2E7D32); }
        .icon-naranja { background: linear-gradient(135deg, #FFB74D, #E67E22); }
        .icon-rojo    { background: linear-gradient(135deg, #EF5350, #C62828); }
        .icon-azul    { background: linear-gradient(135deg, #64B5F6, #1565C0); }
        .icon-morado  { background: linear-gradient(135deg, #CE93D8, #7B1FA2); }
        .icon-teal    { background: linear-gradient(135deg, #4DB6AC, #00695C); }

        /* =============================================
           CONTENEDORES DE TABLA
        ============================================= */
        .card-panel {
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--sombra-suave);
            border: 1px solid rgba(46,125,50,0.05);
            transition: var(--transicion);
            margin-bottom: 24px;
        }

        .card-panel:hover { box-shadow: var(--sombra-media); }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-header h2, .panel-header h5 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin: 0;
        }

        .panel-header h2 i, .panel-header h5 i {
            margin-right: 8px;
            color: var(--naranja-vivo);
        }

        /* =============================================
           BOTONES
        ============================================= */
        .btn-admin-primary {
            background: linear-gradient(135deg, var(--verde-hoja), var(--verde-oscuro));
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: var(--transicion);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-admin-primary:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(46,125,50,0.3);
            filter: brightness(1.08);
        }

        .btn-admin-naranja {
            background: linear-gradient(135deg, var(--naranja-vivo), var(--naranja-dark));
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: var(--transicion);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-admin-naranja:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255,152,0,0.35);
        }

        .btn-admin-rojo {
            background: linear-gradient(135deg, #EF5350, #C62828);
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: var(--transicion);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-admin-rojo:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(198,40,40,0.3);
        }

        /* Botones de acción pequeños */
        .action-btn {
            width: 33px;
            height: 33px;
            border-radius: 10px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transicion);
            text-decoration: none;
        }

        .action-btn:hover {
            transform: scale(1.15) translateY(-2px);
            filter: brightness(1.1);
            color: #fff;
        }

        .action-btn.ver     { background: var(--verde-hoja); }
        .action-btn.editar  { background: var(--naranja-vivo); }
        .action-btn.eliminar{ background: var(--rojo-alerta); }
        .action-btn.resolver{ background: #1565C0; }

        /* =============================================
           BADGES / PILLS
        ============================================= */
        .badge-pill {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-admin    { background: rgba(123,31,162,0.12); color: #7B1FA2; }
        .badge-vendedor { background: rgba(46,125,50,0.12);  color: #2E7D32; }
        .badge-cliente  { background: rgba(21,101,192,0.12); color: #1565C0; }

        .badge-critica  { background: rgba(198,40,40,0.13);  color: #C62828; }
        .badge-alta     { background: rgba(230,57,35,0.12);  color: #E53935; }
        .badge-media    { background: rgba(255,152,0,0.13);  color: #E65100; }
        .badge-baja     { background: rgba(46,125,50,0.12);  color: #2E7D32; }

        .badge-pendiente { background: rgba(255,152,0,0.13);  color: #F57C00; }
        .badge-resuelta  { background: rgba(46,125,50,0.12);  color: #2E7D32; }
        .badge-ignorada  { background: rgba(108,117,125,0.12);color: #6c757d; }
        .badge-activa    { background: rgba(46,125,50,0.12);  color: #2E7D32; }
        .badge-inactiva  { background: rgba(198,40,40,0.1);   color: #C62828; }

        /* =============================================
           TABLA
        ============================================= */
        .table-admin { font-size: 0.82rem; }
        .table-admin th {
            font-weight: 600;
            color: #555;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 12px 14px;
            background: #FAFAFA;
        }

        .table-admin td { padding: 12px 14px; vertical-align: middle; }
        .table-admin tbody tr { transition: background 0.2s; }
        .table-admin tbody tr:hover { background: rgba(46,125,50,0.03); }

        /* =============================================
           ALERTAS FLASH
        ============================================= */
        .flash-success {
            background: rgba(46,125,50,0.1);
            border: 1px solid rgba(46,125,50,0.2);
            color: #2E7D32;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .flash-error {
            background: rgba(198,40,40,0.08);
            border: 1px solid rgba(198,40,40,0.2);
            color: #C62828;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        /* =============================================
           FORMULARIOS ADMIN
        ============================================= */
        .form-admin .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        .form-admin .form-control,
        .form-admin .form-select {
            border: 1.5px solid #e0e0e0;
            border-radius: 12px;
            font-size: 0.85rem;
            padding: 10px 14px;
            transition: var(--transicion);
            font-family: 'Poppins', sans-serif;
        }

        .form-admin .form-control:focus,
        .form-admin .form-select:focus {
            border-color: var(--verde-hoja);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.12);
            outline: none;
        }

        /* =============================================
           EMPTY STATE
        ============================================= */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 14px;
        }

        .empty-state p { color: #999; font-size: 0.9rem; }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 992px) {
            .sidebar { width: 100%; position: relative; height: auto; }
            .main-content { margin-left: 0; }
        }

        @media (max-width: 768px) {
            .main-content { padding: 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .topbar { flex-direction: column; text-align: center; gap: 12px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="admin-layout">

    <!-- ============== SIDEBAR ============== -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-seedling"></i>GrowWise
            </div>
            <div class="sidebar-role-badge">Administrador</div>
        </div>

        <!-- Usuario logueado -->
        <div class="sidebar-user">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=Admin&background=2E7D32&color=fff' }}"
                 alt="Admin">
            <div class="sidebar-user-info">
                <div class="name">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                <div class="role">Administrador</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Panel Principal</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Vista General
            </a>

            <hr class="sidebar-divider">
            <div class="nav-section-label">Gestión de Usuarios</div>
            <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a href="{{ route('admin.usuarios.create') }}" class="{{ request()->routeIs('admin.usuarios.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>

            <hr class="sidebar-divider">
            <div class="nav-section-label">Sistema</div>
            <a href="{{ route('admin.sistema.alertas') }}" class="{{ request()->routeIs('admin.sistema.alertas') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Alertas del Sistema
                @php $alertasCount = \App\Models\Alerta::where('estado','Pendiente')->where('prioridad','Critica')->count(); @endphp
                @if($alertasCount > 0)
                    <span class="nav-badge">{{ $alertasCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.sistema.modulos') }}" class="{{ request()->routeIs('admin.sistema.modulos') ? 'active' : '' }}">
                <i class="fas fa-microchip"></i> Módulos y Sensores
            </a>

            <hr class="sidebar-divider">
            <div class="nav-section-label">Módulos de Cultivo</div>
            <a href="{{ route('admin.siembras.index') }}" class="{{ request()->routeIs('admin.siembras.*') ? 'active' : '' }}">
                <i class="fas fa-sprout"></i> Siembras
            </a>
            <a href="{{ route('cultivos.index') }}" class="{{ request()->routeIs('cultivos.*') ? 'active' : '' }}">
                <i class="fas fa-leaf"></i> Cultivos
            </a>
            <a href="{{ route('admin.monitoreo.index') }}" class="{{ request()->routeIs('admin.monitoreo.*') ? 'active' : '' }}">
                <i class="fas fa-thermometer-half"></i> Monitoreo IoT
            </a>
            <a href="{{ route('admin.cosechas.index') }}" class="{{ request()->routeIs('admin.cosechas.*') ? 'active' : '' }}">
                <i class="fas fa-carrot"></i> Cosechas
            </a>
            <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <i class="fas fa-file-chart-column"></i> Reportes
            </a>
            <a href="{{ route('evaluaciones.index') }}" class="{{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Evaluaciones
            </a>

            <hr class="sidebar-divider">
            <div class="nav-section-label">Módulo Ventas</div>
            <a href="{{ route('admin.ventas.index') }}" class="{{ request()->routeIs('admin.ventas.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Ventas globales
            </a>
            <a href="{{ route('admin.pedidos.index') }}" class="{{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Pedidos globales
            </a>
            <a href="{{ route('admin.productos.index') }}" class="{{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Productos
            </a>

            <hr class="sidebar-divider">
            <a href="{{ route('configuracion.index') }}">
                <i class="fas fa-cog"></i> Configuración
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout').submit();" style="color: rgba(239,83,80,0.7);">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
            <form id="admin-logout" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </nav>
    </div>
    <!-- ====== FIN SIDEBAR ====== -->

    <!-- ============== CONTENIDO ============== -->
    <div class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <h1>@yield('header-title', 'Panel Administrador')</h1>
                <p>@yield('header-subtitle', 'Control total del sistema GrowWise')</p>
            </div>
            <div class="topbar-right">
                <a href="{{ route('admin.sistema.alertas') }}" class="topbar-icon-btn" title="Alertas">
                    <i class="fas fa-bell"></i>
                    @if(isset($alertasCount) && $alertasCount > 0)
                        <span class="badge-dot"></span>
                    @endif
                </a>
                <a href="{{ route('configuracion.index') }}" class="topbar-icon-btn" title="Configuración">
                    <i class="fas fa-cog"></i>
                </a>
                <div class="dropdown">
                    <div class="topbar-avatar dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=Admin&background=2E7D32&color=fff' }}"
                             alt="Admin">
                        <span>{{ auth()->user()->nombre }}</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('configuracion.index') }}">
                                <i class="fas fa-user me-2 text-success"></i>Mi Perfil
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.usuarios.index') }}">
                                <i class="fas fa-users me-2 text-success"></i>Gestionar Usuarios
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"
                               onclick="event.preventDefault(); document.getElementById('admin-logout').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="flash-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="flash-success" style="background:rgba(21,101,192,0.08);border-color:rgba(21,101,192,0.2);color:#1565C0;">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 750, once: true, offset: 80 });
</script>
@stack('scripts')
</body>
</html>
