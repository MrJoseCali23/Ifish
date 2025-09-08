<?php

namespace App\Http\Controllers;
use App\Models\Estanque;
use Illuminate\Support\Facades\Auth;
use App\Models\HorarioAlimentacion; 
use App\Models\TipoComida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class EstanqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    // Usamos with('creadoPor') para cargar la relación con el usuario.
    // Esto previene el problema de N+1 queries y es mucho más eficiente.
        $estanques = Estanque::with('creadoPor')->latest()->paginate(10);

    // Retornamos la vista y le pasamos la colección de estanques.
        return view('public.estanques.index', compact('estanques'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    // Solo necesitamos mostrar la vista que contiene el formulario.
    return view('public.estanques.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // app/Http/Controllers/EstanqueController.php

/**
 * Store a newly created resource in storage.
 */
        public function store(Request $request)
    {
        // ... tu código de validación ...
        $this->authorize('create', Estanque::class);
        Estanque::create([
            'nombre_estanque' => $request->nombre_estanque,
            'ubicacion' => $request->ubicacion,
            'dimensiones_metros' => $request->dimensiones_metros ?? 'No especificado',
            'creado_por_usuario' => Auth::id(),
            'actualizado_por_usuario' => Auth::id(),
            'criadero_id' => Auth::user()->criadero_id, // <-- LÍNEA CLAVE AÑADIDA
        ]);

        return redirect()->route('estanques.index')->with('success', '¡Estanque creado exitosamente!');
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
    public function edit(Estanque $estanque)
    {
        // Gracias al Route Model Binding, Laravel ya nos da el estanque.
        // Solo tenemos que pasarlo a la vista.
        return view('public.estanques.edit', compact('estanque'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estanque $estanque)
    {   
        $this->authorize('update', $estanque);
        // 1. VALIDACIÓN DE DATOS
        $request->validate([
            'nombre_estanque' => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:255',
            'dimensiones_metros' => 'nullable|string|max:50',
        ]);

        // 2. PREPARAMOS LOS DATOS A ACTUALIZAR
        $data = $request->only('nombre_estanque', 'ubicacion');

        // Usamos la misma lógica del valor por defecto que en el método store
        $data['dimensiones_metros'] = $request->dimensiones_metros ?? 'No especificado';

        // Actualizamos el ID del usuario que realizó la última modificación
        $data['actualizado_por_usuario'] = Auth::id();

        // 3. ACTUALIZAMOS EL REGISTRO EN LA BASE DE DATOS
        $estanque->update($data);

        // 4. REDIRIGIMOS DE VUELTA A LA LISTA CON UN MENSAJE DE ÉXITO
        return redirect()->route('estanques.index')->with('success', '¡Estanque actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estanque $estanque)
    {   
         $this->authorize('delete', $estanque);
        // 1. ELIMINAMOS EL REGISTRO DE LA BASE DE DATOS
        $estanque->delete();

        // 2. REDIRIGIMOS A LA LISTA CON UN MENSAJE DE ÉXITO
        return redirect()->route('estanques.index')->with('success', 'Estanque eliminado exitosamente.');
    }

}
