<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    protected $table = 'cultivos';

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'temperatura_optima_min',
        'temperatura_optima_max',
        'humedad_optima_min',
        'humedad_optima_max',
        'luz_optima_min',
        'luz_optima_max',
        'ph_optimo_min',
        'ph_optimo_max',
        'dias_cosecha',
        'imagen',
        'activo'
    ];

    protected $casts = [
        'temperatura_optima_min' => 'decimal:2',
        'temperatura_optima_max' => 'decimal:2',
        'humedad_optima_min' => 'integer',
        'humedad_optima_max' => 'integer',
        'luz_optima_min' => 'integer',
        'luz_optima_max' => 'integer',
        'ph_optimo_min' => 'decimal:1',
        'ph_optimo_max' => 'decimal:1',
        'dias_cosecha' => 'integer',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function siembras()
    {
        return $this->hasMany(Siembra::class);
    }

    public function lecturas()
    {
        return $this->hasMany(LecturaSensor::class, 'cultivo_id');
    }
    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Accesores
    public function getRangoTemperaturaAttribute()
    {
        if ($this->temperatura_optima_min && $this->temperatura_optima_max) {
            return "{$this->temperatura_optima_min}°C - {$this->temperatura_optima_max}°C";
        }
        return 'No especificado';
    }

    public function getRangoHumedadAttribute()
    {
        if ($this->humedad_optima_min && $this->humedad_optima_max) {
            return "{$this->humedad_optima_min}% - {$this->humedad_optima_max}%";
        }
        return 'No especificado';
    }
}
