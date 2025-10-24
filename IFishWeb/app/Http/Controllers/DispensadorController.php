<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use App\Models\Estanque;
use App\Models\RegistroAlimentacion;
use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DispensadorController extends Controller
{
    /**
     * Aplica la política de permisos a todos los métodos del recurso.
     */
    public function __construct()
    {
        $this->authorizeResource(Dispensador::class, 'dispensadore');
        
        // 👉 pero excluimos "data" manualmente del chequeo de autorización
        $this->middleware(function ($request, $next) {
            if ($request->routeIs('dispensadores.data')) {
                return $next($request); // saltar autorización
            }
            return $next($request);
        });
    }
    /**
     * Muestra la lista de dispensadores DEL CRIADERO ACTIVO.
     */
    public function index()
    {
        // 1. Obtenemos el ID del criadero activo desde la sesión.
        $criaderoActivoId = session('active_criadero_id');

        // 2. Buscamos solo los dispensadores que pertenecen a ESE criadero.
        $dispensadores = Dispensador::where('criadero_id', $criaderoActivoId)
                                  ->with(['estanque', 'tipoComidaActual'])
                                  ->withCount(['horarios', 'registrosAlimentacion'])
                                  ->latest('id_dispensador')
                                  ->paginate(10);

        return view('public.dispensadores.index', compact('dispensadores'));
    }

    /**
     * Muestra el formulario para que un Dueño gestione un dispensador.
     */
    public function edit(Dispensador $dispensadore)
    {
        $criaderoActivoId = session('active_criadero_id');
        $estanques = Estanque::where('criadero_id', $criaderoActivoId)->orderBy('nombre_estanque')->get();
        $tiposComida = TipoComida::where('criadero_id', $criaderoActivoId)->orWhereNull('criadero_id')->get();
        
        return view('public.dispensadores.edit', compact('dispensadore', 'estanques', 'tiposComida'));
    }

    /**
     * Actualiza la configuración de un dispensador.
     */
    public function update(Request $request, Dispensador $dispensadore)
    {
        $datosValidados = $request->validate([
            'id_estanque' => ['required', 'integer', Rule::exists('Estanques', 'id_estanque')->where('criadero_id', session('active_criadero_id'))],
            'estado' => ['required', 'in:Activo,Inactivo,Error'],
            'current_tipo_comida_id' => ['nullable', 'integer', 'exists:Tipos_Comida,id_tipo_comida'],
        ]);
        
        $dispensadore->update($datosValidados);

        return redirect()->route('dispensadores.index')->with('success', 'Dispensador actualizado exitosamente.');
    }

    /**
     * Elimina un dispensador (acción deshabilitada para Dueños por la Policy).
     */
    public function destroy(Dispensador $dispensadore)
    {
        $dispensadore->delete();
        return redirect()->route('dispensadores.index')->with('success', 'Dispensador eliminado exitosamente.');
    }

    /**
     * Procesa una orden de alimentación manual.
     */
    public function manualFeed(Request $request, Dispensador $dispensadore)
    {
        $this->authorize('manualFeed', $dispensadore);

        if (is_null($dispensadore->current_tipo_comida_id)) {
            return back()->with('error', 'Este dispensador no tiene un tipo de comida asignado.');
        }

        $request->validate(['cantidad_dispensada_gramos' => 'required|integer|min:1|max:10000']);

        if (($request->cantidad_dispensada_gramos / 1000) > $dispensadore->nivel_comida_actual_kg) {
            return back()->with('error', 'La cantidad solicitada supera el nivel de comida actual.');
        }

        RegistroAlimentacion::create([
            'id_dispensador' => $dispensadore->id_dispensador,
            'id_tipo_comida' => $dispensadore->current_tipo_comida_id,
            'iniciado_por_usuario' => Auth::id(),
            'cantidad_dispensada_gramos' => $request->cantidad_dispensada_gramos,
            'tipo_alimentacion' => 'Manual',
            'exitoso' => true,
        ]);

        $dispensadore->comando_pendiente = 'dispensar';
        $dispensadore->comando_valor = $request->input('cantidad_dispensada_gramos');
        $dispensadore->save();

        return redirect()->route('dispensadores.index')->with('success', '¡Orden de alimentación manual enviada!');
    }
    public function data()
    {
        if (!auth()->check()) {
            abort(403, 'No autorizado.');
        }

        $criaderoActivoId = session('active_criadero_id');

        $dispensadores = Dispensador::where('criadero_id', $criaderoActivoId)
            ->select('id_dispensador', 'temperatura_agua', 'nivel_comida_actual_kg', 'ultimo_reporte')
            ->get();

        return response()->json($dispensadores);
    }
}