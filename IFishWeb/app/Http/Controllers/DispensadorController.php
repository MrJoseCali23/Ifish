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
        \Log::info('Iniciando manualFeed', [
            'id_dispensador' => $dispensadore->id_dispensador,
            'request' => $request->all()
        ]);

        $this->authorize('manualFeed', $dispensadore);

        $criaderoActivoId = session('active_criadero_id');
        if (!$criaderoActivoId) {
            \Log::error('No hay criadero activo en la sesión');
            return back()->with('error', 'No se ha seleccionado un criadero activo.')->withInput();
        }

        // Verificar que el dispensador pertenece al criadero activo
        if ($dispensadore->criadero_id !== $criaderoActivoId) {
            \Log::error('Dispensador no pertenece al criadero', [
                'id_dispensador' => $dispensadore->id_dispensador,
                'criadero_id' => $criaderoActivoId
            ]);
            return back()->with('error', 'El dispensador no pertenece al criadero activo.')->withInput();
        }

        if (is_null($dispensadore->current_tipo_comida_id)) {
            \Log::error('Dispensador sin tipo de comida', ['id_dispensador' => $dispensadore->id_dispensador]);
            return back()->with('error', 'El dispensador no tiene un tipo de comida asignado.')->withInput();
        }

        try {
            $datosValidados = $request->validate([
                'cantidad_dispensada_gramos' => [
                    'required',
                    'integer',
                    'min:10',
                    'max:10000',
                    function ($attribute, $value, $fail) {
                        if ($value % 10 !== 0) {
                            $fail('La cantidad debe ser un múltiplo de 10.');
                        }
                    },
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validación fallida', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('error', 'Error en los datos ingresados. Por favor, revisa los campos.');
        }

        $cantidadGramos = $datosValidados['cantidad_dispensada_gramos'];
        if (($cantidadGramos / 1000) > $dispensadore->nivel_comida_actual_kg) {
            \Log::error('Cantidad excede nivel', [
                'cantidad' => $cantidadGramos,
                'nivel' => $dispensadore->nivel_comida_actual_kg
            ]);
            return back()->with('error', 'La cantidad supera el nivel actual del dispensador (' . ($dispensadore->nivel_comida_actual_kg * 1000) . ' g).')->withInput();
        }

        \Log::info('Creando registro de alimentación manual', [
            'id_dispensador' => $dispensadore->id_dispensador,
            'cantidad' => $cantidadGramos
        ]);

        RegistroAlimentacion::create([
            'id_dispensador' => $dispensadore->id_dispensador,
            'id_tipo_comida' => $dispensadore->current_tipo_comida_id,
            'iniciado_por_usuario' => Auth::id(),
            'cantidad_dispensada_gramos' => $cantidadGramos,
            'tipo_alimentacion' => 'Manual',
            'exitoso' => true,
        ]);

        \Log::info('Enviando instrucción de alimentación manual', [
            'id_dispensador' => $dispensadore->id_dispensador,
            'comando' => 'dispensar',
            'cantidad' => $cantidadGramos
        ]);

        $dispensadore->comando_pendiente = 'dispensar';
        $dispensadore->comando_valor = $cantidadGramos;
        $dispensadore->nivel_comida_actual_kg -= $cantidadGramos / 1000;
        $dispensadore->ultimo_reporte = now();
        $dispensadore->save();

        \Log::info('Alimentación manual completada', [
            'id_dispensador' => $dispensadore->id_dispensador,
            'nuevo_nivel' => $dispensadore->nivel_comida_actual_kg
        ]);

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