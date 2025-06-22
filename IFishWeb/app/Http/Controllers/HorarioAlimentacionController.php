<?php

namespace App\Http\Controllers;
use App\Models\HorarioAlimentacion;
use Illuminate\Http\Request;
use App\Models\Dispensador;
use App\Models\TipoComida;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class HorarioAlimentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtenemos TODOS los dispensadores que tienen al menos UN horario programado.
        // Usamos with() para cargar de antemano todas las relaciones que vamos a necesitar
        // en la vista (estanque, horarios, y el tipo de comida de cada horario).
        // Esto es súper eficiente.
        $dispensadoresConHorarios = Dispensador::whereHas('horarios')
                                    ->with([
                                        'estanque', 
                                        'horarios' => function ($query) {
                                            $query->orderBy('hora_programada', 'asc')->with('tipoComida');
                                        }
                                    ])
                                    ->get();

        // Pasamos esta nueva colección a la vista.
        return view('public.horarios.index', compact('dispensadoresConHorarios'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 1. Obtenemos todos los dispensadores disponibles.
        $dispensadores = Dispensador::all();
        // 2. Obtenemos todos los tipos de comida disponibles.
        $tipos_comida = TipoComida::all();

        // 3. Retornamos la vista y le pasamos ambas colecciones de datos.
        return view('public.horarios.create', compact('dispensadores', 'tipos_comida'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. --- VALIDACIÓN DE CAMPOS COMUNES ---
        // Estos campos son necesarios sin importar el modo.
        $datosValidados = $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'id_tipo_comida' => 'required|integer|exists:Tipos_Comida,id_tipo_comida',
            'cantidad_gramos' => 'required|integer|min:1',
            'modo_creacion' => 'required|in:manual,automatico',
        ]);

        // 2. --- LÓGICA CONDICIONAL SEGÚN EL MODO ---
        if ($request->input('modo_creacion') === 'manual') {

            // --- LÓGICA PARA MODO MANUAL ---
            $request->validate([
                'hora_programada' => 'required|date_format:H:i',
            ]);

            HorarioAlimentacion::create([
                'id_dispensador' => $datosValidados['id_dispensador'],
                'id_tipo_comida' => $datosValidados['id_tipo_comida'],
                'cantidad_gramos' => $datosValidados['cantidad_gramos'],
                'hora_programada' => $request->input('hora_programada'),
                'creado_por_usuario' => Auth::id(),
                'activo' => true,
            ]);

        } else { // if ($request->input('modo_creacion') === 'automatico')

            // --- LÓGICA PARA MODO AUTOMÁTICO ---
            $datosValidadosAuto = $request->validate([
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'frecuencia' => 'required|integer|min:1|max:24', // Límite para no sobrecargar
            ]);

            $frecuencia = (int)$datosValidadosAuto['frecuencia'];
            $horaInicio = Carbon::createFromFormat('H:i', $datosValidadosAuto['hora_inicio']);
            $horaFin = Carbon::createFromFormat('H:i', $datosValidadosAuto['hora_fin']);

            $horasCalculadas = [];

            if ($frecuencia == 1) {
                // Si la frecuencia es 1, solo se usa la hora de inicio.
                $horasCalculadas[] = $horaInicio->format('H:i:s');
            } else {
                // Calculamos la diferencia total en minutos entre la hora de fin y la de inicio.
                $duracionTotalMinutos = $horaInicio->diffInMinutes($horaFin);
                // Calculamos el intervalo en minutos entre cada comida.
                $intervaloMinutos = $duracionTotalMinutos / ($frecuencia - 1);

                // Generamos cada hora
                for ($i = 0; $i < $frecuencia; $i++) {
                    $horasCalculadas[] = $horaInicio->copy()->addMinutes(round($intervaloMinutos * $i))->format('H:i:s');
                }
            }

            // Creamos un registro de horario para cada hora calculada
            foreach ($horasCalculadas as $hora) {
                HorarioAlimentacion::create([
                    'id_dispensador' => $datosValidados['id_dispensador'],
                    'id_tipo_comida' => $datosValidados['id_tipo_comida'],
                    'cantidad_gramos' => $datosValidados['cantidad_gramos'],
                    'hora_programada' => $hora,
                    'creado_por_usuario' => Auth::id(),
                    'activo' => true,
                ]);
            }
        }

        // 3. --- REDIRECCIÓN FINAL ---
        return redirect()->route('horarios.index')->with('success', '¡Horario(s) creado(s) exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HorarioAlimentacion $horario)
    {
        // Necesitamos la lista de todos los dispensadores y comidas para los menús desplegables
        $dispensadores = Dispensador::all();
        $tipos_comida = TipoComida::all();

        return view('public.horarios.edit', compact('horario', 'dispensadores', 'tipos_comida'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HorarioAlimentacion $horario)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'id_tipo_comida' => 'required|integer|exists:Tipos_Comida,id_tipo_comida',
            'hora_programada' => 'required|date_format:H:i,H:i:s', // Acepta HH:MM o HH:MM:SS
            'cantidad_gramos' => 'required|integer|min:1',
            'activo' => 'nullable|boolean', // 'activo' puede no venir en el request si el checkbox está desmarcado
        ]);

        // 2. ACTUALIZACIÓN
        $horario->update([
            'id_dispensador' => $request->id_dispensador,
            'id_tipo_comida' => $request->id_tipo_comida,
            'hora_programada' => $request->hora_programada,
            'cantidad_gramos' => $request->cantidad_gramos,
            // Si el checkbox 'activo' está marcado, se envía '1'. Si no, no se envía nada.
            // Con $request->has('activo') comprobamos si existe.
            'activo' => $request->has('activo'),
        ]);

        // 3. REDIRECCIÓN
        return redirect()->route('horarios.index')->with('success', '¡Horario actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HorarioAlimentacion $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
}
