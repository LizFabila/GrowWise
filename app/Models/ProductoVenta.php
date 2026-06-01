<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVenta extends Model
{
    protected $table = 'productos_venta';

    protected $fillable = [
        'user_id', 'cultivo_id', 'cosecha_id', 'cantidad',
        'unidad', 'precio_unitario', 'stock', 'estado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class);
    }

    public function cosecha()
    {
        return $this->belongsTo(Cosecha::class);
    }

    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_unitario, 2) . ' MXN';
    }

    public function getStockFormateadoAttribute()
    {
        return number_format($this->stock, 2) . ' ' . $this->unidad;
    }

    public function scopeDisponible($query)
    {
        return $query->where('estado', 'disponible')->where('stock', '>', 0);
    }
}
