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

class DispensadorInventarioController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos los posibles valores para los filtros
        $estados = ['Activo', 'Inactivo', 'Error'];
        // La corrección
        $criaderos = Criadero::where('estado', '!=', 'Archivado')->get();

        // --- Filtramos los Dispensadores Asignados ---
        $queryAsignados = Dispensador::withoutGlobalScope(CriaderoScope::class)
                                ->has('criadero')
                                ->with('criadero');
        $queryAsignados = Dispensador::withoutGlobalScope(CriaderoScope::class)
                            ->whereHas('criadero', function ($q) {
                                $q->where('estado', '!=', 'Archivado');
                            })
                            ->with('criadero');

        // Aplicamos filtro de estado si existe
        if ($request->filled('filter_estado')) {
            $queryAsignados->where('estado', $request->filter_estado);
        }
        // Aplicamos filtro de criadero si existe
        if ($request->filled('filter_criadero')) {
            $queryAsignados->where('criadero_id', $request->filter_criadero);
        }

        // Obtenemos los resultados y los agrupamos por el nombre del criadero
        $dispensadoresAsignados = $queryAsignados->get()->groupBy('criadero.nombre');


        // --- Filtramos los Dispensadores Disponibles ---
        $queryDisponibles = Dispensador::withoutGlobalScope(CriaderoScope::class)
                                    ->whereNull('criadero_id');
        
        // Aplicamos filtro de estado si existe
        if ($request->filled('filter_estado')) {
            $queryDisponibles->where('estado', $request->filter_estado);
        }

        $dispensadoresDisponibles = $queryDisponibles->get();

        // Pasamos todos los datos a la vista
        return view('superadmin.dispensadores.index', [
            'dispensadoresAsignados' => $dispensadoresAsignados,
            'dispensadoresDisponibles' => $dispensadoresDisponibles,
            'estados' => $estados,
            'criaderos' => $criaderos,
            'filters' => $request->only(['filter_estado', 'filter_criadero']) // Para recordar la selección del filtro
        ]);
    }
    public function create()
    {
        return view('superadmin.dispensadores.create');
    }

    /**
     * Guarda un nuevo dispensador en la base de datos (sin asignar a un criadero).
     */
    public function store(Request $request)
    {
        $request->validate([
            'mac_address' => ['required', 'string', 'mac_address', 'unique:Dispensadores,mac_address'],
            'modelo' => ['required', 'string', 'max:50'],
            'estado' => ['required', 'in:Activo,Inactivo,Error'],
        ]);

        Dispensador::create([
            'mac_address' => $request->mac_address,
            'modelo' => $request->modelo,
            'estado' => $request->estado,
            // criadero_id se queda en NULL por defecto, ya que es un nuevo dispositivo en inventario.
        ]);

        return redirect()->route('superadmin.dispensadores-inventario.index')
                         ->with('success', 'Dispensador añadido al inventario exitosamente.');
    }
    public function edit(Dispensador $dispensadores_inventario)
    {
        $dispensador = $dispensadores_inventario;
        // Buscamos todos los criaderos para poder asignarlo
        // La corrección
        $criaderos = Criadero::where('estado', '!=', 'Archivado')->get();

        return view('superadmin.dispensadores.edit', compact('dispensador', 'criaderos'));
    }

    public function update(Request $request, Dispensador $dispensadores_inventario)
    {
        $dispensador = $dispensadores_inventario;
        // Guardamos el ID del criadero ANTES de la actualización para poder comparar
        $criadero_anterior_id = $dispensador->criadero_id;

        $request->validate([
            'mac_address' => ['required', 'string', 'mac_address', \Illuminate\Validation\Rule::unique('Dispensadores')->ignore($dispensador->id_dispensador, 'id_dispensador')],
            'modelo' => ['required', 'string', 'max:50'],
            'estado' => ['required', 'in:Activo,Inactivo,Error'],
            'criadero_id' => ['nullable', 'integer', 'exists:criaderos,id']
        ]);

        // Actualizamos el dispensador como antes
        $dispensador->update($request->all());

        // ▼▼▼ LÓGICA AÑADIDA PARA EL HISTORIAL ▼▼▼
        // Comparamos si la asignación del criadero ha cambiado
        if ($criadero_anterior_id != $request->criadero_id) {
            $descripcion = '';
            $tipo_evento = '';

            if (is_null($request->criadero_id)) {
                // Si el nuevo ID es nulo, se ha desasignado
                $tipo_evento = 'desasignacion_criadero';
                $descripcion = "Dispensador desasignado y devuelto al inventario.";
            } else {
                // Si tiene un nuevo ID, se ha asignado o reasignado
                $nuevoCriadero = Criadero::find($request->criadero_id);
                $tipo_evento = 'asignacion_criadero';
                $descripcion = "Dispensador asignado (o reasignado) al criadero '{$nuevoCriadero->nombre}'.";
            }

            // Creamos el evento en la bitácora
            DispensadorEvento::create([
                'dispensador_id' => $dispensador->id_dispensador,
                'tipo_evento' => $tipo_evento,
                'descripcion' => $descripcion,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('superadmin.dispensadores-inventario.index')
                        ->with('success', 'Dispensador actualizado exitosamente.');
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
