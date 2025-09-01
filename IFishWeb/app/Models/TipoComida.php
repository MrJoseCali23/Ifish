<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CriaderoScope;

class TipoComida extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     */
    protected $table = 'Tipos_Comida';

    /**
     * La clave primaria asociada con la tabla.
     */
    protected $primaryKey = 'id_tipo_comida';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * Esta tabla no los tiene en su diseño original.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre_comida',
        'descripcion',
        'proveedor',
        'nombre_comida',
        'descripcion',
        'proveedor',
        'criadero_id',
    ];
    public function criadero()
    {
        return $this->belongsTo(Criadero::class, 'criadero_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new CriaderoScope);
    }
}