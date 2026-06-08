<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrowWise - Panel Vendedor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        /* Sidebar - Color oscuro como el footer */
        .sidebar {
            width: 280px;
            background: #1a2a2f;
            background: linear-gradient(135deg, #1a2a2f 0%, #0f1a1e 100%);
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            animation: slideIn 0.5s ease forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-header h3 {
            font-weight: 800;
            font-size: 1.4rem;
            margin-bottom: 5px;
            color: #FFFFFF;
        }

        .sidebar-header h3 i { color: #FF9800; margin-right: 8px; }
        .sidebar-header p { color: rgba(255,255,255,0.6); font-size: 0.75rem; }

        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu ul { list-style: none; }
        .sidebar-menu li { margin-bottom: 5px; }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            margin: 4px 12px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.85rem;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
            transition: left 0.5s ease;
        }

        .sidebar-menu a:hover::before { left: 100%; }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.08);
            color: #FF9800;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            width: 28px;
            font-size: 1rem;
            margin-right: 12px;
            color: rgba(255,255,255,0.6);
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover i { transform: scale(1.1); color: #FF9800; }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .dashboard-header:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .header-title h1 {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .header-title p { color: #666; font-size: 0.8rem; }

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

        .user-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .user-profile span { font-weight: 500; font-size: 0.85rem; }

        .dropdown-menu {
            border: none;
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .dropdown-item:hover {
            background: rgba(46,125,50,0.05);
            color: #2E7D32;
        }

        .btn-naranja {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
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
            font-size: 0.85rem;
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
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
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(46,125,50,0.12);
            border-color: #4CAF50;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #2E7D32, #FF9800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-info p { color: #666; font-weight: 500; font-size: 0.85rem; margin-bottom: 0; }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #81C784, #2E7D32);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon { transform: scale(1.05) rotate(5deg); }

        .table-container {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            animation: fadeInUp 0.5s ease forwards;
        }

        .table-container:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.1); }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2E7D32;
            margin: 0;
        }

        .table th {
            font-weight: 600;
            color: #1B5E20;
            border-bottom: 2px solid #E2E8F0;
            padding: 12px 10px;
            font-size: 0.8rem;
        }

        .table td {
            padding: 10px;
            vertical-align: middle;
            border-bottom: 1px solid #E2E8F0;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .table tr:hover td { background: rgba(46,125,50,0.03); }

        .badge-estado {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pendiente { background: rgba(255,152,0,0.15); color: #F57C00; }
        .badge-confirmado, .badge-success { background: rgba(76,175,80,0.15); color: #2E7D32; }
        .badge-entregado { background: rgba(46,125,50,0.15); color: #1B5E20; }
        .badge-cancelado { background: rgba(108,117,125,0.15); color: #6c757d; }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 12px 20px;
            margin-bottom: 20px;
            font-size: 0.85rem;
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

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 992px) {
            .sidebar { width: 100%; position: relative; height: auto; }
            .main-content { margin-left: 0; }
        }

        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .dashboard-header { flex-direction: column; text-align: center; gap: 15px; }
        }

        @media (max-width: 576px) {
            .stats-grid { grid-template-columns: 1fr; }
            .btn-naranja, .btn-outline-verde { width: 100%; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-seedling"></i> GrowWise</h3>
            <p>Panel de Vendedor</p>
        </div>
        <div class="sidebar-menu">
            <ul>
                <!-- Módulos de gestión en nuevo orden -->
                <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Vista general</a></li>
                <li><a href="{{ route('siembras.index') }}"><i class="fas fa-sprout"></i> Siembras</a></li>
                <li><a href="{{ route('cultivos.index') }}"><i class="fas fa-seedling"></i> Cultivos</a></li>
                <li><a href="{{ route('monitoreo.index') }}"><i class="fas fa-thermometer-half"></i> Monitoreo</a></li>
                <li><a href="{{ route('alertas.index') }}"><i class="fas fa-bell"></i> Alertas</a></li>
                <li><a href="{{ route('cosechas.index') }}"><i class="fas fa-carrot"></i> Cosechas</a></li>
                <li><a href="{{ route('reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a></li>
                <li><a href="{{ route('evaluaciones.index') }}"><i class="fas fa-chart-bar"></i> Evaluaciones</a></li>
                <li><a href="{{ route('configuracion.index') }}"><i class="fas fa-cog"></i> Configuración</a></li>

                <li><hr class="my-2"></li>

                <!-- Módulos de ventas -->
                <li><a href="{{ route('vendedor.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard Ventas</a></li>
                <li><a href="{{ route('vendedor.productos.index') }}"><i class="fas fa-tags"></i> Mis Productos</a></li>
                <li><a href="{{ route('vendedor.ventas.index') }}"><i class="fas fa-shopping-cart"></i> Ventas</a></li>
                <li><a href="{{ route('vendedor.pedidos.index') }}"><i class="fas fa-truck"></i> Pedidos</a></li>
                <li><a href="{{ route('vendedor.resumen') }}"><i class="fas fa-chart-pie"></i> Resumen Ejecutivo</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-title">
                <h1>@yield('header-title', 'Panel de Control')</h1>
                <p>@yield('header-subtitle', 'Bienvenido, ' . auth()->user()->nombre)</p>
            </div>
            <div class="dropdown">
                <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar }}" alt="Profile">
                    <span>{{ auth()->user()->nombre }}</span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('configuracion.index') }}"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                </ul>
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

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true, offset: 100 });
</script>
</body>
</html>
