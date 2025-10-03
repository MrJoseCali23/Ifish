<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CriaderoScope;

class Dispensador extends Model
{
    use HasFactory;

    protected $table = 'Dispensadores';
    protected $primaryKey = 'id_dispensador';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * Como nuestra tabla no los tiene, lo desactivamos.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'id_estanque',
        'mac_address',
        'modelo',
        'estado',
        'nivel_comida_actual_kg',
        'ultimo_reporte',
        'comando_pendiente',
        'comando_valor',
        'criadero_id',
        'current_tipo_comida_id', // <-- AÑADIDO AQUÍ
    ];

    /**
     * El "booting" method del modelo.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CriaderoScope);
    }

    // --- RELACIONES ---

    public function estanque()
    {
        return $this->belongsTo(Estanque::class, 'id_estanque', 'id_estanque');
    }

    public function criadero()
    {
        return $this->belongsTo(Criadero::class, 'criadero_id');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioAlimentacion::class, 'id_dispensador', 'id_dispensador');
    }

    public function registrosAlimentacion()
    {
        return $this->hasMany(RegistroAlimentacion::class, 'id_dispensador', 'id_dispensador');
    }
    
    /**
     * Obtiene el tipo de comida actualmente cargado en el dispensador.
     *
     * ▼▼▼ NUEVA RELACIÓN AÑADIDA ▼▼▼
     */
    public function tipoComidaActual()
    {
        return $this->belongsTo(TipoComida::class, 'current_tipo_comida_id', 'id_tipo_comida');
    }
}
