<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo - GrowWise</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
            --transicion: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .sidebar-header h3 i {
            color: var(--naranja);
            margin-right: 10px;
        }

        .sidebar-header p {
            color: #888;
            font-size: 0.9rem;
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
            font-size: 1.3rem;
            margin-right: 10px;
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
            transition: var(--transicion);
        }

        .dashboard-header:hover {
            box-shadow: var(--sombra-media);
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin-bottom: 5px;
        }

        .header-title p {
            color: #666;
            font-size: 0.95rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            font-size: 1.5rem;
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
            font-size: 0.7rem;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--sombra-media);
            border-radius: 15px;
            padding: 10px 0;
            margin-top: 10px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: var(--transicion);
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
            transition: var(--transicion);
            background: #f5f5f5;
        }

        .user-profile:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-profile span {
            font-weight: 600;
        }

        .alert {
            border-radius: 50px;
            padding: 15px 25px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(46,125,50,0.1);
            color: var(--verde-hoja);
            border: 1px solid rgba(46,125,50,0.2);
        }

        .alert-danger {
            background: rgba(220,53,69,0.1);
            color: #dc3545;
            border: 1px solid rgba(220,53,69,0.2);
        }

        .btn-naranja {
            background: linear-gradient(135deg, var(--naranja), var(--naranja-oscuro));
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transicion);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-naranja:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,152,0,0.3);
            color: white;
        }

        .btn-outline-verde {
            background: transparent;
            border: 2px solid var(--verde-hoja);
            color: var(--verde-hoja);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transicion);
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-verde:hover {
            background: var(--verde-hoja);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46,125,50,0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
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
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--sombra-media);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--verde-hoja);
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #666;
            font-weight: 500;
            margin: 0;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--verde-menta), var(--verde-hoja));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            transition: var(--transicion);
        }

        .stat-card:hover .stat-icon {
            transform: rotate(5deg) scale(1.1);
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }

        .chart-card:hover {
            box-shadow: var(--sombra-media);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chart-header h5 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--verde-hoja);
            margin: 0;
        }

        .chart-header i {
            color: var(--naranja);
            margin-right: 5px;
        }

        canvas {
            max-height: 250px;
            width: 100% !important;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .sensores-section {
            margin-bottom: 40px;
        }

        .sensores-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .sensores-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--verde-hoja);
        }

        .sensores-header h2 i {
            margin-right: 10px;
            color: var(--naranja);
        }

        .sensores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .sensor-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
            border: 1px solid rgba(46,125,50,0.1);
        }

        .sensor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-media);
        }

        .sensor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .sensor-header h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--verde-oscuro);
            margin: 0;
        }

        .sensor-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #28a745;
            box-shadow: 0 0 10px #28a745;
        }

        .sensor-status.warning {
            background: var(--naranja);
            box-shadow: 0 0 10px var(--naranja);
        }

        .sensor-status.danger {
            background: #dc3545;
            box-shadow: 0 0 10px #dc3545;
        }

        .sensor-reading {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .sensor-reading span:first-child {
            color: #666;
            font-size: 0.85rem;
        }

        .sensor-reading span:last-child {
            font-weight: 700;
            color: var(--verde-hoja);
            font-size: 1.3rem;
        }

        .progress {
            height: 6px;
            border-radius: 10px;
            margin-top: 10px;
            background: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--verde-menta), var(--verde-hoja));
            border-radius: 10px;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }

        .table-container:hover {
            box-shadow: var(--sombra-media);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin: 0;
        }

        .table-header i {
            margin-right: 10px;
            color: var(--naranja);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 10px;
            color: #666;
            font-weight: 600;
            border-bottom: 2px solid #f0f0f0;
            font-size: 0.85rem;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.85rem;
        }

        tr:hover {
            background: rgba(46,125,50,0.02);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--sombra-suave);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            color: #666;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            .charts-row {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
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
                <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Vista general</a></li>
                <li><a href="{{ route('siembras.index') }}"><i class="fas fa-sprout"></i> Siembras</a></li>
                <li><a href="{{ route('cultivos.index') }}"><i class="fas fa-seedling"></i> Cultivos</a></li>
                <li><a href="{{ route('monitoreo.index') }}"><i class="fas fa-thermometer-half"></i> Monitoreo</a></li>
                <li><a href="{{ route('alertas.index') }}" class="active"><i class="fas fa-bell"></i> Alertas</a></li>
                <li><a href="{{ route('cosechas.index') }}"><i class="fas fa-carrot"></i> Cosechas</a></li>
                <li><a href="{{ route('reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a></li>
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
                <h1>Monitoreo Ambiental</h1>
                <p>Variables en tiempo real de tus cultivos</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('alertas.index') }}" class="notification-badge">
                    <i class="fas fa-bell"></i>
                    @php
                        $alertasCount = \App\Models\Alerta::where('user_id', auth()->id())->where('estado', 'Pendiente')->count();
                    @endphp
                    @if($alertasCount > 0)
                        <span>{{ $alertasCount }}</span>
                    @endif
                </a>
                <div class="dropdown">
                    <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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

        <!-- Tarjetas de resumen (sin nutrientes) -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ $stats['temperatura']['valor'] ?? '--' }}°C</h3>
                    <p>Temperatura</p>
                </div>
                <div class="stat-icon"><i class="fas fa-thermometer-half"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-info">
                    <h3>{{ $stats['humedad']['valor'] ?? '--' }}%</h3>
                    <p>Humedad promedio</p>
                </div>
                <div class="stat-icon"><i class="fas fa-tint"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="150">
                <div class="stat-info">
                    <h3>{{ $stats['luz']['valor'] ?? '--' }} lux</h3>
                    <p>Luz</p>
                </div>
                <div class="stat-icon"><i class="fas fa-sun"></i></div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-info">
                    <h3>pH {{ $stats['ph']['valor'] ?? '--' }}</h3>
                    <p>pH del suelo</p>
                </div>
                <div class="stat-icon"><i class="fas fa-flask"></i></div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-row">
            <!-- Gráfica de Temperatura -->
            <div class="chart-card" data-aos="fade-right" data-aos-delay="100">
                <div class="chart-header">
                    <h5><i class="fas fa-temperature-high"></i> Temperatura (°C) - Últimas 24h</h5>
                </div>
                <canvas id="tempChart" height="200"></canvas>
            </div>

            <!-- Gráfica de Humedad del Sustrato -->
            <div class="chart-card" data-aos="fade-left" data-aos-delay="200">
                <div class="chart-header">
                    <h5><i class="fas fa-tint"></i> Humedad del Sustrato (%) - Últimas 24h</h5>
                </div>
                <canvas id="humedadChart" height="200"></canvas>
                <div class="legend" id="humedadLegend"></div>
            </div>
        </div>

        <!-- Sensores por cultivo -->
        <div class="sensores-section">
            <div class="sensores-header">
                <h2><i class="fas fa-seedling"></i> Monitoreo de Cultivos</h2>
                <button class="btn-naranja btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>

            @if(isset($sensores) && $sensores->count() > 0)
                <div class="sensores-grid">
                    @foreach($sensores as $sensor)
                        @if(str_contains($sensor->nombre, 'Humedad Sustrato'))
                            <div class="sensor-card" data-aos="zoom-in" data-aos-delay="50">
                                <div class="sensor-header">
                                    <h6><i class="fas fa-tint me-1" style="color: var(--naranja);"></i> {{ str_replace('Humedad Sustrato - ', '', $sensor->nombre) }}</h6>
                                    @php
                                        $valor = $sensor->ultima_lectura ?? 0;
                                        $estado = 'success';
                                        if($valor < 50) $estado = 'danger';
                                        elseif($valor < 60) $estado = 'warning';
                                    @endphp
                                    <div class="sensor-status {{ $estado }}"></div>
                                </div>
                                <div class="sensor-reading">
                                    <span>Humedad del sustrato:</span>
                                    <span>{{ $valor }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ min($valor, 100) }}%"></div>
                                </div>
                                <div class="mt-2 d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        @if($sensor->ultima_lectura_at)
                                            {{ \Carbon\Carbon::parse($sensor->ultima_lectura_at)->diffForHumans() }}
                                        @else
                                            Sin datos
                                        @endif
                                    </small>
                                    <small class="text-success">
                                        <i class="fas fa-tint"></i> Riego cada 8h
                                    </small>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-seedling"></i>
                    <h3>No hay cultivos activos</h3>
                    <p>Registra tus siembras para ver el monitoreo</p>
                    <a href="{{ route('siembras.create') }}" class="btn-naranja">Agregar Siembra</a>
                </div>
            @endif
        </div>

        <!-- Lecturas recientes -->
        <div class="table-container" data-aos="fade-up" data-aos-delay="100">
            <div class="table-header">
                <h5><i class="fas fa-history"></i> Últimas lecturas registradas</h5>
                <button class="btn-outline-verde btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Cultivo</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lecturasRecientes ?? [] as $lectura)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($lectura->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ str_replace('Humedad Sustrato - ', '', $lectura->sensor_nombre) }}</td>
                            <td><i class="fas fa-tint text-primary"></i> Humedad</td>
                            <td class="fw-bold">{{ $lectura->valor }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-chart-line"></i>
                                    <p>Generando primeras lecturas...</p>
                                    <small class="text-muted">Las lecturas aparecerán en los próximos minutos</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 50 });

    // Colores para cada cultivo
    const coloresCultivos = {
        'Rábano': '#E91E63',
        'Lechuga': '#4CAF50',
        'Espinaca': '#2196F3',
        'Cilantro': '#FF9800'
    };

    // Datos de humedad por cultivo (24h)
    const datosHumedad = {
        'Rábano': [65, 63, 62, 60, 58, 62, 68, 72, 70, 68, 65, 63, 62, 61, 60, 62, 66, 70, 68, 65, 63, 62, 61, 60],
        'Lechuga': [60, 58, 55, 53, 50, 55, 62, 68, 65, 62, 60, 58, 56, 54, 52, 56, 60, 65, 62, 60, 58, 56, 54, 52],
        'Espinaca': [78, 76, 75, 73, 70, 72, 80, 85, 82, 80, 78, 76, 75, 74, 72, 74, 78, 82, 80, 78, 76, 74, 72, 70],
        'Cilantro': [68, 66, 65, 63, 60, 62, 70, 75, 72, 70, 68, 66, 65, 64, 62, 64, 68, 72, 70, 68, 66, 64, 62, 60]
    };

    // Datos de temperatura (24h)
    const datosTemperatura = [18.5, 18.2, 18.0, 18.5, 19.0, 20.5, 22.0, 23.5, 24.0, 24.5, 24.0, 23.0, 22.0, 21.0, 20.5, 20.0, 19.5, 19.0, 18.5, 18.0, 17.5, 17.0, 17.5, 18.0];

    const horas = Array.from({length: 24}, (_, i) => i + ':00');

    // Gráfico de temperatura
    let tempChart;
    const ctxTemp = document.getElementById('tempChart').getContext('2d');

    tempChart = new Chart(ctxTemp, {
        type: 'line',
        data: {
            labels: horas,
            datasets: [{
                label: 'Temperatura (°C)',
                data: datosTemperatura,
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46,125,50,0.1)',
                borderWidth: 2.5,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#2E7D32',
                pointBorderColor: '#fff',
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: { callbacks: { label: function(context) { return context.raw + '°C'; } } }
            },
            scales: {
                y: { beginAtZero: false, min: 15, max: 30, title: { display: true, text: 'Temperatura (°C)' } },
                x: { title: { display: true, text: 'Hora' } }
            }
        }
    });

    // Gráfico de humedad
    let humedadChart;
    const ctxHum = document.getElementById('humedadChart').getContext('2d');
    const legendContainer = document.getElementById('humedadLegend');

    const datasets = [];

    for (const [cultivo, datos] of Object.entries(datosHumedad)) {
        datasets.push({
            label: cultivo,
            data: datos,
            borderColor: coloresCultivos[cultivo],
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.3,
            fill: false,
            pointBackgroundColor: coloresCultivos[cultivo],
            pointBorderColor: '#fff',
            pointRadius: 2,
            pointHoverRadius: 5
        });

        legendContainer.innerHTML += `
            <div class="legend-item">
                <div class="legend-color" style="background: ${coloresCultivos[cultivo]}"></div>
                <span>${cultivo}</span>
            </div>
        `;
    }

    humedadChart = new Chart(ctxHum, {
        type: 'line',
        data: { labels: horas, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.raw + '%'; } } }
            },
            scales: {
                y: { beginAtZero: false, min: 40, max: 90, title: { display: true, text: 'Humedad (%)' } },
                x: { title: { display: true, text: 'Hora' } }
            }
        }
    });
</script>
</body>
</html>
