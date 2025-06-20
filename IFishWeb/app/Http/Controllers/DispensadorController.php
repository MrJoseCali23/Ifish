<?php

namespace App\Http\Controllers;
use App\Models\Dispensador;
use App\Models\Estanque;
use App\Models\TipoComida;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\RegistroAlimentacion;
use Illuminate\Support\Facades\Auth;


class DispensadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        // Usamos withCount para obtener solo el NÚMERO de relaciones.
        // Es mucho más eficiente que cargar todos los objetos.
        $dispensadores = Dispensador::with('estanque')
                                ->withCount(['horarios', 'registrosAlimentacion'])
                                ->latest('id_dispensador')
                                ->paginate(10);

        $tipos_comida = TipoComida::orderBy('nombre_comida')->get();

        return view('public.dispensadores.index', compact('dispensadores', 'tipos_comida'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 1. Obtenemos todos los estanques para poder listarlos en un menú desplegable.
        $estanques = Estanque::all();

        // 2. Retornamos la vista y le pasamos la colección de estanques.
        return view('public.dispensadores.create', compact('estanques'));
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
            // 'exists:Estanques,id_estanque' comprueba que el id del estanque realmente exista en la tabla Estanques.
            'id_estanque' => 'required|integer|exists:Estanques,id_estanque',
            // 'mac' valida que el formato sea de una MAC Address. 'unique' para que no se repitan.
            'mac_address' => 'required|string|mac_address|unique:Dispensadores,mac_address',
            'modelo' => 'nullable|string|max:50',
        ]);

        // 2. CREACIÓN DEL DISPENSADOR
        Dispensador::create($request->all());

        // 3. REDIRECCIÓN
        return redirect()->route('dispensadores.index')->with('success', '¡Dispensador registrado exitosamente!');
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
    public function edit(Dispensador $dispensadore) // <-- CAMBIO AQUÍ
    {
        $estanques = Estanque::all();
        // Y pasamos la variable correcta a la vista
        return view('public.dispensadores.edit', compact('dispensadore', 'estanques'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dispensador $dispensadore) // <-- CAMBIO AQUÍ
    {
        $request->validate([
            'id_estanque' => 'required|integer|exists:Estanques,id_estanque',
            'mac_address' => 'required|string|mac_address|unique:Dispensadores,mac_address,' . $dispensadore->id_dispensador . ',id_dispensador',
            'modelo' => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo,Error',
        ]);

        // Usamos la variable correcta para actualizar
        $dispensadore->update($request->all());

        return redirect()->route('dispensadores.index')->with('success', '¡Dispensador actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // app/Http/Controllers/DispensadorController.php

// app/Http/Controllers/DispensadorController.php

    public function destroy(Dispensador $dispensadore)
    {
        // 1. Contamos los registros asociados ANTES de que se borren en cascada.
        // Usamos la relación 'horarios()' que ya definimos en el modelo Dispensador.
        $horariosAsociados = $dispensadore->horarios()->count();
        
        // Para los registros de alimentación, como no definimos la relación inversa, lo contamos así:
        $registrosAsociados = RegistroAlimentacion::where('id_dispensador', $dispensadore->id_dispensador)->count();

        // 2. Borramos el dispensador.
        // La base de datos se encargará de borrar en cascada los horarios y registros.
        $dispensadore->delete();
        
        // 3. Construimos un mensaje de éxito dinámico.
        $mensaje = "¡Dispensador eliminado exitosamente!";

        if ($horariosAsociados > 0 || $registrosAsociados > 0) {
            $mensaje .= " Se eliminaron también {$horariosAsociados} horarios y {$registrosAsociados} registros de su historial.";
        }

        // 4. Redirigimos con el nuevo mensaje personalizado.
        return redirect()->route('dispensadores.index')->with('success', $mensaje);
    }
    public function manualFeed(Request $request, Dispensador $dispensadore)
    {
        // 1. Validamos los datos del formulario del modal
        $request->validate([
            'id_tipo_comida' => 'required|integer|exists:Tipos_Comida,id_tipo_comida',
            'cantidad_dispensada_gramos' => 'required|integer|min:1',
        ]);

        // 2. Creamos el registro en el historial
        RegistroAlimentacion::create([
            'id_dispensador' => $dispensadore->id_dispensador,
            'id_tipo_comida' => $request->id_tipo_comida,
            'iniciado_por_usuario' => Auth::id(),
            'cantidad_dispensada_gramos' => $request->cantidad_dispensada_gramos,
            'tipo_alimentacion' => 'Manual',
            'exitoso' => true,
        ]);

        // 3. Dejamos la "carta en el buzón": establecemos el comando para el ESP
        $dispensadore->comando_pendiente = 'dispensar';
        $dispensadore->comando_valor = $request->input('cantidad_dispensada_gramos');
        $dispensadore->save();

        // 4. Redirigimos con un mensaje de éxito
        return redirect()->route('dispensadores.index')->with('success', '¡Orden de alimentación manual enviada al dispensador!');
    }

}
