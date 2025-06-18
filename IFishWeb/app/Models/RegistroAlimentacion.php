<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAlimentacion extends Model
{
    use HasFactory;

    protected $table = 'Registros_Alimentacion';
    protected $primaryKey = 'id_registro';

    // Ya no especificamos los nombres de las columnas de fecha,
    // por lo que Laravel usará created_at y updated_at por defecto.

    protected $fillable = [
        'id_dispensador',
        'id_tipo_comida',
        'iniciado_por_usuario',
        'cantidad_dispensada_gramos',
        'tipo_alimentacion',
        'exitoso',
    ];

    // --- RELACIONES ---

    public function dispensador()
    {
        return $this->belongsTo(Dispensador::class, 'id_dispensador', 'id_dispensador');
    }

    public function tipoComida()
    {
        return $this->belongsTo(TipoComida::class, 'id_tipo_comida', 'id_tipo_comida');
    }

    public function iniciadoPor()
    {
        return $this->belongsTo(User::class, 'iniciado_por_usuario', 'id');
    }
}