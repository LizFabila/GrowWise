<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrowWise - Tienda</title>

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

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #E2E8F0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #2E7D32; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1B5E20; }

        /* Navbar - Mismo color oscuro que la sidebar del vendedor */
        .navbar {
            background: #1a2a2f;
            background: linear-gradient(135deg, #1a2a2f 0%, #0f1a1e 100%);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            padding: 15px 0;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .navbar-brand {
            font-weight: 800;
            color: #FFFFFF !important;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            transition: all 0.3s ease;
        }

        .navbar-brand i {
            color: #FF9800;
            transition: transform 0.5s ease;
        }

        .navbar-brand:hover i { transform: rotate(360deg); }

        .nav-link {
            font-weight: 500;
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s ease;
            margin: 0 10px;
            position: relative;
            padding: 5px 0 !important;
            font-size: 0.9rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #FF9800, #F57C00);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after, .nav-link.active::after { width: 80%; }
        .nav-link:hover { color: #FF9800 !important; }

        /* Dropdown de usuario en navbar */
        .dropdown-toggle::after {
            display: none;
        }

        .user-btn {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .user-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .user-btn i {
            margin-right: 8px;
            color: #FF9800;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 8px 0;
            margin-top: 10px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: rgba(46,125,50,0.08);
            color: #2E7D32;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            color: #2E7D32;
        }

        /* Carrito badge */
        .cart-badge {
            position: relative;
            display: inline-block;
            margin-right: 15px;
        }

        .cart-icon {
            font-size: 1.3rem;
            color: white;
            transition: all 0.3s ease;
        }

        .cart-icon:hover {
            color: #FF9800;
            transform: scale(1.1);
        }

        .cart-count {
            position: absolute;
            top: -10px;
            right: -12px;
            background: #FF9800;
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 50%;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

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

        /* Tarjetas de productos */
        .producto-card {
            background: #FFFFFF;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .producto-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .producto-imagen {
            height: 180px;
            background: linear-gradient(135deg, #81C784, #2E7D32);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .producto-imagen::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .producto-card:hover .producto-imagen::after { left: 100%; }

        .producto-imagen i { font-size: 3.5rem; color: white; }

        .producto-body { padding: 20px; }
        .producto-precio { font-size: 1.3rem; font-weight: 700; color: #2E7D32; }
        .producto-body p { font-size: 0.85rem; color: #666; }

        /* Main content */
        .main-content { min-height: calc(100vh - 200px); padding: 40px 0; }

        /* Footer */
        .footer {
            background: #1a2a2f;
            background: linear-gradient(135deg, #1a2a2f 0%, #0f1a1e 100%);
            color: #aaa;
            padding: 40px 0 20px;
        }

        .footer h5 {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .footer a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .footer a:hover {
            color: #FF9800;
            transform: translateX(3px);
        }

        .footer p { font-size: 0.85rem; color: rgba(255,255,255,0.5); }

        .footer .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 8px;
            transition: all 0.3s ease;
            color: rgba(255,255,255,0.6);
        }

        .footer .social-icons a:hover {
            background: #FF9800;
            color: white;
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 25px;
            margin-top: 30px;
            text-align: center;
            font-size: 0.8rem;
        }

        /* Alertas */
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

        @media (max-width: 768px) {
            .main-content { padding: 20px 0; }
            .producto-imagen { height: 140px; }
            .navbar .container { flex-direction: column; gap: 15px; }
            .navbar-nav { margin: 10px 0; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('cliente.tienda.index') }}">
            <i class="fas fa-seedling"></i> GrowWise
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.tienda.index') ? 'active' : '' }}"
                       href="{{ route('cliente.tienda.index') }}">Tienda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.tienda.categorias') ? 'active' : '' }}"
                       href="{{ route('cliente.tienda.categorias') }}">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.tienda.ofertas') ? 'active' : '' }}"
                       href="{{ route('cliente.tienda.ofertas') }}">Ofertas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cliente.tienda.contacto') ? 'active' : '' }}"
                       href="{{ route('cliente.tienda.contacto') }}">Contacto</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="{{ route('cliente.carrito.ver') }}" class="cart-badge">
                    <i class="fas fa-shopping-cart cart-icon"></i>
                    @php $cartCount = count(session()->get('carrito', [])); @endphp
                    @if($cartCount > 0)<span class="cart-count">{{ $cartCount }}</span>@endif
                </a>
                <div class="dropdown">
                    <button class="btn user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> {{ auth()->user()->nombre }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('cliente.pedidos.index') }}">
                                <i class="fas fa-box"></i> Mis Pedidos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('cliente.direcciones.index') }}">
                                <i class="fas fa-map-marker-alt"></i> Direcciones
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif
        @yield('content')
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>GrowWise</h5>
                <p>Tu huerto inteligente en casa. Cultiva fresco, come saludable.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Enlaces rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('cliente.tienda.index') }}">Tienda</a></li>
                    <li><a href="{{ route('cliente.tienda.categorias') }}">Categorías</a></li>
                    <li><a href="{{ route('cliente.tienda.ofertas') }}">Ofertas</a></li>
                    <li><a href="{{ route('cliente.tienda.contacto') }}">Contacto</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Contacto</h5>
                <p><i class="fas fa-envelope me-2"></i> info@growwise.com</p>
                <p><i class="fas fa-phone me-2"></i> +52 55 1234 5678</p>
                <p><i class="fas fa-map-marker-alt me-2"></i> Av. Tecnología 123, Ciudad</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} GrowWise. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 100 });
</script>
</body>
</html>
