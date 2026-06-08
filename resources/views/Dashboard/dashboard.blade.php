<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GrowWise</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --verde-hoja: #2E7D32;
            --verde-menta: #81C784;
            --verde-oscuro: #1B5E20;
            --tierra: #8D6E63;
            --naranja: #FF9800;
            --naranja-oscuro: #F57C00;
            --azul-cielo: #64B5F6;
            --fondo: #F8F9FA;
            --gris-oscuro: #2c3e50;
            --sombra-suave: 0 10px 30px rgba(0,0,0,0.1);
            --sombra-media: 0 15px 40px rgba(0,0,0,0.15);
            --transicion: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--fondo);
            color: #333;
            overflow-x: hidden;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 20px rgba(0,0,0,0.05);
            transition: var(--transicion);
            position: relative;
            z-index: 10;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(46,125,50,0.1);
        }

        .sidebar-header h3 {
            font-weight: 800;
            color: var(--verde-hoja);
            font-size: 1.6rem;
            margin-bottom: 5px;
        }

        .sidebar-header h3 i {
            color: var(--naranja);
            margin-right: 10px;
        }

        .sidebar-header p {
            color: #888;
            font-size: 0.85rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(46,125,50,0.05);
            color: var(--verde-hoja);
            border-left-color: var(--verde-hoja);
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            width: 30px;
            font-size: 1.2rem;
            margin-right: 12px;
            color: var(--verde-hoja);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--sombra-suave);
        }

        .dashboard-header:hover {
            box-shadow: var(--sombra-media);
        }

        .header-title h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin-bottom: 5px;
        }

        .header-title p {
            color: #666;
            font-size: 0.85rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            font-size: 1.3rem;
            color: #666;
            transition: var(--transicion);
            text-decoration: none;
        }

        .notification-badge:hover {
            color: var(--naranja);
            transform: scale(1.1);
        }

        .notification-badge span {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--naranja);
            color: white;
            font-size: 0.65rem;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--sombra-media);
            border-radius: 15px;
            padding: 8px 0;
            margin-top: 10px;
        }

        .dropdown-item {
            padding: 8px 20px;
            transition: var(--transicion);
            font-size: 0.85rem;
        }

        .dropdown-item:hover {
            background: rgba(46,125,50,0.05);
            color: var(--verde-hoja);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 15px;
            border-radius: 50px;
            background: #f5f5f5;
        }

        .user-profile:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-profile span {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Tarjetas de resumen */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(46,125,50,0.1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            min-width: 0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-media);
            border-color: var(--verde-hoja);
        }

        .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--verde-hoja);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card:hover .stat-info h3 {
            color: var(--naranja);
        }

        .stat-info p {
            color: #666;
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-info small {
            color: #999;
            font-size: 0.7rem;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            background: linear-gradient(135deg, var(--verde-menta), var(--verde-hoja));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            transition: var(--transicion);
            margin-left: 12px;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.05) rotate(5deg);
            background: linear-gradient(135deg, var(--naranja), var(--naranja-oscuro));
        }

        /* Dos columnas */
        .row-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .card-custom {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }

        .card-custom:hover {
            box-shadow: var(--sombra-media);
            transform: translateY(-5px);
        }

        .card-custom h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin-bottom: 20px;
        }

        .card-custom h5 i {
            margin-right: 10px;
            color: var(--naranja);
        }

        /* Lista de siembras */
        .siembra-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 15px;
            background: #f8f9fa;
            margin-bottom: 10px;
            transition: var(--transicion);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .siembra-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .siembra-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(46,125,50,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: var(--verde-hoja);
            font-size: 1rem;
        }

        .siembra-item:hover .siembra-icon {
            background: var(--verde-hoja);
            color: white;
        }

        .siembra-content {
            flex: 1;
        }

        .siembra-title {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .siembra-subtitle {
            font-size: 0.75rem;
            color: #888;
        }

        .siembra-progress {
            width: 80px;
            height: 5px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-left: 12px;
        }

        .siembra-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--verde-hoja), var(--naranja));
        }

        /* Monitoreo ambiental */
        .monitoring-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 5px;
        }

        .monitor-item {
            text-align: center;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px 10px;
            transition: var(--transicion);
        }

        .monitor-item:hover {
            background: var(--verde-menta);
            color: white;
            transform: translateY(-3px);
        }

        .monitor-item i {
            font-size: 1.6rem;
            color: var(--verde-hoja);
            margin-bottom: 8px;
        }

        .monitor-item:hover i {
            color: white;
        }

        .monitor-item h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .monitor-item p {
            font-size: 0.75rem;
            color: #666;
            margin: 0;
        }

        .monitor-item:hover p {
            color: white;
        }

        /* Sistema de Riego */
        .riego-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 12px 15px;
            transition: var(--transicion);
        }

        .riego-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        .riego-item span:first-child {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .riego-item span:last-child {
            color: var(--verde-hoja);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .riego-status {
            background: rgba(76,175,80,0.1);
            text-align: center;
            padding: 10px;
            border-radius: 12px;
            margin-top: 15px;
        }

        .riego-status.activo {
            background: rgba(76,175,80,0.2);
        }

        /* Alertas */
        .alertas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-naranja {
            background: linear-gradient(135deg, var(--naranja), var(--naranja-oscuro));
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: var(--transicion);
            text-decoration: none;
            display: inline-block;
        }

        .btn-naranja:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,152,0,0.3);
            color: white;
        }

        .alert-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 15px;
            background: #f8f9fa;
            margin-bottom: 10px;
            transition: var(--transicion);
            text-decoration: none;
            color: inherit;
        }

        .alert-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .alert-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1rem;
        }

        .alert-icon.critica { background: rgba(220,53,69,0.15); color: #dc3545; }
        .alert-icon.alta { background: rgba(255,152,0,0.15); color: var(--naranja); }
        .alert-icon.media { background: rgba(100,181,246,0.15); color: var(--azul-cielo); }
        .alert-icon.baja { background: rgba(46,125,50,0.15); color: var(--verde-hoja); }

        .alert-item:hover .alert-icon.critica { background: #dc3545; color: white; }
        .alert-item:hover .alert-icon.alta { background: var(--naranja); color: white; }
        .alert-item:hover .alert-icon.media { background: var(--azul-cielo); color: white; }
        .alert-item:hover .alert-icon.baja { background: var(--verde-hoja); color: white; }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .alert-time {
            font-size: 0.7rem;
            color: #888;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #ddd;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard { flex-direction: column; }
            .sidebar { width: 100%; }
            .main-content { padding: 20px; }
        }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .row-custom { grid-template-columns: 1fr; gap: 20px; }
            .monitoring-grid { grid-template-columns: repeat(2, 1fr); }
            .stat-info h3 { font-size: 1.2rem; }
            .stat-info p { font-size: 0.7rem; }
            .stat-icon { width: 40px; height: 40px; min-width: 40px; font-size: 1rem; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar" data-aos="fade-right" data-aos-duration="1000">
        <div class="sidebar-header">
            <h3><i class="fas fa-seedling"></i> GrowWise</h3>
            <p>Gestión Inteligente</p>
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
                <li><a href="{{ route('buscar.index') }}"><i class="fas fa-search"></i> Buscar cultivos</a></li>
            </ul>
        </div>

    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header" data-aos="fade-down" data-aos-duration="1000">
            <div class="header-title">
                <h1>Gestión General</h1>
                <p>Bienvenido, {{ auth()->user()->nombre . ' ' . auth()->user()->apellido }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('alertas.index') }}" class="notification-badge">
                    <i class="fas fa-bell"></i>
                    @if(isset($stats['alertas_pendientes']) && $stats['alertas_pendientes'] > 0)
                        <span>{{ $stats['alertas_pendientes'] }}</span>
                    @endif
                </a>
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
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <a href="{{ route('cultivos.index') }}" class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['total_cultivos'] ?? 0 }}</h3>
                    <p>Total Cultivos</p>
                    <small>En desarrollo</small>
                </div>
                <div class="stat-icon"><i class="fas fa-seedling"></i></div>
            </a>

            <a href="{{ route('siembras.index') }}" class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-info">
                    <h3>${{ number_format($stats['inversion_total'] ?? 0, 2) }}</h3>
                    <p>Inversión Total</p>
                    <small>Semillas + Sustrato + Luz</small>
                </div>
                <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            </a>

            <a href="{{ route('alertas.index') }}" class="stat-card" data-aos="fade-up" data-aos-delay="150">
                <div class="stat-info">
                    <h3>{{ $stats['alertas_pendientes'] ?? 0 }}</h3>
                    <p>Alertas Pendientes</p>
                    <small>{{ ($stats['alertas_pendientes'] ?? 0) > 0 ? '⚠️ Requieren atención' : '✅ Todo en orden' }}</small>
                </div>
                <div class="stat-icon"><i class="fas fa-bell"></i></div>
            </a>

            <a href="{{ route('siembras.index') }}" class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-info">
                    <h3>{{ $stats['total_siembras'] ?? 0 }}</h3>
                    <p>Total Siembras</p>
                    <small>Historial completo</small>
                </div>
                <div class="stat-icon"><i class="fas fa-sprout"></i></div>
            </a>

            <a href="{{ route('vendedor.ventas.index') }}" class="stat-card" data-aos="fade-up" data-aos-delay="250">
                <div class="stat-info">
                    <h3>${{ number_format($stats['ventas_totales'] ?? 0, 2) }}</h3>
                    <p>Ventas Totales</p>
                    <small>💰 Ingresos generados</small>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </a>
        </div>

        <!-- Dos columnas: Siembras Recientes y Evaluaciones -->
        <div class="row-custom">
            <div class="card-custom" data-aos="fade-right" data-aos-delay="100">
                <h5><i class="fas fa-history"></i> Siembras Recientes</h5>
                @if($siembrasRecientes->count() > 0)
                    @foreach($siembrasRecientes as $siembra)
                        <a href="{{ route('siembras.show', $siembra->id) }}" class="siembra-item">
                            <div class="siembra-icon">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div class="siembra-content">
                                <div class="siembra-title">{{ $siembra->cultivo->nombre ?? 'Cultivo' }}</div>
                                <div class="siembra-subtitle">{{ $siembra->modulo->nombre ?? 'Módulo' }} • {{ $siembra->fecha_siembra ? $siembra->fecha_siembra->format('d/m/Y') : '' }}</div>
                            </div>
                            <div class="siembra-progress">
                                <div class="siembra-progress-bar" style="width: {{ $siembra->progreso ?? 0 }}%"></div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-seedling"></i>
                        <p>No hay siembras registradas.</p>
                    </div>
                @endif
            </div>

            <div class="card-custom" data-aos="fade-left" data-aos-delay="200">
                <h5><i class="fas fa-chart-pie"></i> Evaluaciones Recientes</h5>
                @if($evaluacionesRecientes->count() > 0)
                    @foreach($evaluacionesRecientes as $evaluacion)
                        <div class="siembra-item">
                            <div class="siembra-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="siembra-content">
                                <div class="siembra-title">{{ $evaluacion->cultivo_nombre ?? 'Cultivo' }}</div>
                                <div class="siembra-subtitle">Rendimiento: {{ $evaluacion->rendimiento ?? 0 }}/10 • {{ \Carbon\Carbon::parse($evaluacion->fecha_evaluacion)->format('d/m/Y') }}</div>
                            </div>
                            <div class="siembra-progress">
                                <div class="siembra-progress-bar" style="width: {{ ($evaluacion->rendimiento ?? 0) * 10 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <p>No hay evaluaciones registradas.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monitoreo Ambiental y Sistema de Riego -->
        <div class="row-custom" style="margin-bottom: 40px;">
            <!-- Monitoreo Ambiental -->
            <div class="card-custom" data-aos="fade-up" data-aos-delay="300">
                <h5><i class="fas fa-leaf"></i> Monitoreo Ambiental</h5>
                <div class="monitoring-grid">
                    <div class="monitor-item">
                        <i class="fas fa-thermometer-half"></i>
                        <h4>{{ $monitoreo['temperatura'] ?? '--' }}°C</h4>
                        <p>Temperatura</p>
                    </div>
                    <div class="monitor-item">
                        <i class="fas fa-sun"></i>
                        <h4>{{ $monitoreo['luz'] ?? '--' }} lux</h4>
                        <p>Luz</p>
                    </div>
                    <div class="monitor-item">
                        <i class="fas fa-tint"></i>
                        <h4>{{ $monitoreo['humedad_charola1'] ?? '--' }}%</h4>
                        <p>Charola 1 (Rábano)</p>
                    </div>
                    <div class="monitor-item">
                        <i class="fas fa-tint"></i>
                        <h4>{{ $monitoreo['humedad_charola2'] ?? '--' }}%</h4>
                        <p>Charola 2 (Lechuga)</p>
                    </div>
                    <div class="monitor-item">
                        <i class="fas fa-tint"></i>
                        <h4>{{ $monitoreo['humedad_charola3'] ?? '--' }}%</h4>
                        <p>Charola 3 (Espinaca)</p>
                    </div>
                    <div class="monitor-item">
                        <i class="fas fa-tint"></i>
                        <h4>{{ $monitoreo['humedad_charola4'] ?? '--' }}%</h4>
                        <p>Charola 4 (Cilantro)</p>
                    </div>
                </div>
            </div>

            <!-- Sistema de Riego -->
            <div class="card-custom" data-aos="fade-up" data-aos-delay="350">
                <h5><i class="fas fa-water"></i> Sistema de Riego</h5>

                @php
                    $horasRiego = [0, 8, 16];
                    $horaActual = date('H');
                    $proximaHora = null;

                    foreach ($horasRiego as $hora) {
                        if ($horaActual < $hora) {
                            $proximaHora = $hora;
                            break;
                        }
                    }
                    if ($proximaHora === null) {
                        $proximaHora = $horasRiego[0] + 24;
                    }

                    $proximaHoraFormateada = sprintf("%02d:00", $proximaHora % 24);
                    $horasRestantes = $proximaHora - $horaActual;
                    if ($horasRestantes < 0) $horasRestantes += 24;

                    $esHoraRiego = in_array($horaActual, $horasRiego);
                    $minutoActual = date('i');
                    $esMinutoRiego = $esHoraRiego && $minutoActual <= 1;
                @endphp

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="riego-item">
                        <span><i class="fas fa-clock"></i> Frecuencia:</span>
                        <span>Cada 8 horas</span>
                    </div>
                    <div class="riego-item">
                        <span><i class="fas fa-tint"></i> Volumen por riego:</span>
                        <span>750 ml</span>
                    </div>
                    <div class="riego-item">
                        <span><i class="fas fa-hourglass-half"></i> Duración:</span>
                        <span>15 segundos</span>
                    </div>
                    <div class="riego-item" style="background: rgba(46,125,50,0.1);">
                        <span><i class="fas fa-calendar-check"></i> Próximo riego:</span>
                        <span style="color: var(--naranja);">{{ $proximaHoraFormateada }} hrs (en {{ $horasRestantes }}h)</span>
                    </div>
                    <div class="riego-item">
                        <span><i class="fas fa-chart-line"></i> Consumo diario:</span>
                        <span>2,250 ml (3 riegos)</span>
                    </div>
                    <div class="riego-item">
                        <span><i class="fas fa-chart-simple"></i> Consumo semanal:</span>
                        <span>15,750 ml (15.75 L)</span>
                    </div>
                </div>

                <div class="riego-status {{ $esMinutoRiego ? 'activo' : '' }}">
                    @if($esMinutoRiego)
                        <i class="fas fa-play-circle" style="color: #4CAF50;"></i>
                        <span style="color: #4CAF50; font-weight: 600;">💧 Riego en curso...</span>
                    @else
                        <i class="fas fa-pause-circle" style="color: #FF9800;"></i>
                        <span style="color: #FF9800; font-weight: 600;">⏳ Esperando próximo ciclo de riego</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alertas Recientes -->
        <div class="card-custom" data-aos="fade-up" data-aos-delay="400">
            <div class="alertas-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Alertas Recientes</h5>
                <a href="{{ route('alertas.index') }}" class="btn-naranja">Ver Todas</a>
            </div>
            @if($alertasRecientes->count() > 0)
                @foreach($alertasRecientes as $alerta)
                    <a href="{{ route('alertas.index') }}" class="alert-item">
                        <div class="alert-icon {{ strtolower($alerta->prioridad ?? 'media') }}">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">{{ $alerta->titulo ?? 'Alerta' }}</div>
                            <div class="alert-time">{{ $alerta->created_at ? $alerta->created_at->diffForHumans() : 'Recientemente' }}</div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fas fa-check-circle" style="color: var(--verde-hoja);"></i>
                    <p>No hay alertas pendientes. Todo en orden.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });
</script>
</body>
</html>
