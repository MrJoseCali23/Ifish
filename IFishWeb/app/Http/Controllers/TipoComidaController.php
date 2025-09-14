<?php

namespace App\Http\Controllers;
use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TipoComidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtenemos todos los tipos de comida, ordenados por nombre
        $tipos_comida = TipoComida::orderBy('nombre_comida', 'asc')->paginate(10);

        // Retornamos la vista y le pasamos los datos
        return view('public.tipos_comida.index', compact('tipos_comida'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('public.tipos_comida.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
        public function store(Request $request)
    {
        // ... tu código de validación ...

        $data = $request->all();
        $data['criadero_id'] = Auth::user()->criadero_id; // <-- LÍNEA CLAVE AÑADIDA

        TipoComida::create($data);

        return redirect()->route('tipos_comida.index')->with('success', '¡Tipo de comida creado exitosamente!');
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
    public function edit(TipoComida $tipos_comida)
    {
        // Usamos Route Model Binding. Laravel nos da el objeto ya encontrado.
        // La variable se llama $tipos_comida porque nuestro recurso es 'tipos_comida'.
        return view('public.tipos_comida.edit', compact('tipos_comida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoComida $tipos_comida)
    {
        // 1. VALIDACIÓN
        $request->validate([
            // La regla 'unique' debe ignorar al registro actual
            'nombre_comida' => 'required|string|max:100|unique:Tipos_Comida,nombre_comida,' . $tipos_comida->id_tipo_comida . ',id_tipo_comida',
            'descripcion' => 'nullable|string',
            'proveedor' => 'nullable|string|max:100',
        ]);

        // 2. ACTUALIZACIÓN
        $tipos_comida->update($request->all());

        // 3. REDIRECCIÓN
        return redirect()->route('tipos_comida.index')->with('success', '¡Tipo de comida actualizado exitosamente!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoComida $tipos_comida)
    {
        $tipos_comida->delete();
        return redirect()->route('tipos_comida.index')->with('success', 'Tipo de comida eliminado exitosamente.');
    }
}
