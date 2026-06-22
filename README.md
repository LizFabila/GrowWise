# GrowWise

Sistema de monitoreo hortícola con sensores IoT (ESP32) y plataforma web Laravel para gestión de cultivos, siembras, cosechas y venta de producto.

## Instalación para nuevos clones

> **Importante:** este proyecto usa vistas SQL y un procedimiento almacenado además de tablas normales. La forma más confiable de levantarlo es importando el dump completo en lugar de depender únicamente de `php artisan migrate`.

### 1. Clonar y entrar al proyecto
```bash
cd C:\xampp\htdocs
git clone https://github.com/LizFabila/GrowWise.git
cd GrowWise
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar entorno
```bash
cp .env.ejemplo .env
php artisan key:generate
```
Revisa en `.env` que `DB_DATABASE=smartgarden_db` coincida con el nombre que le des a tu base en phpMyAdmin.

### 4. Crear la base de datos e importar el dump
En phpMyAdmin (o por consola MySQL):
1. Crea una base vacía llamada `smartgarden_db`.
2. Ve a la pestaña **Importar** y selecciona `database/sql/smartgarden_db.sql`.

Esto crea todas las tablas, vistas (`alertas_activas`, `dashboard_resumen`, `estadisticas_cosechas`, `monitoreo_actual`, `siembras_detalle`), el procedimiento almacenado `sp_costo_beneficio_vendedor`, y carga los datos de ejemplo con los que se ha trabajado el proyecto.

**No corras `php artisan migrate` después de importar el .sql** — las tablas ya existen y Laravel marcaría conflicto. Las migraciones en `database/migrations/` quedan como documentación del esquema y como respaldo si en algún momento se necesita reconstruir la base desde cero (en ese caso sí: `php artisan migrate` sin haber importado el .sql).

### 5. Levantar el proyecto
```bash
php artisan serve
```
Abre `http://localhost:8000`.

### Compilar assets (en otra terminal, si vas a editar estilos/JS)
```bash
npm run dev
```

### Nota sobre los seeders
Los seeders en `database/seeders/` (`MetodosPagoSeeder`, `UserRoleSeeder`, etc.) **no son necesarios** si ya importaste `database/sql/smartgarden_db.sql`, porque esos datos ya vienen incluidos en el dump. Correrlos después generaría registros duplicados (por ejemplo, métodos de pago repetidos). Solo úsalos si decides reconstruir la base desde cero con `php artisan migrate` en lugar de importar el .sql.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
