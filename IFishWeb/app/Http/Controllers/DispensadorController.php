<?php

namespace App\Http\Controllers;
use App\Models\Dispensador;
use App\Models\Estanque;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DispensadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Usamos with('estanque') para cargar la relación eficientemente (Eager Loading).
        $dispensadores = Dispensador::with('estanque')->latest('id_dispensador')->paginate(10);

        // Retornamos la vista y le pasamos la colección de dispensadores.
        return view('public.dispensadores.index', compact('dispensadores'));
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

    public function destroy(Dispensador $dispensadore) // <-- CAMBIO CLAVE
    {
        // Ahora $dispensadore SÍ será el objeto correcto con todos sus datos.
        $dispensadore->delete();

        return redirect()->route('dispensadores.index')
                        ->with('success', '¡Dispensador eliminado exitosamente!');
    }
}
