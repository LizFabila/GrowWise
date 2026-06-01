<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'pedidos_detalle';

    protected $fillable = [
        'pedido_id', 'producto_venta_id', 'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto()
    {
        return $this->belongsTo(ProductoVenta::class, 'producto_venta_id');
    }
}
