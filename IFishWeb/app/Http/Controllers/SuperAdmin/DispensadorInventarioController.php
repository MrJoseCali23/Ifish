<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Dispensador;
use App\Scopes\CriaderoScope; // <-- Importante
use Illuminate\Http\Request;
use App\Models\Criadero;
use Illuminate\Validation\Rule;
use App\Models\DispensadorEvento;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TipoComida;

class DispensadorInventarioController extends Controller
{
     public function index(Request $request)
    {
        // Obtenemos los posibles valores para los filtros
        $estados = ['Activo', 'Inactivo', 'Error'];
        $criaderos = Criadero::where('estado', '!=', 'Archivado')->get();

        // --- Buscamos los Dispensadores Asignados ---
        $queryAsignados = Dispensador::withoutGlobalScope(CriaderoScope::class)
                                ->whereHas('criadero', function ($q) {
                                    $q->where('estado', '!=', 'Archivado');
                                })
                                ->with('criadero.owner');

        if ($request->filled('filter_estado')) { $queryAsignados->where('estado', $request->filter_estado); }
        if ($request->filled('filter_criadero')) { $queryAsignados->where('criadero_id', $request->filter_criadero); }
        $dispensadoresPorDueño = $queryAsignados->get()->groupBy('criadero.owner.name');

        // ▼▼▼ LÓGICA DE BÚSQUEDA MEJORADA Y A PRUEBA DE FALLOS ▼▼▼
        // --- Buscamos los Dispensadores Disponibles ---
        $queryDisponibles = Dispensador::withoutGlobalScopes() // Desactiva TODOS los scopes globales
                                ->whereNull('criadero_id');
        
        if ($request->filled('filter_estado')) { 
            $queryDisponibles->where('estado', $request->filter_estado); 
        }
        $dispensadoresDisponibles = $queryDisponibles->get();

        // Pasamos todos los datos a la vista
        return view('superadmin.dispensadores.index', [
            'dispensadoresPorDueño' => $dispensadoresPorDueño,
            'dispensadoresDisponibles' => $dispensadoresDisponibles,
            'estados' => $estados,
            'criaderos' => $criaderos,
            'filters' => $request->only(['filter_estado', 'filter_criadero'])
        ]);
    }
    public function create()
    {
        return view('superadmin.dispensadores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'mac_address' => ['required', 'string', 'mac_address', 'unique:Dispensadores,mac_address'],
            'modelo' => ['required', 'string', 'max:50'],
            'estado' => ['required', 'in:Activo,Inactivo,Error'],
        ]);

        // ▼▼▼ LÓGICA DE CREACIÓN DEFINITIVA Y ROBUSTA ▼▼▼
        // Usamos el método ::create() y pasamos explícitamente todos los valores.
        Dispensador::create([
            'mac_address' => $request->mac_address,
            'modelo' => $request->modelo,
            'estado' => $request->estado,
            'criadero_id' => null, // Aseguramos que se guarde como nulo
        ]);
        // ▲▲▲ FIN DE LA LÓGICA DEFINITIVA ▲▲▲

        return redirect()->route('superadmin.dispensadores-inventario.index')
                         ->with('success', 'Dispensador añadido al inventario exitosamente.');
    }
    public function edit(Dispensador $dispensadores_inventario)
    {
        $dispensador = $dispensadores_inventario;
        
        // ▼▼▼ AQUÍ ESTÁ LA LÓGICA QUE FALTABA ▼▼▼
        // Buscamos todos los dueños que tengan al menos un criadero y cargamos esos criaderos.
        $dueñosConCriaderos = User::where('rol', 'Dueño')
                                  ->whereHas('criaderos')
                                  ->with('criaderos')
                                  ->orderBy('name')
                                  ->get();
        
        // También obtenemos los tipos de comida para el otro menú desplegable.
        $tiposComida = TipoComida::orderBy('nombre_comida')->get();

        return view('superadmin.dispensadores.edit', compact('dispensador', 'dueñosConCriaderos', 'tiposComida'));
    }


    public function update(Request $request, Dispensador $dispensadores_inventario)
{
    $dispensador = $dispensadores_inventario;
    // ... (lógica para registrar evento de asignación) ...

    $request->validate([
        'mac_address' => ['required', 'string', 'mac_address', Rule::unique('Dispensadores')->ignore($dispensador->id_dispensador, 'id_dispensador')],
        'modelo' => ['required', 'string', 'max:50'],
        'estado' => ['required', 'in:Activo,Inactivo,Error'],
        'criadero_id' => ['nullable', 'integer', 'exists:criaderos,id'],
        // La validación para 'current_tipo_comida_id' se elimina.
    ]);
    
    // Usamos 'except' para asegurarnos de no guardar el tipo de comida desde aquí.
    $dispensador->update($request->except('current_tipo_comida_id'));

    // ... (resto de la lógica) ...
    return redirect()->route('superadmin.dispensadores-inventario.index')->with('success', 'Dispensador actualizado exitosamente.');
}

    public function destroy(Dispensador $dispensadores_inventario)
    {
        $dispensadores_inventario->delete();
        return redirect()->route('superadmin.dispensadores-inventario.index')
                        ->with('success', 'Dispensador eliminado permanentemente del inventario.');
    }
    public function indexArchivados()
    {
        // Buscamos dispensadores cuyo criadero esté archivado
        $dispensadoresArchivados = Dispensador::withoutGlobalScope(CriaderoScope::class)
                                    ->whereHas('criadero', function ($q) {
                                        $q->where('estado', 'Archivado');
                                    })
                                    ->with('criadero')
                                    ->paginate(15);

        return view('superadmin.dispensadores.archivados', compact('dispensadoresArchivados'));
    }
}
