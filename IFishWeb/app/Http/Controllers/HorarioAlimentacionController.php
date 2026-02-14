<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class HorarioAlimentacionController extends Controller
{
    private $minIntervaloHoras = 3; // Mínimo de 3 horas entre horarios

    /**
     * Muestra los horarios agrupados por dispensador del criadero activo.
     */
    public function index()
    {
        $criaderoActivoId = session('active_criadero_id');
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
        $dispensadores = Dispensador::with('estanque', 'tipoComidaActual')
            ->where('criadero_id', $criaderoActivoId)
            ->whereNotNull('id_estanque')
            ->whereNotNull('current_tipo_comida_id')
            ->get();
        if ($dispensadores->isEmpty()) {
            return redirect()->route('horarios.index')
                ->with('error', 'No hay dispensadores listos para programar. Asegúrate de que tus dispensadores estén asignados a un estanque y tengan un tipo de comida cargado.');
        }
        return view('public.horarios.create', compact('dispensadores'));
    }

    /**
     * Guarda uno o más horarios nuevos.
     */
    public function store(Request $request)
    {
        Log::info('Iniciando store', $request->all());

        $criaderoActivoId = session('active_criadero_id');
        $datosValidados = $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'modo_creacion' => 'required|in:manual,automatico',
        ]);

        $dispensador = Dispensador::where('id_dispensador', $datosValidados['id_dispensador'])
            ->where('criadero_id', $criaderoActivoId)
            ->firstOrFail();

        if (is_null($dispensador->current_tipo_comida_id)) {
            Log::error('Dispensador sin tipo de comida', ['id_dispensador' => $dispensador->id_dispensador]);
            return back()->with('error', 'El dispensador no tiene un tipo de comida asignado.')->withInput();
        }

        if ($request->modo_creacion === 'manual') {
            $request->validate([
                'hora_programada' => 'required|date_format:H:i',
                'cantidad_gramos' => [
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

            $hora = $request->input('hora_programada');
            $cantidad = $request->input('cantidad_gramos');

            $duplicado = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                ->where('hora_programada', $hora)
                ->exists();
            if ($duplicado) {
                Log::error('Horario duplicado', ['hora' => $hora, 'id_dispensador' => $dispensador->id_dispensador]);
                return back()->with('error', 'Ya existe un horario programado a esa hora para este dispensador.')->withInput();
            }

            $horaSeleccionada = Carbon::createFromFormat('H:i', $hora);
            $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                ->get()
                ->contains(function ($h) use ($horaSeleccionada) {
                    $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                    return $diferenciaMin < ($this->minIntervaloHoras * 60);
                });
            if ($conflicto) {
                Log::error('Conflicto de intervalo', ['hora' => $hora, 'id_dispensador' => $dispensador->id_dispensador]);
                return back()->with('error', 'Debe haber al menos ' . $this->minIntervaloHoras . ' horas entre horarios para este dispensador.')->withInput();
            }

            if (now()->format('H:i') > $hora) {
                Log::error('Hora pasada', ['hora' => $hora]);
                return back()->with('error', 'No puedes programar una hora que ya pasó hoy.')->withInput();
            }

            if (($cantidad / 1000) > $dispensador->nivel_comida_actual_kg) {
                Log::error('Cantidad excede nivel', ['cantidad' => $cantidad, 'nivel' => $dispensador->nivel_comida_actual_kg]);
                return back()->with('error', 'La cantidad supera el nivel actual del dispensador.')->withInput();
            }

            Log::info('Creando horario manual', ['id_dispensador' => $dispensador->id_dispensador, 'cantidad' => $cantidad, 'hora' => $hora]);
            HorarioAlimentacion::create([
                'id_dispensador' => $dispensador->id_dispensador,
                'id_tipo_comida' => $dispensador->current_tipo_comida_id,
                'cantidad_gramos' => $cantidad,
                'hora_programada' => $hora,
                'creado_por_usuario' => Auth::id(),
                'activo' => true,
            ]);
        } else {
            $request->validate([
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'frecuencia' => 'required|integer|min:1|max:24',
                'cantidad_gramos' => [
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

            // Borrar horarios existentes para el dispensador en modo automático
            Log::info('Eliminando horarios existentes', ['id_dispensador' => $dispensador->id_dispensador]);
            HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)->delete();

            $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $horaFin = Carbon::createFromFormat('H:i', $request->hora_fin);
            $frecuencia = (int) $request->frecuencia;
            $duracionMin = $horaInicio->diffInMinutes($horaFin);
            $intervalo = $frecuencia > 1 ? $duracionMin / ($frecuencia - 1) : 0;
            $horas = [];
            for ($i = 0; $i < $frecuencia; $i++) {
                $horas[] = $horaInicio->copy()->addMinutes(round($intervalo * $i))->format('H:i');
            }

            foreach ($horas as $hora) {
                $horaSeleccionada = Carbon::createFromFormat('H:i', $hora);
                // Verificar intervalo mínimo (aunque no debería haber conflictos tras borrar horarios)
                $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                    ->get()
                    ->contains(function ($h) use ($horaSeleccionada) {
                        $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                        return $diferenciaMin < ($this->minIntervaloHoras * 60);
                    });
                if ($conflicto) {
                    Log::warning('Conflicto de intervalo detectado', ['hora' => $hora, 'id_dispensador' => $dispensador->id_dispensador]);
                    continue;
                }

                // Verificar si la cantidad excede el nivel de comida
                if (($request->cantidad_gramos / 1000) > $dispensador->nivel_comida_actual_kg) {
                    Log::error('Cantidad excede nivel', ['cantidad' => $request->cantidad_gramos, 'nivel' => $dispensador->nivel_comida_actual_kg]);
                    return back()->with('error', 'La cantidad supera el nivel actual del dispensador.')->withInput();
                }

                Log::info('Creando horario automático', ['id_dispensador' => $dispensador->id_dispensador, 'cantidad' => $request->cantidad_gramos, 'hora' => $hora]);
                HorarioAlimentacion::create([
                    'id_dispensador' => $dispensador->id_dispensador,
                    'id_tipo_comida' => $dispensador->current_tipo_comida_id,
                    'cantidad_gramos' => $request->cantidad_gramos,
                    'hora_programada' => $hora,
                    'creado_por_usuario' => Auth::id(),
                    'activo' => true,
                ]);
            }
        }

        Log::info('Horario(s) creado(s) exitosamente');
        return redirect()->route('horarios.index')->with('success', 'Horario(s) creado(s) exitosamente.');
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
        \Log::info('Iniciando update', $request->all());

        $criaderoActivoId = session('active_criadero_id');
        if (!$criaderoActivoId) {
            \Log::error('No hay criadero activo en la sesión');
            return back()->with('error', 'No se ha seleccionado un criadero activo.')->withInput();
        }

        try {
            $datosValidados = $request->validate([
                'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
                'hora_programada' => 'required|date_format:H:i',
                'cantidad_gramos' => [
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
                // Eliminamos 'activo' => 'boolean' para manejar manualmente
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validación fallida', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('error', 'Error en los datos ingresados. Por favor, revisa los campos.');
        }

        $dispensador = Dispensador::where('id_dispensador', $datosValidados['id_dispensador'])
            ->where('criadero_id', $criaderoActivoId)
            ->first();
        if (!$dispensador) {
            \Log::error('Dispensador no encontrado o no pertenece al criadero', [
                'id_dispensador' => $datosValidados['id_dispensador'],
                'criadero_id' => $criaderoActivoId
            ]);
            return back()->with('error', 'El dispensador seleccionado no es válido o no pertenece al criadero activo.')->withInput();
        }

        if (is_null($dispensador->current_tipo_comida_id)) {
            \Log::error('Dispensador sin tipo de comida', ['id_dispensador' => $dispensador->id_dispensador]);
            return back()->with('error', 'El dispensador no tiene un tipo de comida asignado.')->withInput();
        }

        $hora = $request->input('hora_programada');
        $cantidad = $request->input('cantidad_gramos');
        $activo = $request->has('activo'); // Convertir "on" o ausencia a true/false

        // Verificar duplicado (excepto el horario actual)
        $duplicado = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
            ->where('hora_programada', $hora)
            ->where('id_horario', '!=', $horario->id_horario)
            ->exists();
        if ($duplicado) {
            \Log::error('Horario duplicado', ['hora' => $hora, 'id_dispensador' => $dispensador->id_dispensador]);
            return back()->with('error', 'Ya existe un horario programado a esa hora para este dispensador.')->withInput();
        }

        // Verificar intervalo mínimo
        $horaSeleccionada = Carbon::createFromFormat('H:i', $hora);
        $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
            ->where('id_horario', '!=', $horario->id_horario)
            ->get()
            ->contains(function ($h) use ($horaSeleccionada) {
                $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                return $diferenciaMin < ($this->minIntervaloHoras * 60);
            });
        if ($conflicto) {
            \Log::error('Conflicto de intervalo', ['hora' => $hora, 'id_dispensador' => $dispensador->id_dispensador]);
            return back()->with('error', 'Debe haber al menos ' . $this->minIntervaloHoras . ' horas entre horarios para este dispensador.')->withInput();
        }

        // Verificar hora futura
        $now = now()->format('H:i');
        if ($now > $hora) {
            \Log::error('Hora pasada', ['hora' => $hora, 'hora_actual' => $now]);
            return back()->with('error', 'No puedes programar una hora que ya pasó hoy.')->withInput();
        }

        // Verificar nivel de comida
        if (($cantidad / 1000) > $dispensador->nivel_comida_actual_kg) {
            \Log::error('Cantidad excede nivel', ['cantidad' => $cantidad, 'nivel' => $dispensador->nivel_comida_actual_kg]);
            return back()->with('error', 'La cantidad supera el nivel actual del dispensador (' . ($dispensador->nivel_comida_actual_kg * 1000) . ' g).')->withInput();
        }

        \Log::info('Actualizando horario', [
            'id_horario' => $horario->id_horario,
            'id_dispensador' => $dispensador->id_dispensador,
            'cantidad' => $cantidad,
            'hora' => $hora,
            'activo' => $activo
        ]);
        $horario->update([
            'id_dispensador' => $dispensador->id_dispensador,
            'id_tipo_comida' => $dispensador->current_tipo_comida_id,
            'cantidad_gramos' => $cantidad,
            'hora_programada' => $hora,
            'activo' => $activo,
        ]);

        \Log::info('Horario actualizado exitosamente', ['id_horario' => $horario->id_horario]);
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