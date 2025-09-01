<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use App\Models\TipoComida;
use App\Models\Estanque;
use App\Models\RegistroAlimentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DispensadorController extends Controller
{
    /**
     * Aplica la política de permisos a todos los métodos de este controlador.
     */
    public function __construct()
    {
        // Esto le dice a Laravel que antes de ejecutar cualquier método (index, edit, update, etc.),
        // debe verificar los permisos en el archivo DispensadorPolicy que creamos.
        $this->authorizeResource(Dispensador::class, 'dispensadore');
    }

    /**
     * Muestra la lista de dispensadores del criadero del usuario.
     */
    public function index()
    {
        // El GlobalScope y la Policy ya se encargan de filtrar,
        // así que el Dueño/Trabajador solo verá los dispensadores de su criadero.
        $dispensadores = Dispensador::with(['estanque', 'horarios'])
                                ->withCount(['horarios', 'registrosAlimentacion'])
                                ->latest('id_dispensador')
                                ->paginate(10);

        $tipos_comida = TipoComida::orderBy('nombre_comida')->get();

        return view('public.dispensadores.index', compact('dispensadores', 'tipos_comida'));
    }

    /**
     * NOTA: Los métodos 'create' y 'store' han sido eliminados de este controlador.
     * La creación de nuevos dispensadores ahora es una tarea exclusiva del Super Admin
     * y se gestiona desde el 'DispensadorInventarioController'.
     */

    /**
     * Muestra el formulario para editar un dispensador.
     */
    public function edit(Dispensador $dispensadore)
    {
        // authorizeResource ya verificó el permiso de 'update'
        $estanques = Estanque::all(); // El Global Scope filtra para mostrar solo los de su criadero
        return view('public.dispensadores.edit', compact('dispensadore', 'estanques'));
    }

    /**
     * Actualiza la información de un dispensador.
     */
    public function update(Request $request, Dispensador $dispensadore)
    {
        // Verificamos si el usuario tiene permiso para actualizar
        $this->authorize('update', $dispensadore);

        // 1. --- VALIDACIÓN SIMPLIFICADA ---
        // Solo validamos los campos que el usuario PUEDE cambiar.
        $datosValidados = $request->validate([
            'id_estanque' => ['required', 'integer', \Illuminate\Validation\Rule::exists('Estanques', 'id_estanque')->where('criadero_id', Auth::user()->criadero_id)],
            'estado' => ['required', 'in:Activo,Inactivo,Error'],
        ]);
        
        // 2. --- ACTUALIZACIÓN PRECISA ---
        // Actualizamos únicamente los campos permitidos.
        $dispensadore->update($datosValidados);

        return redirect()->route('dispensadores.index')->with('success', 'Dispensador actualizado exitosamente.');
}

    /**
     * Elimina un dispensador.
     */
    public function destroy(Dispensador $dispensadore)
    {
        // authorizeResource ya verificó el permiso de 'delete'
        $horariosAsociados = $dispensadore->horarios()->count();
        $registrosAsociados = $dispensadore->registrosAlimentacion()->count();
        
        $dispensadore->delete();
        
        $mensaje = "¡Dispensador eliminado exitosamente!";
        if ($horariosAsociados > 0 || $registrosAsociados > 0) {
            $mensaje .= " Se eliminaron también {$horariosAsociados} horarios y {$registrosAsociados} registros de su historial.";
        }

        return redirect()->route('dispensadores.index')->with('success', $mensaje);
    }
    
    /**
     * Ejecuta una alimentación manual.
     */
    public function manualFeed(Request $request, Dispensador $dispensadore)
    {
        // La política de permisos se verifica con un nombre de método personalizado.
        $this->authorize('manualFeed', $dispensadore);

        $request->validate([
            'id_tipo_comida' => ['required', 'integer', Rule::exists('Tipos_Comida', 'id_tipo_comida')->where('criadero_id', Auth::user()->criadero_id)],
            'cantidad_dispensada_gramos' => 'required|integer|min:1',
        ]);

        RegistroAlimentacion::create([
            'id_dispensador' => $dispensadore->id_dispensador,
            'id_tipo_comida' => $request->id_tipo_comida,
            'iniciado_por_usuario' => Auth::id(),
            'cantidad_dispensada_gramos' => $request->cantidad_dispensada_gramos,
            'tipo_alimentacion' => 'Manual',
            'exitoso' => true,
        ]);

        $dispensadore->comando_pendiente = 'dispensar';
        $dispensadore->comando_valor = $request->input('cantidad_dispensada_gramos');
        $dispensadore->save();

        return redirect()->route('dispensadores.index')->with('success', '¡Orden de alimentación manual enviada al dispensador!');
    }
}
