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
        // --- 1. VALIDACIÓN BÁSICA ---
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.'
        ]);

        // --- 2. LÓGICA DE LÍMITES DE FECHA ---
        $primerRegistro = RegistroAlimentacion::orderBy('created_at', 'asc')->first();
        // La fecha más antigua que se puede buscar es la del primer registro, o hoy si no hay registros.
        $fechaMinima = $primerRegistro ? Carbon::parse($primerRegistro->created_at) : Carbon::now();
        // La fecha más reciente que se puede buscar es hoy.
        $fechaMaxima = Carbon::now();

        // Obtenemos las fechas del request o usamos valores por defecto (última semana)
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : $fechaMaxima->copy();
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $fechaMaxima->copy()->subWeek();

        // 3. CORRECCIÓN AUTOMÁTICA DE FECHAS ---
        // Si el usuario elige una fecha futura, la ajustamos a hoy.
        if ($fechaFin->isFuture()) {
            $fechaFin = $fechaMaxima->copy();
        }
        // Si el usuario elige una fecha muy antigua, la ajustamos a la del primer registro.
        if ($fechaInicio->lessThan($fechaMinima)) {
            $fechaInicio = $fechaMinima->copy();
        }


        // --- 4. LA CONSULTA (Usa las fechas ya validadas y corregidas) ---
        $query = RegistroAlimentacion::query();
        $query->with(['dispensador.estanque', 'tipoComida', 'iniciadoPor']);
        
        $query->whereDate('created_at', '>=', $fechaInicio);
        $query->whereDate('created_at', '<=', $fechaFin);

        if ($request->filled('estanque_id')) {
            $query->whereHas('dispensador.estanque', function ($q) use ($request) {
                $q->where('id_estanque', $request->estanque_id);
            });
        }

        $registros = $query->latest()->paginate(25)->withQueryString();
        $estanques = Estanque::orderBy('nombre_estanque')->get();

        return view('public.reportes.historial_alimentacion', [
            'registros' => $registros,
            'estanques' => $estanques,
            'request' => $request,
            // Pasamos las fechas corregidas a la vista para que los campos del formulario se actualicen
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ]);
    }
/**
 * Muestra una tabla simple con todos los criaderos para el reporte.
 */
    public function reporteCriaderosTabla()
    {
        $criaderos = Criadero::with('owner')->get();
        return view('public.reportes.criaderos_tabla', compact('criaderos'));
    }
    public function reporteCriaderosPdf()
    {
        // 1. Obtenemos los datos que queremos mostrar en el reporte
        $criaderos = Criadero::with('owner')->get();
        $fecha = \Carbon\Carbon::now()->format('d/m/Y');

        // 2. Cargamos una vista especial para el PDF y le pasamos los datos
        $pdf = PDF::loadView('public.reportes.pdf.criaderos', compact('criaderos', 'fecha'));

        // 3. Devolvemos el PDF para que se descargue en el navegador
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
        // 1. Reutilizamos EXACTAMENTE la misma lógica de filtrado que en la página normal
        $query = RegistroAlimentacion::query()
                    ->with(['dispensador.estanque', 'tipoComida', 'iniciadoPor']);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('estanque_id')) {
            $query->whereHas('dispensador.estanque', function ($q) use ($request) {
                $q->where('id_estanque', $request->estanque_id);
            });
        }

        // Obtenemos TODOS los registros que coinciden, sin paginación
        $registros = $query->latest()->get();
        $fechaReporte = Carbon::now()->format('d/m/Y');

        // 2. Cargamos una vista especial para el PDF y le pasamos los datos
        $pdf = Pdf::loadView('public.reportes.pdf.historial_alimentacion', compact('registros', 'fechaReporte'));

        // 3. Devolvemos el PDF para que se descargue en el navegador
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