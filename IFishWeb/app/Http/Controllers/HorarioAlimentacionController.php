<?php

namespace App\Http\Controllers;
use App\Models\HorarioAlimentacion;
use Illuminate\Http\Request;
use App\Models\Dispensador;
use App\Models\TipoComida;
use Illuminate\Support\Facades\Auth;
class HorarioAlimentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Esta es nuestra consulta más avanzada hasta ahora.
        // Con with(), le decimos a Eloquent que cargue de antemano las relaciones
        // que vamos a necesitar. Esto evita cientos de consultas a la base de datos
        // y hace que la página cargue muchísimo más rápido.
        $horarios = HorarioAlimentacion::with(['dispensador', 'tipoComida', 'creadoPor'])
                                    ->orderBy('hora_programada', 'asc')
                                    ->paginate(15);

        // Retornamos la vista y le pasamos los datos.
        return view('public.horarios.index', compact('horarios'));
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
        // 1. VALIDACIÓN
        $request->validate([
            'id_dispensador' => 'required|integer|exists:Dispensadores,id_dispensador',
            'id_tipo_comida' => 'required|integer|exists:Tipos_Comida,id_tipo_comida',
            'hora_programada' => 'required|date_format:H:i', // Valida formato HH:MM
            'cantidad_gramos' => 'required|integer|min:1', // Debe ser al menos 1 gramo
        ]);

        // 2. CREACIÓN DEL HORARIO
        HorarioAlimentacion::create([
            'id_dispensador' => $request->id_dispensador,
            'id_tipo_comida' => $request->id_tipo_comida,
            'hora_programada' => $request->hora_programada,
            'cantidad_gramos' => $request->cantidad_gramos,
            'creado_por_usuario' => Auth::id(), // Asignamos el usuario logueado
            'activo' => true, // Por defecto, un nuevo horario está activo
        ]);

        // 3. REDIRECCIÓN
        return redirect()->route('horarios.index')->with('success', '¡Horario de alimentación creado exitosamente!');
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
