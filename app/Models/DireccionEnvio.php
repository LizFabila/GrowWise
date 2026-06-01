<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DireccionEnvio extends Model
{
    protected $table = 'direcciones_envio';

    protected $fillable = [
        'user_id', 'calle', 'numero', 'colonia', 'ciudad', 'estado',
        'codigo_postal', 'referencias', 'principal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDireccionCompletaAttribute()
    {
        return "{$this->calle} #{$this->numero}, {$this->colonia}, {$this->ciudad}, {$this->estado}, CP {$this->codigo_postal}";
    }
}
