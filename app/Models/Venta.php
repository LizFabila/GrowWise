<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'user_id_vendedor', 'user_id_cliente', 'pedido_id', 'total', 'fecha_venta', 'estado'
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id_vendedor');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id_cliente');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
