<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id_cliente', 'user_id_vendedor', 'id_direccion_envio', 'id_metodo_pago',
        'subtotal', 'impuesto', 'total_final', 'estado', 'fecha_pedido', 'notas'
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime'
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id_cliente');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id_vendedor');
    }

    public function direccion()
    {
        return $this->belongsTo(DireccionEnvio::class, 'id_direccion_envio');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total_final, 2);
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'confirmado' => 'info',
            'enviado' => 'primary',
            'entregado' => 'success',
            'cancelado' => 'danger',
            default => 'secondary'
        };
    }
}
