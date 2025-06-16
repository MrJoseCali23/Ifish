<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

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
}