<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensadorEvento extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     */
    protected $table = 'dispensador_eventos';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'dispensador_id',
        'tipo_evento',
        'descripcion',
        'user_id',
    ];

    // --- RELACIONES ---

    /**
     * Un evento pertenece a un dispensador.
     */
    public function dispensador()
    {
        return $this->belongsTo(Dispensador::class, 'dispensador_id', 'id_dispensador');
    }

    /**
     * Un evento es generado por un usuario (si aplica).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
