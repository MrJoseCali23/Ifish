<?php

namespace App\Http\Controllers;

use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TipoComidaController extends Controller
{
    /**
     * Muestra la lista de tipos de comida.
     */
    public function index()
    {
        $criaderoActivoId = session('active_criadero_id');
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
                // ▼▼▼ SOLUCIÓN AL BUG #1 (VALIDACIÓN) ▼▼▼
                Rule::unique('Tipos_Comida')->where(fn ($query) => $query->where('criadero_id', $criaderoActivoId)),
            ],
            'descripcion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:100',
        ]);

        TipoComida::create([
            'nombre_comida' => $request->nombre_comida,
            'descripcion' => $request->descripcion,
            'proveedor' => $request->proveedor,
            'criadero_id' => $criaderoActivoId,
        ]);

        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un tipo de comida.
     */
    public function edit($id)
    {
        $tipo_comida = TipoComida::withoutGlobalScope(CriaderoScope::class)->findOrFail($id);
        $user = Auth::user();
        $criaderoIds = $user->rol !== 'Admin' ? $user->criaderos()->pluck('id')->toArray() : null;

        if ($criaderoIds && !in_array($tipo_comida->criadero_id, $criaderoIds) && !is_null($tipo_comida->criadero_id)) {
            return redirect()->route('tipos_comida.index')->with('error', 'No tienes permiso para editar este tipo de comida.');
        }

        \Log::info('TipoComida encontrado:', ['tipo_comida' => $tipo_comida]);
        return view('public.tipos_comida.edit', compact('tipo_comida'));
    }
    // public function edit(TipoComida $tipo_comida)
    // {
    //     // ▼▼▼ SOLUCIÓN AL BUG #2 ▼▼▼
    //     // Simplemente pasamos el modelo que Laravel ya encontró por nosotros.
    //     return view('public.tipos_comida.edit', compact('tipo_comida'));
    // }

    /**
     * Actualiza un tipo de comida en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $tipo_comida = TipoComida::withoutGlobalScope(\App\Models\CriaderoScope::class)->findOrFail($id);
        $user = Auth::user();
        $criaderoIds = $user->rol !== 'Admin' ? $user->criaderos()->pluck('id')->toArray() : null;

        if ($criaderoIds && !in_array($tipo_comida->criadero_id, $criaderoIds) && !is_null($tipo_comida->criadero_id)) {
            return redirect()->route('tipos_comida.index')->with('error', 'No tienes permiso para actualizar este tipo de comida.');
        }

        $validated = $request->validate([
            'nombre_comida' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $tipo_comida->update($validated);
        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida actualizado correctamente.');
    }
    // public function update(Request $request, TipoComida $tipo_comida)
    // {
    //     $criaderoActivoId = session('active_criadero_id');
    //     $request->validate([
    //          'nombre_comida' => [
    //             'required', 'string', 'max:100',
    //             Rule::unique('Tipos_Comida', 'nombre_comida')->ignore($tipo_comida->id_tipo_comida, 'id_tipo_comida')->where(fn ($query) => $query->where('criadero_id', $criaderoActivoId)),
    //         ],
    //         'descripcion' => 'nullable|string',
    //         'proveedor' => 'nullable|string|max:100',
    //     ]);

    //     $tipo_comida->update($request->all());

    //     return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida actualizado exitosamente.');
    // }

    /**
     * Elimina un tipo de comida de la base de datos.
     */
    public function destroy(TipoComida $tipo_comida)
    {
        // ▼▼▼ SOLUCIÓN AL BUG #3 ▼▼▼
        // Comprobamos si la comida está siendo usada en algún horario o registro.
        if ($tipo_comida->horarios()->count() > 0 || $tipo_comida->registrosAlimentacion()->count() > 0) {
            return back()->with('error', 'No se puede eliminar este tipo de comida porque ya está en uso en horarios o en el historial de alimentación.');
        }

        $tipo_comida->delete();
        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida eliminado exitosamente.');
    }
}