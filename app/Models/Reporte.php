<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'periodo_inicio',
        'periodo_fin',
        'formato',
        'archivo_url',
        'tamaño_kb',
        'parametros',
        'descargado',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
