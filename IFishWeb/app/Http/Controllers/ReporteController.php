<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroAlimentacion;
use App\Models\Estanque;
use Illuminate\Support\Facades\Auth;
use App\Models\Criadero;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon;
use App\Models\Dispensador;
class ReporteController extends Controller
{
    /**
     * Muestra el menú principal de reportes.
     */

    
    /**
     * Muestra el formulario y la tabla del historial de alimentación.
     */
    public function historialAlimentacionForm(Request $request)
    {
        $user = Auth::user();

        // --- VALIDACIÓN Y MANEJO DE FECHAS ---
        $request->validate(['fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio']);
        $fechaMaxima = Carbon::now();
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : $fechaMaxima->copy();
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $fechaMaxima->copy()->subWeek();
        if ($fechaFin->isFuture()) { $fechaFin = $fechaMaxima->copy(); }

        // ▼▼▼ AQUÍ ESTÁ LA LÓGICA QUE FALTABA ▼▼▼
        // 1. Obtenemos la lista de criaderos que pertenecen al dueño para el filtro
        $criaderosDelDueño = $user->criaderos()->orderBy('nombre')->get();
        $criaderoIdsDelDueño = $criaderosDelDueño->pluck('id');
        // ▲▲▲ FIN DE LA LÓGICA QUE FALTABA ▲▲▲

        // 2. Determinamos qué criadero(s) mostrar
        $criaderoSeleccionadoId = $request->input('criadero_id', session('active_criadero_id'));
        $criaderoIdsParaFiltrar = $criaderoIdsDelDueño; // Por defecto, todos los suyos

        if ($criaderoSeleccionadoId && $criaderoSeleccionadoId !== 'todos') {
            // Si se seleccionó uno específico (y es suyo), usamos solo ese
            if ($criaderoIdsDelDueño->contains($criaderoSeleccionadoId)) {
                $criaderoIdsParaFiltrar = [$criaderoSeleccionadoId];
            }
        }
        
        // 3. Obtenemos los dispensadores que pertenecen a los criaderos seleccionados
        $dispensadorIds = Dispensador::whereIn('criadero_id', $criaderoIdsParaFiltrar)->pluck('id_dispensador');

        // 4. Construimos la consulta principal
        $query = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                    ->with(['dispensador.estanque.criadero', 'tipoComida', 'iniciadoPor']);

        $query->whereDate('created_at', '>=', $fechaInicio);
        $query->whereDate('created_at', '<=', $fechaFin);

        if ($request->filled('estanque_id')) {
            $query->whereHas('dispensador.estanque', fn($q) => $q->where('id_estanque', $request->estanque_id));
        }
        
        $registros = $query->latest()->paginate(25)->withQueryString();
        
        $estanques = Estanque::whereIn('criadero_id', $criaderoIdsParaFiltrar)->orderBy('nombre_estanque')->get();

        return view('public.reportes.historial_alimentacion', [
            'registros' => $registros,
            'estanques' => $estanques,
            'criaderosDelDueño' => $criaderosDelDueño, // <-- Pasamos la variable a la vista
            'request' => $request,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ]);
    }
/**
 * Muestra una tabla simple con todos los criaderos para el reporte.
 */
    public function reporteCriaderosTabla()
    {
        // 1. Buscamos todos los criaderos y cargamos la información de su dueño
        $criaderos = Criadero::with('owner')->whereHas('owner')->get();

        // 2. ▼▼▼ LA MAGIA ESTÁ AQUÍ ▼▼▼
        // Usamos el método groupBy de las colecciones de Laravel para agruparlos.
        $criaderosPorDueño = $criaderos->groupBy('owner.name');

        // 3. Pasamos la nueva colección agrupada a la vista
        return view('public.reportes.criaderos_tabla', compact('criaderosPorDueño'));
    }
    public function reporteCriaderosPdf()
    {
        $criaderos = Criadero::with('owner')->get();
        $fecha = Carbon::now()->format('d/m/Y');
        $pdf = Pdf::loadView('public.reportes.pdf.criaderos', compact('criaderos', 'fecha'));
        return $pdf->download('reporte-criaderos-' . date('Y-m-d') . '.pdf');
    }
    public function index()
    {
        $reportesDisponibles = [];
        $rolUsuario = Auth::user()->rol;

        // ▼▼▼ NUEVO CÁLCULO AÑADIDO ▼▼▼
        // Contamos todos los criaderos para la tarjeta de resumen.
        // Esta variable solo se usará si el usuario es Super Admin.
        $totalCriaderos = 0;
        if ($rolUsuario === 'Admin') {
            $totalCriaderos = Criadero::count();
        }

        if ($rolUsuario === 'Admin') {
            // Preparamos los datos para los reportes del Super Admin
            $reportesDisponibles = [
                [
                    'titulo' => 'Lista de Criaderos',
                    'descripcion' => 'Genera un reporte en PDF con todos los criaderos registrados, sus dueños y estados.',
                    'icono' => 'bi-building',
                    'ruta' => route('reportes.criaderos.pdf'),
                    'es_pdf' => true,
                ],
                // Aquí podríamos añadir más reportes para el Super Admin en el futuro
            ];
        } 
        elseif ($rolUsuario === 'Dueño') {
            // Preparamos los datos para los reportes del Dueño de Criadero
            $reportesDisponibles = [
                [
                    'titulo' => 'Historial de Alimentación',
                    'descripcion' => 'Consulta la bitácora detallada de todas las alimentaciones, con filtros por fecha y estanque.',
                    'icono' => 'bi-file-earmark-text-fill',
                    'ruta' => route('reportes.historial_alimentacion.form'),
                    'es_pdf' => false,
                ],
                // Aquí podríamos añadir el historial de dispensadores para el Dueño
            ];
        }

        // ▼▼▼ ACTUALIZAMOS EL COMPACT PARA INCLUIR LA NUEVA VARIABLE ▼▼▼
        return view('public.reportes.index', compact('reportesDisponibles', 'totalCriaderos'));
    }
    public function reporteSaludPlataforma()
    {
        // Solo el Super Admin puede ver este reporte.
        // Usamos una Policy o un Gate para esto es lo ideal, pero por ahora un simple `abort_if` funciona.
        abort_if(Auth::user()->rol !== 'Admin', 403, 'Acción no autorizada.');

        // Definimos el umbral: consideramos "offline" a un dispensador que no reporta hace más de 24 horas.
        $umbralDesconexion = Carbon::now()->subHours(24);

        // Buscamos los dispensadores con problemas (offline)
        $dispensadoresOffline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                ->where('ultimo_reporte', '<', $umbralDesconexion)
                                ->orWhereNull('ultimo_reporte')
                                ->with('criadero')
                                ->get();

        // Buscamos los dispensadores que están funcionando bien (online)
        $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                ->where('ultimo_reporte', '>=', $umbralDesconexion)
                                ->with('criadero')
                                ->get();


        return view('public.reportes.salud_plataforma', compact(
            'dispensadoresOffline',
            'dispensadoresOnline'
        ));
    }
     public function historialAlimentacionPdf(Request $request)
    {
        // ▼▼▼ LÓGICA DE FILTRADO Y CONSULTA AÑADIDA AQUÍ ▼▼▼
        $user = Auth::user();
        $criaderoIdsDelDueño = $user->criaderos()->pluck('id');
        
        $criaderoSeleccionadoId = $request->input('criadero_id', session('active_criadero_id'));
        $criaderoIdsParaFiltrar = $criaderoIdsDelDueño;

        if ($criaderoSeleccionadoId && $criaderoSeleccionadoId !== 'todos') {
            if ($criaderoIdsDelDueño->contains($criaderoSeleccionadoId)) {
                $criaderoIdsParaFiltrar = [$criaderoSeleccionadoId];
            }
        }
        
        $dispensadorIds = Dispensador::whereIn('criadero_id', $criaderoIdsParaFiltrar)->pluck('id_dispensador');

        $query = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                    ->with(['dispensador.estanque.criadero', 'tipoComida', 'iniciadoPor']);

        if ($request->filled('fecha_inicio')) { $query->whereDate('created_at', '>=', $request->fecha_inicio); }
        if ($request->filled('fecha_fin')) { $query->whereDate('created_at', '<=', $request->fecha_fin); }
        if ($request->filled('estanque_id')) { $query->whereHas('dispensador.estanque', fn($q) => $q->where('id_estanque', $request->estanque_id)); }
        
        // Obtenemos TODOS los registros que coinciden, sin paginación
        $registros = $query->latest()->get();
        $fechaReporte = Carbon::now()->format('d/m/Y');
        
        // Variable para saber si debemos mostrar la columna 'Criadero'
        $mostrarCriadero = ($request->criadero_id === 'todos');

        // Cargamos la vista del PDF y le pasamos los datos
        $pdf = Pdf::loadView('public.reportes.pdf.historial_alimentacion', compact('registros', 'fechaReporte', 'mostrarCriadero'));

        // Devolvemos el PDF para que se descargue
        return $pdf->download('historial-alimentacion-' . date('Y-m-d') . '.pdf');
    }
    // app/Http/Controllers/ReporteController.php

/**
 * Muestra el reporte con el historial de eventos de los dispensadores.
 */
    public function reporteHistorialDispensador(Request $request)
    {
        // Solo el Super Admin puede ver este reporte.
        abort_if(Auth::user()->rol !== 'Admin', 403, 'Acción no autorizada.');

        $query = \App\Models\DispensadorEvento::with(['dispensador', 'usuario']);

        // --- Aplicamos los filtros ---
        if ($request->filled('dispensador_id')) {
            $query->where('dispensador_id', $request->dispensador_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $eventos = $query->latest()->paginate(20)->withQueryString();

        // Obtenemos todos los dispensadores para el menú del filtro
        $dispensadores = \App\Models\Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)->orderBy('modelo')->get();

        return view('public.reportes.historial_dispensador', compact('eventos', 'dispensadores', 'request'));
    }
}