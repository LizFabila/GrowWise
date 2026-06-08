<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrowWise - @yield('title', 'Sistema de Gestión')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* Aquí va todo tu CSS existente */
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

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #1a2a2f;
            background: linear-gradient(135deg, #1a2a2f 0%, #0f1a1e 100%);
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            transition: var(--transicion);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
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
        }

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
        }

        .sidebar-menu a:hover i { color: #FF9800; }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            overflow-y: auto;
        }

        /* Dashboard header */
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
        }

        .header-title h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 5px;
        }

        .header-title p { color: #666; font-size: 0.8rem; }

        /* Perfil de usuario */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 15px;
            border-radius: 50px;
            background: #f5f5f5;
        }

        .user-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .user-profile span { font-weight: 500; font-size: 0.85rem; }

        /* Botones */
        .btn-naranja {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

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
        }

        .btn-outline-verde:hover {
            background: #2E7D32;
            color: white;
        }

        /* Tarjetas de estadísticas */
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
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(46,125,50,0.08);
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(46,125,50,0.12);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2E7D32;
            margin-bottom: 5px;
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
        }

        /* Tablas */
        .table-container {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
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

        .table-header h2, .table-header h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2E7D32;
            margin: 0;
        }

        /* Badges */
        .badge-alerta {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-resuelta { background: rgba(46,125,50,0.15); color: #2E7D32; }
        .badge-pendiente { background: rgba(255,152,0,0.15); color: #F57C00; }
        .badge-ignorada { background: rgba(108,117,125,0.15); color: #6c757d; }

        /* Botones de acción */
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
        }

        .action-btn.resolver, .action-btn.ver { background: #2E7D32; }
        .action-btn.eliminar { background: #dc3545; }
        .action-btn.editar { background: #FF9800; }

        .action-btn:hover {
            transform: scale(1.1) translateY(-2px);
            filter: brightness(1.1);
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: #FFFFFF;
            border-radius: 20px;
        }

        .empty-state i { font-size: 3.5rem; color: #ddd; margin-bottom: 15px; }

        /* Responsive */
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
        }
    </style>
</head>
<body>
<div class="dashboard">
    <!-- Incluir la barra lateral -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-title">
                <h1>@yield('header-title', 'Panel de Control')</h1>
                <p>@yield('header-subtitle', 'Bienvenido, ' . auth()->user()->nombre)</p>
            </div>
            <div class="header-actions d-flex align-items-center gap-3">
                <a href="{{ route('alertas.index') }}" class="notification-badge position-relative">
                    <i class="fas fa-bell fa-lg"></i>
                    @php
                        $alertasCount = \App\Models\Alerta::where('user_id', auth()->id())->where('estado', 'Pendiente')->count();
                    @endphp
                    @if($alertasCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            {{ $alertasCount }}
                        </span>
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
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info mb-3">{{ session('info') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 100 });
</script>
@stack('scripts')
</body>
</html>
