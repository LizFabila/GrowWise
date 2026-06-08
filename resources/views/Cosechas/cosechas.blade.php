<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosechas - GrowWise</title>
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
            --naranja: #E67E22;
            --naranja-oscuro: #D35400;
            --azul-cielo: #64B5F6;
            --fondo: #F8F9FA;
            --sombra-suave: 0 10px 30px rgba(0,0,0,0.1);
            --sombra-media: 0 15px 40px rgba(0,0,0,0.15);
            --transicion: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            overflow-x: hidden;
        }

        .dashboard { display: flex; min-height: 100vh; }

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

        .sidebar-header p { color: #888; font-size: 0.9rem; }

        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu ul { list-style: none; }
        .sidebar-menu li { margin-bottom: 5px; }

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

        .header-title p { color: #666; font-size: 0.95rem; }

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

        .user-profile span { font-weight: 600; }

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

        .alert-info {
            background: rgba(100,181,246,0.1);
            color: var(--azul-cielo);
            border: 1px solid rgba(100,181,246,0.2);
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
            box-shadow: 0 10px 25px rgba(230,126,34,0.3);
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

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
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
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--sombra-media);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--verde-hoja);
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #666;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-info small {
            color: #999;
            font-size: 0.8rem;
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
            transform: rotate(5deg) scale(1.1);
        }

        /* Filtros */
        .filters-card {
            background: white;
            border-radius: 20px;
            padding: 20px 25px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
            margin-bottom: 30px;
        }

        .filters-card:hover { box-shadow: var(--sombra-media); }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group { flex: 1; min-width: 150px; }
        .filter-group label { font-weight: 500; margin-bottom: 5px; color: #666; display: block; }

        .filter-group select, .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            transition: var(--transicion);
            outline: none;
        }

        .filter-group select:focus, .filter-group input:focus {
            border-color: var(--verde-hoja);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }

        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--sombra-suave);
            transition: var(--transicion);
        }

        .table-container:hover { box-shadow: var(--sombra-media); }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--verde-hoja);
            margin: 0;
        }

        .table-header h2 i { margin-right: 10px; color: var(--naranja); }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 10px; color: #666; font-weight: 600; border-bottom: 2px solid #f0f0f0; }
        td { padding: 15px 10px; border-bottom: 1px solid #f0f0f0; transition: var(--transicion); }
        tr:hover { background: rgba(46,125,50,0.02); }

        .badge-calidad {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-excelente { background: rgba(46,125,50,0.15); color: var(--verde-hoja); }
        .badge-buena { background: rgba(100,181,246,0.15); color: var(--azul-cielo); }
        .badge-regular { background: rgba(255,152,0,0.15); color: var(--naranja-oscuro); }
        .badge-mala { background: rgba(220,53,69,0.15); color: #dc3545; }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: var(--transicion);
            margin: 0 3px;
            border: none;
            text-decoration: none;
        }
        .action-btn.ver { background: var(--azul-cielo); }
        .action-btn.editar { background: var(--naranja); }
        .action-btn.eliminar { background: #dc3545; }
        .action-btn:hover { transform: scale(1.15); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--sombra-suave);
        }
        .empty-state i { font-size: 4rem; color: #ddd; margin-bottom: 20px; }
        .empty-state h3 { font-size: 1.5rem; color: #666; margin-bottom: 10px; }
        .empty-state p { color: #999; margin-bottom: 20px; }

        @media (max-width: 992px) {
            .dashboard { flex-direction: column; }
            .sidebar { width: 100%; }
        }
        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; }
            .filter-row { flex-direction: column; }
            .filter-group { width: 100%; }
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
                <li><a href="{{ route('alertas.index') }}"><i class="fas fa-bell"></i> Alertas</a></li>
                <li><a href="{{ route('cosechas.index') }}" class="active"><i class="fas fa-carrot"></i> Cosechas</a></li>
                <li><a href="{{ route('reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a></li>
                <li><a href="{{ route('evaluaciones.index') }}"><i class="fas fa-chart-bar"></i> Evaluaciones</a></li>
                <li><a href="{{ route('buscar.index') }}"><i class="fas fa-search"></i> Buscar cultivos</a></li>
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header" data-aos="fade-down" data-aos-duration="1000">
            <div class="header-title">
                <h1>Gestión de Cosechas</h1>
                <p>Registra y monitorea tus cosechas</p>
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

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="50">
                <div class="stat-info">
                    <h3>{{ number_format($stats['peso_total'], 2) }} kg</h3>
                    <p>Total Cosechado</p>
                    <small>
                        @if(request('cultivo') || request('desde') || request('hasta'))
                            Según filtros aplicados
                        @else
                            Histórico total
                        @endif
                    </small>
                </div>
                <div class="stat-icon"><i class="fas fa-weight-hanging"></i></div>
            </div>
            <a href="{{ route('cosechas.proximas') }}" class="stat-card" data-aos="fade-up" data-aos-delay="100" style="text-decoration: none; cursor: pointer;">
                <div class="stat-info">
                    <h3>{{ $stats['pendientes'] }}</h3>
                    <p>Próximas Cosechas</p>
                    <small>Próximos días (clic para ver)</small>
                </div>
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
            </a>
        </div>

        <!-- Filtros (Cultivo + Rango de fechas) -->
        <div class="filters-card" data-aos="fade-up" data-aos-delay="100">
            <form method="GET" action="{{ route('cosechas.index') }}" class="filter-row">
                <div class="filter-group">
                    <label><i class="fas fa-seedling"></i> Cultivo</label>
                    <select name="cultivo">
                        <option value="">Todos los cultivos</option>
                        @foreach(\App\Models\Cultivo::where('activo', 1)->get() as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ request('cultivo') == $cultivo->id ? 'selected' : '' }}>
                                {{ $cultivo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-naranja"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('cosechas.index') }}" class="btn-outline-verde"><i class="fas fa-undo"></i> Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Tabla de cosechas -->
        <div class="table-container" data-aos="fade-up" data-aos-delay="200">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Registro de Cosechas</h2>
                <a href="{{ route('cosechas.create') }}" class="btn-naranja">
                    <i class="fas fa-plus-circle"></i> Nueva Cosecha
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th style="width: 8%">ID</th>
                        <th style="width: 20%">Cultivo</th>
                        <th style="width: 15%">Fecha Cosecha</th>
                        <th style="width: 15%">Cantidad</th>
                        <th style="width: 15%">Calidad</th>
                        <th style="width: 27%">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cosechas as $cosecha)
                        <tr>
                            <td class="fw-bold">#{{ str_pad($cosecha->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $cosecha->siembra->cultivo->nombre ?? 'N/A' }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($cosecha->fecha_cosecha)->format('d/m/Y') }}</td>
                            <td><span class="fw-bold">{{ number_format($cosecha->cantidad_kg, 2) }} kg</span></td>
                            <td>
                                <span class="badge-calidad
                                    @if($cosecha->calidad == 'Excelente') badge-excelente
                                    @elseif($cosecha->calidad == 'Buena') badge-buena
                                    @elseif($cosecha->calidad == 'Regular') badge-regular
                                    @else badge-mala
                                    @endif">
                                    {{ $cosecha->calidad }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('cosechas.show', $cosecha->id) }}" class="action-btn ver" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('cosechas.edit', $cosecha->id) }}" class="action-btn editar" title="Editar cosecha">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('cosechas.destroy', $cosecha->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta cosecha?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn eliminar" title="Eliminar cosecha">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-carrot"></i>
                                    <h3>No hay cosechas registradas</h3>
                                    <p>Registra tu primera cosecha</p>
                                    <a href="{{ route('cosechas.create') }}" class="btn-naranja mt-3">Nueva Cosecha</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $cosechas->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 50 });
</script>
</body>
</html>
