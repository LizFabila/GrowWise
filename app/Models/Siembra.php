<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siembra extends Model
{
    protected $table = 'siembras';

    protected $fillable = [
        'user_id',
        'cultivo_id',
        'modulo_id',
        'charola',
        'fecha_siembra',
        'cantidad_semillas',
        'fecha_estimada_cosecha',
        'fecha_cosecha_real',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_siembra' => 'date',
        'fecha_estimada_cosecha' => 'date',
        'fecha_cosecha_real' => 'date',
        'cantidad_semillas' => 'integer',
        'charola' => 'integer'
    ];

    // Relaciones
    public function lecturas()
    {
        return $this->hasMany(LecturaSensor::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class);
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }

    public function cosecha()
    {
        return $this->hasOne(Cosecha::class);
    }

    public function evaluacion()
    {
        return $this->hasOne(Evaluacion::class);
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    // Scopes
    public function scopeActiva($query)
    {
        return $query->where('estado', 'Activa');
    }

    public function scopeCompletada($query)
    {
        return $query->where('estado', 'Completada');
    }

    public function scopeConProblemas($query)
    {
        return $query->where('estado', 'Problema');
    }

    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorCosechar($query)
    {
        return $query->where('estado', 'Activa')
            ->whereNotNull('fecha_estimada_cosecha')
            ->where('fecha_estimada_cosecha', '<=', now()->addDays(15));
    }

    // Accesores
    public function getDiasTranscurridosAttribute()
    {
        return $this->fecha_siembra->diffInDays(now());
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_estimada_cosecha) {
            return null;
        }
        return now()->diffInDays($this->fecha_estimada_cosecha, false);
    }

    public function getProgresoAttribute()
    {
        if ($this->estado === 'Completada') {
            return 100;
        }

        if (!$this->fecha_estimada_cosecha) {
            return 0;
        }

        $totalDias = $this->fecha_siembra->diffInDays($this->fecha_estimada_cosecha);
        if ($totalDias <= 0) {
            return 0;
        }

        $progreso = ($this->dias_transcurridos * 100) / $totalDias;
        return min(max(round($progreso), 0), 100);
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'Activa' => 'success',
            'Completada' => 'info',
            'Problema' => 'danger',
            'Cancelada' => 'secondary',
            default => 'primary'
        };
    }
}
