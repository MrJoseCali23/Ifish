<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $criaderoActivoId = session('active_criadero_id');

        $datosValidados = $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'modo_creacion' => 'required|in:manual,automatico',
        ]);

        $dispensador = Dispensador::where('id_dispensador', $datosValidados['id_dispensador'])
            ->where('criadero_id', $criaderoActivoId)
            ->firstOrFail();

        if (is_null($dispensador->current_tipo_comida_id)) {
            return back()->with('error', 'El dispensador no tiene un tipo de comida asignado.')->withInput();
        }

        if ($request->modo_creacion === 'manual') {

            $request->validate([
                'hora_programada' => 'required|date_format:H:i',
                'cantidad_gramos' => 'required|integer|min:1|max:10000',
            ]);

            $hora = $request->input('hora_programada');
            $cantidad = $request->input('cantidad_gramos');

            // Evitar duplicados
            $duplicado = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                ->where('hora_programada', $hora)
                ->exists();
            if ($duplicado) {
                return back()->with('error', 'Ya existe un horario programado a esa hora para este dispensador.')->withInput();
            }

            // ⏱️ Validar intervalo mínimo (en minutos)
            $horaSeleccionada = Carbon::createFromFormat('H:i', $hora);
            $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                ->get()
                ->contains(function ($h) use ($horaSeleccionada) {
                    $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                    return $diferenciaMin < ($this->minIntervaloHoras * 60);
                });

            if ($conflicto) {
                return back()->with('error', 'Debe haber al menos ' . $this->minIntervaloHoras . ' horas entre horarios para este dispensador.')->withInput();
            }

            // No permitir horas pasadas
            if (now()->format('H:i') > $hora) {
                return back()->with('error', 'No puedes programar una hora que ya pasó hoy.')->withInput();
            }

            // Validar cantidad
            if (($cantidad / 1000) > $dispensador->nivel_comida_actual_kg) {
                return back()->with('error', 'La cantidad supera el nivel actual del dispensador.')->withInput();
            }

            HorarioAlimentacion::create([
                'id_dispensador' => $dispensador->id_dispensador,
                'id_tipo_comida' => $dispensador->current_tipo_comida_id,
                'cantidad_gramos' => $cantidad,
                'hora_programada' => $hora,
                'creado_por_usuario' => Auth::id(),
                'activo' => true,
            ]);
        } else {
            // ⚙️ MODO AUTOMÁTICO
            $request->validate([
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'frecuencia' => 'required|integer|min:1|max:24',
                'cantidad_gramos' => 'required|integer|min:1|max:10000',
            ]);

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

                $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                    ->get()
                    ->contains(function ($h) use ($horaSeleccionada) {
                        $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                        return $diferenciaMin < ($this->minIntervaloHoras * 60);
                    });

                if ($conflicto) continue;

                $existe = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
                    ->where('hora_programada', $hora)
                    ->exists();

                if (!$existe) {
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
        }

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
        $criaderoActivoId = session('active_criadero_id');

        $datosValidados = $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'hora_programada' => 'required|date_format:H:i',
            'cantidad_gramos' => 'required|integer|min:1|max:10000',
            'activo' => 'sometimes|boolean'
        ]);

        $dispensador = Dispensador::where('id_dispensador', $datosValidados['id_dispensador'])
            ->where('criadero_id', $criaderoActivoId)
            ->firstOrFail();

        $nuevaHora = $datosValidados['hora_programada'];
        $nuevaCantidad = $datosValidados['cantidad_gramos'];

        // 🔹 Evitar duplicados
        $duplicado = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
            ->where('hora_programada', $nuevaHora)
            ->where('id_horario', '!=', $horario->id_horario)
            ->exists();

        if ($duplicado) {
            return back()->with('error', 'Ya existe otro horario con esa hora.')->withInput();
        }

        // ⏱️ Validar intervalo mínimo
        $horaSeleccionada = Carbon::createFromFormat('H:i', $nuevaHora);
        $conflicto = HorarioAlimentacion::where('id_dispensador', $dispensador->id_dispensador)
            ->where('id_horario', '!=', $horario->id_horario)
            ->get()
            ->contains(function ($h) use ($horaSeleccionada) {
                $diferenciaMin = abs(Carbon::parse($h->hora_programada)->diffInMinutes($horaSeleccionada));
                return $diferenciaMin < ($this->minIntervaloHoras * 60);
            });

        if ($conflicto) {
            return back()->with('error', 'Debe haber al menos ' . $this->minIntervaloHoras . ' horas entre horarios.')->withInput();
        }

        // ⏰ Validar hora pasada
        if (now()->format('H:i') > $nuevaHora) {
            return back()->with('error', 'No puedes programar una hora que ya pasó hoy.')->withInput();
        }

        // 🍽️ Validar cantidad
        if (($nuevaCantidad / 1000) > $dispensador->nivel_comida_actual_kg) {
            return back()->with('error', 'La cantidad supera el nivel actual del dispensador.')->withInput();
        }

        $horario->update([
            'id_dispensador' => $dispensador->id_dispensador,
            'id_tipo_comida' => $dispensador->current_tipo_comida_id,
            'cantidad_gramos' => $nuevaCantidad,
            'hora_programada' => $nuevaHora,
            'activo' => $request->has('activo'),
        ]);

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
