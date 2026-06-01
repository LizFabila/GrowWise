<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'avatar',
        'role',  // <--- AGREGAR ESTO
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ========== NUEVOS MÉTODOS PARA ROLES ==========
    public function isCliente()
    {
        return $this->role === 'cliente';
    }

    public function isVendedor()
    {
        return $this->role === 'vendedor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    // ================================================

    // Relaciones existentes (NO CAMBIAR)
    public function modulos()
    {
        return $this->hasMany(Modulo::class);
    }

    public function siembras()
    {
        return $this->hasMany(Siembra::class);
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    public function cosechas()
    {
        return $this->hasMany(Cosecha::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function configuraciones()
    {
        return $this->hasMany(Configuracion::class);
    }

    // Nuevas relaciones para ventas
    public function productosVenta()
    {
        return $this->hasMany(ProductoVenta::class, 'user_id');
    }

    public function ventasRealizadas()
    {
        return $this->hasMany(Venta::class, 'user_id_vendedor');
    }

    public function pedidosComoVendedor()
    {
        return $this->hasMany(Pedido::class, 'user_id_vendedor');
    }

    public function pedidosComoCliente()
    {
        return $this->hasMany(Pedido::class, 'user_id_cliente');
    }

    // Accesor
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Mutator
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}
