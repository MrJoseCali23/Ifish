<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HorarioAlimentacionController extends Controller
{
    /**
     * Muestra los horarios agrupados por dispensador del criadero activo.
     */
    public function index()
    {
        $criaderoActivoId = session('active_criadero_id');

        // Buscamos TODOS los dispensadores del criadero activo que estén asignados a un estanque.
        // Cargamos sus horarios de forma eficiente. La vista se encargará de mostrarlos.
        $dispensadoresDelCriadero = Dispensador::where('criadero_id', $criaderoActivoId)
                                    ->whereNotNull('id_estanque')
                                    ->with(['estanque', 'horarios' => function ($query) {
                                        $query->orderBy('hora_programada', 'asc')->with('tipoComida');
                                    }])
                                    ->get();

        return view('public.horarios.index', compact('dispensadoresDelCriadero'));
    }

    /**
     * Muestra el formulario para crear nuevos horarios.
     */
    public function create()
    {
        $criaderoActivoId = session('active_criadero_id');
        
        // Buscamos solo los dispensadores que están en un estanque Y que ya tienen un tipo de comida asignado.
        $dispensadores = Dispensador::with('estanque', 'tipoComidaActual')
                                ->where('criadero_id', $criaderoActivoId)
                                ->whereNotNull('id_estanque')
                                ->whereNotNull('current_tipo_comida_id') 
                                ->get();

        if ($dispensadores->isEmpty()) {
            return redirect()->route('horarios.index')
                             ->with('error', 'No hay dispensadores listos para programar. Asegúrate de que tus dispensadores estén asignados a un estanque y tengan un tipo de comida cargado.');
        }
        
        // Ya no necesitamos pasar los tipos de comida, cada dispensador sabe el suyo.
        return view('public.horarios.create', compact('dispensadores'));
    }

    /**
     * Guarda uno o más horarios nuevos.
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            // La validación de 'id_tipo_comida' se elimina.
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'cantidad_gramos' => 'required|integer|min:1',
            'modo_creacion' => 'required|in:manual,automatico',
        ]);
        
        // Buscamos el dispensador seleccionado para saber qué comida tiene.
        $dispensadorSeleccionado = Dispensador::find($datosValidados['id_dispensador']);

        if ($request->input('modo_creacion') === 'manual') {
            $request->validate(['hora_programada' => 'required|date_format:H:i']);
            
            HorarioAlimentacion::create([
                'id_dispensador' => $datosValidados['id_dispensador'],
                'id_tipo_comida' => $dispensadorSeleccionado->current_tipo_comida_id, // <-- Usa la comida del dispensador
                'cantidad_gramos' => $datosValidados['cantidad_gramos'],
                'hora_programada' => $request->input('hora_programada'),
                'creado_por_usuario' => Auth::id(),'activo' => true,
            ]);
        } else {
            $datosValidadosAuto = $request->validate([
                'hora_inicio' => 'required|date_format:H:i','hora_fin' => 'required|date_format:H:i|after_or_equal:hora_inicio',
                'frecuencia' => 'required|integer|min:1|max:24',
            ]);

            $frecuencia = (int)$datosValidadosAuto['frecuencia'];
            $horaInicio = Carbon::createFromFormat('H:i', $datosValidadosAuto['hora_inicio']);
            $horaFin = Carbon::createFromFormat('H:i', $datosValidadosAuto['hora_fin']);
            $horasCalculadas = [];

            if ($frecuencia == 1) {
                $horasCalculadas[] = $horaInicio->format('H:i:s');
            } else {
                $duracionTotalMinutos = $horaInicio->diffInMinutes($horaFin);
                $intervaloMinutos = $duracionTotalMinutos > 0 ? $duracionTotalMinutos / ($frecuencia - 1) : 0;
                for ($i = 0; $i < $frecuencia; $i++) {
                    $horasCalculadas[] = $horaInicio->copy()->addMinutes(round($intervaloMinutos * $i))->format('H:i:s');
                }
            }
            
            foreach ($horasCalculadas as $hora) {
                HorarioAlimentacion::create([
                    'id_dispensador' => $datosValidados['id_dispensador'],
                    'id_tipo_comida' => $dispensadorSeleccionado->current_tipo_comida_id, // <-- Usa la comida del dispensador
                    'cantidad_gramos' => $datosValidados['cantidad_gramos'],
                    'hora_programada' => $hora,
                    'creado_por_usuario' => Auth::id(),'activo' => true,
                ]);
            }
        }
        return redirect()->route('horarios.index')->with('success', '¡Horario(s) creado(s) exitosamente!');
    }

    /**
     * Muestra el formulario para editar un horario.
     */
    public function edit(HorarioAlimentacion $horario)
    {
        $criaderoActivoId = session('active_criadero_id');
        $dispensadores = Dispensador::with('estanque', 'tipoComidaActual')
                                ->where('criadero_id', $criaderoActivoId)
                                ->whereNotNull('id_estanque')
                                ->whereNotNull('current_tipo_comida_id')
                                ->get();

        return view('public.horarios.edit', compact('horario', 'dispensadores'));
    }

    /**
     * Actualiza un horario en la base de datos.
     */
    public function update(Request $request, HorarioAlimentacion $horario)
    {
        // La validación ahora es más simple
        $datosValidados = $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'cantidad_gramos' => 'required|integer|min:1',
            'hora_programada' => 'required|date_format:H:i',
            'activo' => 'sometimes|boolean' // 'activo' es opcional
        ]);
        
        // Buscamos la comida del dispensador seleccionado
        $dispensadorSeleccionado = Dispensador::find($datosValidados['id_dispensador']);
        
        // Añadimos el tipo de comida y el estado 'activo' a los datos para actualizar
        $datosValidados['id_tipo_comida'] = $dispensadorSeleccionado->current_tipo_comida_id;
        $datosValidados['activo'] = $request->has('activo');

        $horario->update($datosValidados);

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado exitosamente.');
    }

    /**
     * Elimina un horario.
     */
    public function destroy(HorarioAlimentacion $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado.');
    }
}