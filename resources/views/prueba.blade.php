<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agrícola - Vista Previa</title>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fon
    ts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Aquí va tu código CSS -->
    <style>
        /* ===== TU CÓDIGO CSS COMPLETO VA AQUÍ ===== */
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

        /* Dashboard layout */
        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
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
            transition: var(--transicion);
        }

        .sidebar-header h3 {
            font-weight: 800;
            color: var(--verde-hoja);
            font-size: 1.8rem;
            margin-bottom: 5px;
            transition: var(--transicion);
        }

        .sidebar-header h3 i {
            color: var(--naranja);
            margin-right: 10px;
            transition: transform 0.5s ease;
        }

        .sidebar-header:hover h3 i {
            transform: rotate(360deg);
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
            transition: var(--transicion);
            border-left: 4px solid transparent;
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

        .sidebar-menu a:hover::before {
            left: 100%;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(46,125,50,0.05);
            color: var(--verde-hoja);
            border-left-color: var(--verde-hoja);
        }

        .sidebar-menu a i {
            width: 30px;
            font-size: 1.3rem;
            margin-right: 10px;
            color: var(--verde-hoja);
            transition: transform 0.3s ease;
        }

        .sidebar-menu a:hover i {
            transform: scale(1.2);
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
            background: white;
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }

        .dashboard-header:hover {
            box-shadow: var(--sombra-media);
            transform: translateY(-2px);
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin-bottom: 5px;
            transition: var(--transicion);
        }

        .header-title h1:hover {
            color: var(--naranja);
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
            transform: scale(1.1) rotate(5deg);
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
            transition: var(--transicion);
        }

        .notification-badge:hover span {
            background: var(--verde-hoja);
            transform: scale(1.1);
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

        .dropdown-item.text-danger:hover {
            background: rgba(220,53,69,0.1);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .user-profile:hover img {
            transform: scale(1.1);
        }

        .user-profile span {
            font-weight: 600;
        }

        /* Tarjetas de resumen */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(46,125,50,0.1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--sombra-media);
            border-color: var(--verde-hoja);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--verde-hoja);
            margin-bottom: 5px;
            transition: var(--transicion);
        }

        .stat-card:hover .stat-info h3 {
            color: var(--naranja);
        }

        .stat-info p {
            color: #666;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-info small {
            color: #999;
            font-size: 0.8rem;
            transition: var(--transicion);
        }

        .stat-card:hover .stat-info small {
            color: var(--verde-hoja);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--verde-menta), var(--verde-hoja));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            transition: var(--transicion);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
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
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin-bottom: 20px;
            transition: var(--transicion);
        }

        .card-custom:hover h5 {
            color: var(--naranja);
        }

        .card-custom h5 i {
            margin-right: 10px;
            color: var(--naranja);
            transition: var(--transicion);
        }

        .card-custom:hover h5 i {
            transform: scale(1.2) rotate(5deg);
            color: var(--verde-hoja);
        }

        /* Lista de siembras */
        .siembra-item {
            display: flex;
            align-items: center;
            padding: 15px;
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
            transform: translateX(5px) scale(1.01);
            box-shadow: var(--sombra-suave);
        }

        .siembra-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(46,125,50,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--verde-hoja);
            font-size: 1.2rem;
            transition: var(--transicion);
        }

        .siembra-item:hover .siembra-icon {
            background: var(--verde-hoja);
            color: white;
            transform: rotate(5deg);
        }

        .siembra-content {
            flex: 1;
        }

        .siembra-title {
            font-weight: 600;
            transition: var(--transicion);
        }

        .siembra-item:hover .siembra-title {
            color: var(--verde-hoja);
        }

        .siembra-subtitle {
            font-size: 0.85rem;
            color: #888;
        }

        .siembra-progress {
            width: 100px;
            height: 6px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-left: 15px;
        }

        .siembra-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--verde-hoja), var(--naranja));
            border-radius: 10px;
        }

        /* Monitoreo ambiental */
        .monitoring-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .monitor-item {
            text-align: center;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px 10px;
            transition: var(--transicion);
            cursor: pointer;
        }

        .monitor-item:hover {
            background: var(--verde-menta);
            color: white;
            transform: scale(1.05) translateY(-5px);
            box-shadow: var(--sombra-media);
        }

        .monitor-item i {
            font-size: 2rem;
            color: var(--verde-hoja);
            margin-bottom: 10px;
            transition: var(--transicion);
        }

        .monitor-item:hover i {
            color: white;
            transform: scale(1.2);
        }

        .monitor-item h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            transition: var(--transicion);
        }

        .monitor-item:hover h4 {
            color: white;
        }

        .monitor-item p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
            transition: var(--transicion);
        }

        .monitor-item:hover p {
            color: white;
        }

        /* Alertas */
        .alertas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .alertas-header h5 {
            margin-bottom: 0;
            transition: var(--transicion);
        }

        .btn-naranja {
            background: var(--naranja);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transicion);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-naranja:hover {
            background: var(--naranja-oscuro);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 15px rgba(255,152,0,0.4);
            color: white;
        }

        .btn-verde {
            background: var(--verde-hoja);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transicion);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-verde:hover {
            background: var(--verde-oscuro);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 15px rgba(46,125,50,0.4);
            color: white;
        }

        .alert-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 15px;
            background: #f8f9fa;
            margin-bottom: 10px;
            transition: var(--transicion);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .alert-item:hover {
            background: #e9ecef;
            transform: translateX(5px) scale(1.01);
            box-shadow: var(--sombra-suave);
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            transition: var(--transicion);
        }

        .alert-icon.Crítica, .alert-icon.critica { background: rgba(220,53,69,0.1); color: #dc3545; }
        .alert-icon.Alta, .alert-icon.alta { background: rgba(255,152,0,0.1); color: var(--naranja); }
        .alert-icon.Media, .alert-icon.media { background: rgba(100,181,246,0.1); color: var(--azul-cielo); }
        .alert-icon.Baja, .alert-icon.baja { background: rgba(46,125,50,0.1); color: var(--verde-hoja); }

        .alert-item:hover .alert-icon.Crítica,
        .alert-item:hover .alert-icon.critica { background: #dc3545; color: white; }
        .alert-item:hover .alert-icon.Alta,
        .alert-item:hover .alert-icon.alta { background: var(--naranja); color: white; }
        .alert-item:hover .alert-icon.Media,
        .alert-item:hover .alert-icon.media { background: var(--azul-cielo); color: white; }
        .alert-item:hover .alert-icon.Baja,
        .alert-item:hover .alert-icon.baja { background: var(--verde-hoja); color: white; }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            transition: var(--transicion);
        }

        .alert-item:hover .alert-title {
            color: var(--verde-hoja);
        }

        .alert-time {
            font-size: 0.8rem;
            color: #888;
            transition: var(--transicion);
        }

        .alert-item:hover .alert-time {
            color: var(--verde-oscuro);
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .row-custom {
                grid-template-columns: 1fr;
            }
            .monitoring-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <!-- SIDEBAR (Menú lateral) -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-leaf"></i> AgroControl</h3>
            <p>Sistema de monitoreo</p>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="#" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="#"><i class="fas fa-seedling"></i> Siembras</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
                <li><a href="#"><i class="fas fa-tint"></i> Riego</a></li>
                <li><a href="#"><i class="fas fa-temperature-high"></i> Clima</a></li>
                <li><a href="#"><i class="fas fa-bell"></i> Alertas</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuración</a></li>
                <li><a href="{{ route('tienda.index') }}"><i class="fas fa-store"></i> Tienda</a></li>
                <li><a href="{{ route('ventas.index') }}"><i class="fas fa-tags"></i> Mis Ventas</a></li>
                <li><a href="{{ route('pedidos.index') }}"><i class="fas fa-shopping-cart"></i> Mis Pedidos</a></li>
                <li><a href="{{ route('direcciones.index') }}"><i class="fas fa-map-marker-alt"></i> Direcciones</a></li>
            </ul>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <!-- HEADER -->
        <div class="dashboard-header">
            <div class="header-title">
                <h1>Bienvenido, Juan</h1>
                <p>Aquí está el resumen de tus cultivos</p>
            </div>
            <div class="header-actions">
                <a href="#" class="notification-badge">
                    <i class="fas fa-bell"></i>
                    <span>3</span>
                </a>
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="User">
                    <span>Juan Pérez</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- TARJETAS DE RESUMEN (STATS) -->
        <div class="stats-grid">
            <a href="#" class="stat-card">
                <div class="stat-info">
                    <h3>24</h3>
                    <p>Siembras activas</p>
                    <small>+3 esta semana</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-seedling"></i>
                </div>
            </a>
            <a href="#" class="stat-card">
                <div class="stat-info">
                    <h3>156</h3>
                    <p>Plantas</p>
                    <small>12 en cosecha</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tree"></i>
                </div>
            </a>
            <a href="#" class="stat-card">
                <div class="stat-info">
                    <h3>85%</h3>
                    <p>Humedad</p>
                    <small>Nivel óptimo</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tint"></i>
                </div>
            </a>
            <a href="#" class="stat-card">
                <div class="stat-info">
                    <h3>23°C</h3>
                    <p>Temperatura</p>
                    <small>+2°C que ayer</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-temperature-high"></i>
                </div>
            </a>
        </div>

        <!-- FILA DE DOS COLUMNAS -->
        <div class="row-custom">
            <!-- COLUMNA IZQUIERDA: Siembras recientes -->
            <div class="card-custom">
                <h5><i class="fas fa-seedling"></i> Siembras activas</h5>

                <a href="#" class="siembra-item">
                    <div class="siembra-icon"><i class="fas fa-leaf"></i></div>
                    <div class="siembra-content">
                        <div class="siembra-title">Tomate Cherry</div>
                        <div class="siembra-subtitle">Invernadero 3 · 45 plantas</div>
                    </div>
                    <div class="siembra-progress">
                        <div class="siembra-progress-bar" style="width: 75%"></div>
                    </div>
                </a>

                <a href="#" class="siembra-item">
                    <div class="siembra-icon"><i class="fas fa-carrot"></i></div>
                    <div class="siembra-content">
                        <div class="siembra-title">Zanahoria</div>
                        <div class="siembra-subtitle">Campo abierto · 120 plantas</div>
                    </div>
                    <div class="siembra-progress">
                        <div class="siembra-progress-bar" style="width: 40%"></div>
                    </div>
                </a>

                <a href="#" class="siembra-item">
                    <div class="siembra-icon"><i class="fas fa-apple-alt"></i></div>
                    <div class="siembra-content">
                        <div class="siembra-title">Fresas</div>
                        <div class="siembra-subtitle">Macrotúnel · 200 plantas</div>
                    </div>
                    <div class="siembra-progress">
                        <div class="siembra-progress-bar" style="width: 90%"></div>
                    </div>
                </a>

                <a href="#" class="siembra-item">
                    <div class="siembra-icon"><i class="fas fa-pepper-hot"></i></div>
                    <div class="siembra-content">
                        <div class="siembra-title">Pimiento Morrón</div>
                        <div class="siembra-subtitle">Invernadero 2 · 80 plantas</div>
                    </div>
                    <div class="siembra-progress">
                        <div class="siembra-progress-bar" style="width: 25%"></div>
                    </div>
                </a>

                <div style="margin-top: 15px; text-align: right;">
                    <a href="#" class="btn-verde" style="font-size: 0.9rem;">Ver todas <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Alertas -->
            <div class="card-custom">
                <div class="alertas-header">
                    <h5><i class="fas fa-exclamation-triangle"></i> Alertas activas</h5>
                    <a href="#" class="btn-naranja" style="font-size: 0.9rem;">Ver todas</a>
                </div>

                <a href="#" class="alert-item">
                    <div class="alert-icon Crítica"><i class="fas fa-tint"></i></div>
                    <div class="alert-content">
                        <div class="alert-title">Riego urgente - Invernadero 2</div>
                        <div class="alert-time">Hace 10 minutos</div>
                    </div>
                </a>

                <a href="#" class="alert-item">
                    <div class="alert-icon Alta"><i class="fas fa-temperature-high"></i></div>
                    <div class="alert-content">
                        <div class="alert-title">Temperatura alta - Sector A</div>
                        <div class="alert-time">Hace 25 minutos</div>
                    </div>
                </a>

                <a href="#" class="alert-item">
                    <div class="alert-icon Media"><i class="fas fa-bug"></i></div>
                    <div class="alert-content">
                        <div class="alert-title">Plagas detectadas - Tomates</div>
                        <div class="alert-time">Hace 2 horas</div>
                    </div>
                </a>

                <a href="#" class="alert-item">
                    <div class="alert-icon Baja"><i class="fas fa-seedling"></i></div>
                    <div class="alert-content">
                        <div class="alert-title">Fertilizante próximo a vencer</div>
                        <div class="alert-time">Hace 1 día</div>
                    </div>
                </a>
            </div>
        </div>
        <!-- MONITOREO AMBIENTAL -->
        <div class="card-custom" style="margin-bottom: 40px;">
            <h5><i class="fas fa-cloud-sun"></i> Monitoreo ambiental en tiempo real</h5>
            <div class="monitoring-grid">
                <div class="monitor-item">
                    <i class="fas fa-thermometer-half"></i>
                    <h4>23°C</h4>
                    <p>Temperatura</p>
                </div>
                <div class="monitor-item">
                    <i class="fas fa-tint"></i>
                    <h4>85%</h4>
                    <p>Humedad</p>
                </div>
                <div class="monitor-item">
                    <i class="fas fa-sun"></i>
                    <h4>450W/m²</h4>
                    <p>Radiación</p>
                </div>
                <div class="monitor-item">
                    <i class="fas fa-wind"></i>
                    <h4>12km/h</h4>
                    <p>Viento</p>
                </div>
                <div class="monitor-item">
                    <i class="fas fa-cloud-rain"></i>
                    <h4>40%</h4>
                    <p>Lluvia</p>
                </div>
            </div>
        </div>
        <!-- SECCIÓN DE EJEMPLO ADICIONAL -->
        <div style="display: flex; gap: 20px; justify-content: space-between; margin-bottom: 30px;">
            <a href="#" class="btn-verde" style="padding: 12px 30px;"><i class="fas fa-plus"></i> Nueva siembra</a>
            <a href="#" class="btn-naranja" style="padding: 12px 30px;"><i class="fas fa-chart-bar"></i> Generar reporte</a>
        </div>
    </div>
</div>
</body>
</html>
