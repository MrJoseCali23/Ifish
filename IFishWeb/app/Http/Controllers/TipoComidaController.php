<?php

namespace App\Http\Controllers;

use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // <-- ¡ESTA ES LA LÍNEA MÁGICA QUE SOLUCIONA EL ERROR!

class TipoComidaController extends Controller
{
    /**
     * Muestra la lista de tipos de comida.
     */
    public function index()
    {
        $criaderoActivoId = session('active_criadero_id');
        
        // Un Dueño ve las comidas de su criadero activo Y las globales (sin criadero_id).
        $tipos_comida = TipoComida::where('criadero_id', $criaderoActivoId)
                                ->orWhereNull('criadero_id')
                                ->orderBy('nombre_comida', 'asc')
                                ->paginate(10);

        return view('public.tipos_comida.index', compact('tipos_comida'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de comida.
     */
    public function create()
    {
        return view('public.tipos_comida.create');
    }

    /**
     * Guarda un nuevo tipo de comida en la base de datos.
     */
    public function store(Request $request)
    {
        $criaderoActivoId = session('active_criadero_id');
        $request->validate([
            'nombre_comida' => [
                'required', 'string', 'max:100',
                 // La comida debe ser única para este criadero O única entre las globales.
                Rule::unique('Tipos_Comida')->where(function ($query) use ($criaderoActivoId) {
                    return $query->where('criadero_id', $criaderoActivoId)->orWhereNull('criadero_id');
                }),
            ],
            'descripcion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:100',
        ]);

        TipoComida::create([
            'nombre_comida' => $request->nombre_comida,
            'descripcion' => $request->descripcion,
            'proveedor' => $request->proveedor,
            'criadero_id' => $criaderoActivoId, // Asigna automáticamente al criadero activo
        ]);

        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un tipo de comida.
     */
    public function edit(TipoComida $tipo_comida) // Laravel usa el singular del resource name
    {
        return view('public.tipos_comida.edit', compact('tipo_comida'));
    }

    /**
     * Actualiza un tipo de comida en la base de datos.
     */
    public function update(Request $request, TipoComida $tipo_comida)
    {
        $criaderoActivoId = session('active_criadero_id');
        $request->validate([
             'nombre_comida' => [
                'required', 'string', 'max:100',
                Rule::unique('Tipos_Comida', 'nombre_comida')->ignore($tipo_comida->id_tipo_comida, 'id_tipo_comida')->where(function ($query) use ($criaderoActivoId) {
                    return $query->where('criadero_id', $criaderoActivoId)->orWhereNull('criadero_id');
                }),
            ],
            'descripcion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:100',
        ]);

        $tipo_comida->update($request->all());

        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida actualizado exitosamente.');
    }

    /**
     * Elimina un tipo de comida de la base de datos.
     */
    public function destroy(TipoComida $tipo_comida)
    {
        $tipo_comida->delete();
        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida eliminado exitosamente.');
    }
}