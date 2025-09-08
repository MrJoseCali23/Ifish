<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HorarioAlimentacion;
use App\Models\Dispensador;
use App\Scopes\CriaderoScope;

class Estanque extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN BÁSICA ---
    protected $table = 'Estanques';
    protected $primaryKey = 'id_estanque';

    // Laravel gestionará created_at y updated_at automáticamente
    // porque SÍ tenemos estas columnas en la migración.

    protected $fillable = [
        'nombre_estanque',
        'ubicacion',
        'dimensiones_metros',
        'creado_por_usuario',
        'actualizado_por_usuario',
        'tasa_alimentacion_porcentaje',
        'criadero_id',
    ];
    public function criadero()
    {
        return $this->belongsTo(Criadero::class, 'criadero_id');
    }
    // --- DEFINICIÓN DE RELACIONES ---

    /**
     * Define la relación "pertenece a" con el Usuario que lo creó.
     * Un Estanque pertenece a un Usuario.
     */
    public function creadoPor()
    {
        // El primer argumento es el Modelo relacionado (User).
        // El segundo es el nombre de la clave foránea en ESTA tabla (Estanques).
        return $this->belongsTo(User::class, 'creado_por_usuario');
    }

    /**
     * Define la relación "pertenece a" con el Usuario que lo actualizó.
     */
    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por_usuario');
    }
    public function horarios()
    {
        return $this->hasManyThrough(
            HorarioAlimentacion::class, // El modelo final al que queremos llegar
            Dispensador::class,         // El modelo intermedio
            'id_estanque',              // Clave foránea en la tabla intermedia (dispensadores)
            'id_dispensador',           // Clave foránea en la tabla final (horarios)
            'id_estanque',              // Clave local en esta tabla (estanques)
            'id_dispensador'            // Clave local en la tabla intermedia (dispensadores)
        );
    }
    protected static function booted()
    {
        static::addGlobalScope(new CriaderoScope);
    }
    public function dispensadores()
    {
        return $this->hasMany(Dispensador::class, 'id_estanque', 'id_estanque');
    }
}