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
