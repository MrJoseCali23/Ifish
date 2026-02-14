<?php

namespace App\Http\Controllers;

use App\Models\Estanque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EstanqueController extends Controller
{
    /**
     * Aplica la política de permisos a todos los métodos del recurso.
     */
    public function __construct()
    {
        $this->authorizeResource(Estanque::class, 'estanque');
    }

    /**
     * Muestra la lista de estanques DEL CRIADERO ACTIVO.
     */
    public function index()
    {
        // 1. Obtenemos el ID del criadero activo desde la sesión.
        $criaderoActivoId = session('active_criadero_id');

        // 2. Buscamos solo los estanques que pertenecen a ESE criadero.
        $estanques = Estanque::where('criadero_id', $criaderoActivoId)
                           ->withCount('dispensadores')
                           ->latest('id_estanque')
                           ->paginate(10);
                           
        return view('public.estanques.index', compact('estanques'));
    }

    /**
     * Muestra el formulario para crear un nuevo estanque.
     */
    public function create()
    {
        return view('public.estanques.create');
    }

    /**
     * Guarda un nuevo estanque en la base de datos, asignándolo al criadero activo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_estanque' => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:255',
            'dimensiones_metros' => 'nullable|string|max:50',
        ]);

        Estanque::create([
            'nombre_estanque' => $request->nombre_estanque,
            'ubicacion' => $request->ubicacion,
            'dimensiones_metros' => $request->dimensiones_metros,
            'criadero_id' => session('active_criadero_id'), 
            'creado_por_usuario' => Auth::id(),
            'actualizado_por_usuario' => Auth::id(),
        ]);

        return redirect()->route('estanques.index')->with('success', '¡Estanque creado exitosamente!');
    }

    /**
     * Muestra el formulario para editar un estanque.
     */
    public function edit(Estanque $estanque)
    {
        return view('public.estanques.edit', compact('estanque'));
    }

    /**
     * Actualiza un estanque en la base de datos.
     */
    public function update(Request $request, Estanque $estanque)
    {
        $request->validate([
            'nombre_estanque' => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:255',
            'dimensiones_metros' => 'nullable|string|max:50',
        ]);

        $estanque->update([
            'nombre_estanque' => $request->nombre_estanque,
            'ubicacion' => $request->ubicacion,
            'dimensiones_metros' => $request->dimensiones_metros,
            'actualizado_por_usuario' => Auth::id(),
        ]);

        return redirect()->route('estanques.index')->with('success', 'Estanque actualizado exitosamente.');
    }

    /**
     * Elimina un estanque de la base de datos.
     */
    public function destroy(Estanque $estanque)
    {
        $estanque->delete();
        return redirect()->route('estanques.index')->with('success', 'Estanque eliminado exitosamente.');
    }
}