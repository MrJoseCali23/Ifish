<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criadero extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'user_id',
        'ubicacion',
        'estado',
    ];

    // --- RELACIONES ---

    /**
     * Un criadero pertenece a un usuario (el dueño).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un criadero puede tener muchos usuarios (trabajadores).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'criadero_id');
    }

    /**
     * Un criadero tiene muchos estanques.
     */
    public function estanques()
    {
        return $this->hasMany(Estanque::class, 'criadero_id');
    }

    /**
     * Un criadero tiene muchos dispensadores.
     */
    public function dispensadores()
    {
        return $this->hasMany(Dispensador::class, 'criadero_id');
    }

    /**
     * Un criadero tiene muchos tipos de comida personalizados.
     */
    public function tiposComida()
    {
        return $this->hasMany(TipoComida::class, 'criadero_id');
    }
}