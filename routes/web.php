<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CultivoController;
use App\Http\Controllers\SiembraController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CosechaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta principal - Página de bienvenida
Route::get('/', function () {
    return view('Principal.principal');
})->name('home');

Route::get('/prueba', function () {
    return view('prueba');
});
// Módulo: Cosechas
Route::get('/cosechas/proximas', [CosechaController::class, 'proximas'])->name('cosechas.proximas');
Route::resource('cosechas', CosechaController::class);
// ===========================================
// RUTAS DE AUTENTICACIÓN (PÚBLICAS)
// ===========================================
Route::get('/sesion', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/sesion', [LoginController::class, 'login']);
Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ===========================================
Route::middleware(['auth'])->group(function () {

    // Redirección después de login según rol
    Route::get('/redirect', function () {
        $user = auth()->user();
        if ($user->isVendedor() || $user->isAdmin()) {
            return redirect()->route('vendedor.dashboard');
        }
        return redirect()->route('cliente.tienda.index');
    })->name('redirect');

    // ===========================================
    // MÓDULOS EXISTENTES (Solo para VENDEDORES y ADMIN)
    // ===========================================
    Route::middleware(['role:vendedor,admin'])->group(function () {

        // Dashboard principal
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Módulo: Cultivos
        Route::resource('cultivos', CultivoController::class);

        // Módulo: Siembras
        Route::resource('siembras', SiembraController::class);

        // Módulo: Monitoreo
        Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
        Route::post('/monitoreo/actualizar', [MonitoreoController::class, 'actualizar'])->name('monitoreo.actualizar');

        // Módulo: Alertas
        Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::post('/alertas/{alerta}/resolver', [AlertaController::class, 'resolver'])->name('alertas.resolver');
        Route::post('/alertas/marcar-todas', [AlertaController::class, 'marcarTodasComoLeidas'])->name('alertas.marcar-todas');
        Route::delete('/alertas/{alerta}', [AlertaController::class, 'destroy'])->name('alertas.destroy');

        // Módulo: Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');
        Route::get('/reportes/{reporte}/descargar', [ReporteController::class, 'descargar'])->name('reportes.descargar');
        Route::get('/reportes/{reporte}/pdf', [ReporteController::class, 'verPdf'])->name('reportes.ver-pdf');
        Route::delete('/reportes/{reporte}', [ReporteController::class, 'destroy'])->name('reportes.destroy');



        // Módulo: Evaluaciones
        Route::resource('evaluaciones', EvaluacionController::class);

        // Módulo: Configuración
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('/configuracion/general', [ConfiguracionController::class, 'updateGeneral'])->name('configuracion.general');
        Route::put('/configuracion/monitoreo', [ConfiguracionController::class, 'updateMonitoreo'])->name('configuracion.monitoreo');
        Route::put('/configuracion/alertas', [ConfiguracionController::class, 'updateAlertas'])->name('configuracion.alertas');
        Route::put('/configuracion/riego', [ConfiguracionController::class, 'updateRiego'])->name('configuracion.riego');
        Route::put('/configuracion/perfil', [ConfiguracionController::class, 'updatePerfil'])->name('configuracion.perfil');
        Route::put('/configuracion/seguridad', [ConfiguracionController::class, 'updateSeguridad'])->name('configuracion.seguridad');
    });

    // ===========================================
    // MÓDULO DE VENTAS PARA CLIENTES
    // ===========================================
    Route::prefix('cliente')->name('cliente.')->middleware(['role:cliente'])->group(function () {

        // Tienda
        Route::get('/tienda', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'index'])->name('tienda.index');
        Route::get('/categorias', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'categorias'])->name('tienda.categorias');
        Route::get('/ofertas', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'ofertas'])->name('tienda.ofertas');
        Route::get('/contacto', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'contacto'])->name('tienda.contacto');
        Route::get('/producto/{id}', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'productoDetalle'])->name('tienda.detalle');

        // Carrito
        Route::post('/carrito/agregar', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'agregarCarrito'])->name('carrito.agregar');
        Route::get('/carrito', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'verCarrito'])->name('carrito.ver');
        Route::delete('/carrito/eliminar/{id}', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'eliminarDelCarrito'])->name('carrito.eliminar');
        Route::post('/carrito/actualizar', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'actualizarCarrito'])->name('carrito.actualizar');
        Route::post('/carrito/vaciar', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'vaciarCarrito'])->name('carrito.vaciar');

        // Checkout
        Route::get('/checkout/{producto_id?}', [App\Http\Controllers\Cliente\CheckoutClienteController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/procesar', [App\Http\Controllers\Cliente\CheckoutClienteController::class, 'procesar'])->name('checkout.procesar');

        // Mis Pedidos
        Route::get('/mis-pedidos', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'misPedidos'])->name('pedidos.index');
        Route::get('/pedido/{id}', [App\Http\Controllers\Cliente\TiendaClienteController::class, 'verPedido'])->name('pedidos.show');

        // Direcciones
        Route::get('/direcciones', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'index'])->name('direcciones.index');
        Route::get('/direcciones/crear', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'create'])->name('direcciones.create');
        Route::post('/direcciones', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'store'])->name('direcciones.store');
        Route::get('/direcciones/{id}/editar', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'edit'])->name('direcciones.edit');
        Route::put('/direcciones/{id}', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'update'])->name('direcciones.update');
        Route::delete('/direcciones/{id}', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'destroy'])->name('direcciones.destroy');
        Route::post('/direcciones/{id}/principal', [App\Http\Controllers\Cliente\DireccionClienteController::class, 'setPrincipal'])->name('direcciones.principal');
    });

    // ===========================================
    // MÓDULO DE VENTAS PARA VENDEDORES
    // ===========================================
    Route::prefix('vendedor')->name('vendedor.')->middleware(['role:vendedor,admin'])->group(function () {

        // Dashboard del vendedor
        Route::get('/dashboard', [App\Http\Controllers\Vendedor\DashboardVendedorController::class, 'index'])->name('dashboard');
        Route::get('/resumen-ejecutivo', [App\Http\Controllers\Vendedor\DashboardVendedorController::class, 'resumenEjecutivo'])->name('resumen');

        // Gestión de productos
        Route::get('/productos', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'index'])->name('productos.index');
        Route::get('/productos/crear', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'crear'])->name('productos.crear');
        Route::post('/productos', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'store'])->name('productos.store');
        Route::get('/productos/{id}/editar', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'editar'])->name('productos.editar');
        Route::put('/productos/{id}', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{id}', [App\Http\Controllers\Vendedor\ProductoVendedorController::class, 'destroy'])->name('productos.destroy');

        // Ventas
        Route::get('/ventas', [App\Http\Controllers\Vendedor\VentaVendedorController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/detalle/{id}', [App\Http\Controllers\Vendedor\VentaVendedorController::class, 'detalle'])->name('ventas.detalle');

        // Pedidos
        Route::get('/pedidos', [App\Http\Controllers\Vendedor\PedidoVendedorController::class, 'index'])->name('pedidos.index');
        Route::put('/pedidos/{id}/estado', [App\Http\Controllers\Vendedor\PedidoVendedorController::class, 'actualizarEstado'])->name('pedidos.estado');
    });

    // ===========================================
    // RUTAS DE REDIRECCIÓN PARA COMPATIBILIDAD
    // ===========================================
    Route::get('/tienda', function () {
        return redirect()->route('cliente.tienda.index');
    });

    Route::get('/mis-productos', function () {
        return redirect()->route('vendedor.productos.index');
    });

    Route::get('/mis-pedidos', function () {
        if (auth()->user()->isVendedor()) {
            return redirect()->route('vendedor.pedidos.index');
        }
        return redirect()->route('cliente.pedidos.index');
    });

    Route::get('/direcciones', function () {
        if (auth()->user()->isVendedor()) {
            return redirect()->route('vendedor.dashboard');
        }
        return redirect()->route('cliente.direcciones.index');
    });

    // ===========================================
    // BUSCADOR DE CULTIVOS
    // ===========================================
    Route::get('/buscador', [App\Http\Controllers\BuscarController::class, 'index'])->name('buscar.index');
    Route::get('/buscador/resultados', [App\Http\Controllers\BuscarController::class, 'buscar'])->name('buscar.resultados');
    Route::get('/buscador/autocomplete', [App\Http\Controllers\BuscarController::class, 'autocomplete'])->name('buscar.autocomplete');
    Route::get('/buscador/stats', [App\Http\Controllers\BuscarController::class, 'getStats'])->name('buscar.stats');

});
