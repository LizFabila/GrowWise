<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrowWise - Sistema de Gestión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #F5F7FA 0%, #E8EDF2 100%);
            color: #333;
            min-height: 100vh;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #E2E8F0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #2E7D32; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1B5E20; }

        .dashboard { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #FFFFFF;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            z-index: 10;
            animation: slideIn 0.5s ease forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(46,125,50,0.1);
            background: linear-gradient(135deg, #FFFFFF, #F8F9FA);
        }

        .sidebar-header h3 {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-header h3 i {
            color: #FF9800;
            margin-right: 10px;
            background: none;
            -webkit-text-fill-color: #FF9800;
        }

        .sidebar-header p { color: #888; font-size: 0.9rem; }

        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu ul { list-style: none; }
        .sidebar-menu li { margin-bottom: 5px; }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            margin: 4px 12px;
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(46,125,50,0.1), transparent);
            transition: left 0.5s ease;
        }

        .sidebar-menu a:hover::before { left: 100%; }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: linear-gradient(135deg, rgba(46,125,50,0.1), rgba(129,199,132,0.05));
            color: #2E7D32;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            width: 30px;
            font-size: 1.2rem;
            margin-right: 12px;
            color: #2E7D32;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover i {
            transform: scale(1.1);
            color: #FF9800;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .dashboard-header:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .header-title p { color: #666; font-size: 0.9rem; }

        .header-actions { display: flex; align-items: center; gap: 20px; }

        .notification-badge {
            position: relative;
            font-size: 1.3rem;
            color: #666;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .notification-badge:hover {
            color: #FF9800;
            transform: scale(1.1);
        }

        .notification-badge span {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #FF9800;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 20px;
        }

        /* Perfil */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 15px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: #f5f5f5;
        }

        .user-profile:hover {
            background: #e8e8e8;
            transform: translateY(-2px);
        }

        .user-profile img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; }
        .user-profile span { font-weight: 600; font-size: 0.9rem; }

        /* Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            border-radius: 10px;
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(46,125,50,0.05);
            color: #2E7D32;
        }

        /* Alertas */
        .alert {
            border-radius: 50px;
            padding: 14px 24px;
            border: none;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(46,125,50,0.1);
            color: #2E7D32;
            border-left: 4px solid #2E7D32;
        }

        .alert-danger {
            background: rgba(220,53,69,0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        /* Botones */
        .btn-naranja {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-naranja::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
            z-index: -1;
        }

        .btn-naranja:hover::before { left: 100%; }

        .btn-naranja:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255,152,0,0.3);
            color: white;
        }

        .btn-outline-verde {
            background: transparent;
            border: 2px solid #2E7D32;
            color: #2E7D32;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-verde:hover {
            background: #2E7D32;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46,125,50,0.3);
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(46,125,50,0.08);
            cursor: pointer;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #4CAF50;
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #2E7D32, #FF9800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-info p { color: #666; font-weight: 500; margin-bottom: 5px; }
        .stat-info small { color: #999; font-size: 0.75rem; }

        .stat-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #81C784, #2E7D32);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon { transform: scale(1.05) rotate(5deg); }

        /* Tablas */
        .table-container {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            animation: fadeInUp 0.5s ease forwards;
        }

        .table-container:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.12); }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h2, .table-header h5 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2E7D32;
            margin: 0;
        }

        .table th {
            font-weight: 600;
            color: #1B5E20;
            border-bottom: 2px solid #E2E8F0;
            padding: 14px 12px;
            font-size: 0.85rem;
        }

        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #E2E8F0;
            transition: all 0.3s ease;
        }

        .table tr:hover td { background: rgba(46,125,50,0.03); }

        /* Badges */
        .badge-estado, .badge-alerta {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pendiente { background: rgba(255,152,0,0.15); color: #F57C00; }
        .badge-confirmado, .badge-completada, .badge-resuelta, .badge-success { background: rgba(76,175,80,0.15); color: #2E7D32; }
        .badge-entregado { background: rgba(46,125,50,0.15); color: #1B5E20; }
        .badge-cancelado, .badge-ignorada { background: rgba(108,117,125,0.15); color: #6c757d; }
        .badge-critica, .badge-alta { background: rgba(220,53,69,0.15); color: #dc3545; }
        .badge-media { background: rgba(255,152,0,0.15); color: #F57C00; }
        .badge-baja { background: rgba(46,125,50,0.15); color: #2E7D32; }

        /* Botones acción */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
            margin: 0 3px;
            border: none;
            text-decoration: none;
        }

        .action-btn.resolver, .action-btn.ver { background: #2E7D32; }
        .action-btn.eliminar { background: #dc3545; }
        .action-btn.editar { background: #FF9800; }

        .action-btn:hover {
            transform: scale(1.1) translateY(-2px);
            filter: brightness(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .empty-state i { font-size: 3.5rem; color: #ddd; margin-bottom: 15px; }
        .empty-state h3 { font-size: 1.3rem; color: #666; margin-bottom: 10px; }

        /* Filtros */
        .filtro-select, .form-select, .form-control {
            border-radius: 50px;
            border: 1px solid #E2E8F0;
            padding: 10px 18px;
            transition: all 0.3s ease;
            background: white;
            font-size: 0.85rem;
        }

        .filtro-select:focus, .form-select:focus, .form-control:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
            outline: none;
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard { flex-direction: column; }
            .sidebar { width: 100%; position: relative; }
            .main-content { padding: 20px; }
        }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .dashboard-header { flex-direction: column; text-align: center; gap: 15px; }
            .header-actions { justify-content: center; }
            .table-container { padding: 16px; overflow-x: auto; }
        }

        @media (max-width: 576px) {
            .stats-grid { grid-template-columns: 1fr; }
            .btn-naranja, .btn-outline-verde { width: 100%; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar" data-aos="fade-right" data-aos-duration="1000">
        <div class="sidebar-header">
            <h3><i class="fas fa-seedling"></i> GrowWise</h3>
            <p>Gestión Inteligente</p>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Vista general</a></li>
                <li><a href="{{ route('cultivos.index') }}"><i class="fas fa-seedling"></i> Cultivos</a></li>
                <li><a href="{{ route('siembras.index') }}"><i class="fas fa-sprout"></i> Siembras</a></li>
                <li><a href="{{ route('monitoreo.index') }}"><i class="fas fa-thermometer-half"></i> Monitoreo</a></li>
                <li><a href="{{ route('alertas.index') }}"><i class="fas fa-bell"></i> Alertas</a></li>
                <li><a href="{{ route('reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a></li>
                <li><a href="{{ route('cosechas.index') }}"><i class="fas fa-carrot"></i> Cosechas</a></li>
                <li><a href="{{ route('evaluaciones.index') }}"><i class="fas fa-chart-bar"></i> Evaluaciones</a></li>
                <li><a href="{{ route('configuracion.index') }}"><i class="fas fa-cog"></i> Configuración</a></li>
                <li><hr class="my-2"></li>
                @auth
                    @if(auth()->user()->isVendedor() || auth()->user()->isAdmin())
                        <li><a href="{{ route('vendedor.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard Ventas</a></li>
                        <li><a href="{{ route('vendedor.productos.index') }}"><i class="fas fa-tags"></i> Mis Productos</a></li>
                        <li><a href="{{ route('vendedor.ventas.index') }}"><i class="fas fa-shopping-cart"></i> Ventas</a></li>
                        <li><a href="{{ route('vendedor.pedidos.index') }}"><i class="fas fa-truck"></i> Pedidos</a></li>
                        <li><a href="{{ route('vendedor.resumen') }}"><i class="fas fa-chart-pie"></i> Resumen Ejecutivo</a></li>
                    @else
                        <li><a href="{{ route('cliente.tienda.index') }}"><i class="fas fa-store"></i> Tienda</a></li>
                        <li><a href="{{ route('cliente.carrito.ver') }}"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                        <li><a href="{{ route('cliente.pedidos.index') }}"><i class="fas fa-truck"></i> Mis Pedidos</a></li>
                        <li><a href="{{ route('cliente.direcciones.index') }}"><i class="fas fa-map-marker-alt"></i> Direcciones</a></li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header" data-aos="fade-down" data-aos-duration="1000">
            <div class="header-title">
                @yield('header-title', '')
            </div>
            <div class="header-actions">
                @auth
                    @if(auth()->user()->isCliente())
                        <a href="{{ route('cliente.carrito.ver') }}" class="notification-badge">
                            <i class="fas fa-shopping-cart"></i>
                            @php $cartCount = count(session()->get('carrito', [])); @endphp
                            @if($cartCount > 0)<span>{{ $cartCount }}</span>@endif
                        </a>
                    @endif
                @endauth
                <a href="{{ route('alertas.index') }}" class="notification-badge"><i class="fas fa-bell"></i></a>
                <div class="dropdown">
                    <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->nombre . '&background=2E7D32&color=fff' }}" alt="Profile">
                        <span>{{ auth()->user()->nombre }}</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('configuracion.index') }}"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true, offset: 100 });
</script>
</body>
</html>
