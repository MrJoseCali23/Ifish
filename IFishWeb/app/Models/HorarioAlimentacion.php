<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CriaderoScope;
class HorarioAlimentacion extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN BÁSICA ---
    protected $table = 'Horarios_Alimentacion';
    protected $primaryKey = 'id_horario';
    public $timestamps = false; // Esta tabla no tiene timestamps en su diseño

    protected $fillable = [
        'id_dispensador',
        'id_tipo_comida',
        'creado_por_usuario',
        'hora_programada',
        'cantidad_gramos',
        'activo',
        'ultima_ejecucion'
    ];

    // --- DEFINICIÓN DE RELACIONES ---

    /**
     * Un Horario pertenece a un Dispensador.
     */
    public function dispensador()
    {
        return $this->belongsTo(Dispensador::class, 'id_dispensador', 'id_dispensador');
    }

    /**
     * Un Horario utiliza un Tipo de Comida.
     */
    public function tipoComida()
    {
        return $this->belongsTo(TipoComida::class, 'id_tipo_comida', 'id_tipo_comida');
    }

    /**
     * Un Horario es creado por un Usuario.
     */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por_usuario', 'id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new CriaderoScope);
    }
}