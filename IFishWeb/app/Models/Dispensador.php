<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HorarioAlimentacion;
class Dispensador extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN BÁSICA ---
    protected $table = 'Dispensadores';
    protected $primaryKey = 'id_dispensador';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * Según tu diseño de base de datos, esta tabla no los tiene.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     * @var array
     */
    protected $fillable = [
        'id_estanque',
        'mac_address',
        'modelo',
        'estado',
        'nivel_comida_actual_kg',
        'ultimo_reporte',
    ];

    // --- DEFINICIÓN DE RELACIONES ---

    /**
     * Define la relación "pertenece a" con el Estanque.
     * Un Dispensador pertenece a un Estanque.
     */
    public function estanque()
    {
        // El primer argumento es el Modelo relacionado (Estanque).
        // El segundo es el nombre de la clave foránea en ESTA tabla (Dispensadores).
        // El tercero es el nombre de la clave primaria en la OTRA tabla (Estanques).
        return $this->belongsTo(Estanque::class, 'id_estanque', 'id_estanque');
    }
    public function horarios()
    {
        // El primer argumento es el Modelo relacionado.
        // El segundo es el nombre de la clave foránea en la tabla de horarios.
        // El tercero es la clave primaria de ESTA tabla (Dispensadores).
        return $this->hasMany(HorarioAlimentacion::class, 'id_dispensador', 'id_dispensador');
    }
}