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
use Illuminate\Support\Facades\DB;

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

        // --- Validación de fechas ---
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'criadero_id' => 'nullable|string',
            'estanque_id' => 'nullable|integer|exists:Estanques,id_estanque',
        ], [
            'fecha_fin.after_or_equal' => '⚠️ La fecha final no puede ser anterior a la fecha inicial.',
        ]);

        // --- Fechas ---
        $fechaMaxima = Carbon::now();
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : $fechaMaxima;
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : $fechaMaxima->copy()->subWeek();
        if ($fechaFin->isFuture()) $fechaFin = $fechaMaxima;

        // --- Criaderos y filtros ---
        $criaderosDelDueño = $user->criaderos()->orderBy('nombre')->get();
        $criaderoIdsDelDueño = $criaderosDelDueño->pluck('id');
        $criaderoSeleccionadoId = $request->input('criadero_id', session('active_criadero_id'));
        $criaderoIdsParaFiltrar = $criaderoIdsDelDueño;

        if ($criaderoSeleccionadoId && $criaderoSeleccionadoId !== 'todos' && $criaderoIdsDelDueño->contains($criaderoSeleccionadoId)) {
            $criaderoIdsParaFiltrar = [$criaderoSeleccionadoId];
        }

        // --- Estanques y dispensadores ---
        $estanques = Estanque::whereIn('criadero_id', $criaderoIdsParaFiltrar)->orderBy('nombre_estanque')->get();
        $dispensadorIds = Dispensador::whereIn('criadero_id', $criaderoIdsParaFiltrar)->pluck('id_dispensador');

        // --- Consulta principal ---
        $query = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
            ->with(['dispensador.estanque.criadero', 'tipoComida', 'iniciadoPor'])
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()]);

        if ($request->filled('estanque_id')) {
            $query->whereHas('dispensador.estanque', fn($q) => $q->where('id_estanque', $request->estanque_id));
        }

        $registros = $query->latest()->paginate(25)->withQueryString();

        // 🟡 Mensaje de advertencia cuando no hay resultados
        $mensajeAdvertencia = null;

        if ($registros->isEmpty()) {
            $primerRegistro = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->oldest('created_at')->first();

            $ultimoRegistro = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->latest('created_at')->first();

            $mensajeAdvertencia = '⚠️ No se encontraron registros de alimentación en el rango seleccionado.';

            if ($primerRegistro && $ultimoRegistro) {
                $mensajeAdvertencia .= "\nLos registros disponibles van desde el " .
                    $primerRegistro->created_at->format('d/m/Y') . " hasta el " .
                    $ultimoRegistro->created_at->format('d/m/Y') . ".";
            } elseif ($ultimoRegistro) {
                $mensajeAdvertencia .= "\nEl último registro disponible fue el " .
                    $ultimoRegistro->created_at->format('d/m/Y') . ".";
            } else {
                $mensajeAdvertencia .= "\nNo existen registros históricos todavía.";
            }
        }

        // 🔹 Renderizado normal (con o sin advertencia)
        return view('public.reportes.historial_alimentacion', [
            'registros' => $registros,
            'estanques' => $estanques,
            'criaderosDelDueño' => $criaderosDelDueño,
            'request' => $request,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
            'mensajeAdvertencia' => $mensajeAdvertencia, // 👈 este valor activa el alert
        ]);
    }

/**
 * Muestra una tabla simple con todos los criaderos para el reporte.
 */
    public function reporteCriaderosTabla()
    {
        // Buscamos todos los criaderos con sus dueños
        $criaderos = Criadero::with('owner')->whereHas('owner')->get();

        // Agrupamos por nombre del dueño
        $criaderosPorDueño = $criaderos->groupBy('owner.name');

        // Contadores globales
        $total = $criaderos->count();
        $activos = $criaderos->where('estado', 'Activo')->count();
        $suspendidos = $criaderos->where('estado', 'Suspendido')->count();
        $inactivos = $criaderos->whereNotIn('estado', ['Activo', 'Suspendido'])->count();

        // Mensaje dinámico
        $mensajeAdvertencia = null;
        if ($total === 0) {
            $mensajeAdvertencia = "No hay criaderos registrados en la plataforma.";
        } elseif ($activos === $total) {
            $mensajeAdvertencia = "✅ Todos los criaderos están activos y funcionando.";
        } elseif ($inactivos > 0) {
            $mensajeAdvertencia = "⚠️ Existen criaderos inactivos o en mantenimiento.";
        } elseif ($suspendidos > 0) {
            $mensajeAdvertencia = "🚨 Hay criaderos suspendidos que requieren revisión.";
        }

        return view('public.reportes.criaderos_tabla', compact(
            'criaderosPorDueño', 'total', 'activos', 'suspendidos', 'inactivos', 'mensajeAdvertencia'
        ));
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
        abort_if(Auth::user()->rol !== 'Admin', 403, 'Acción no autorizada.');

        $umbralDesconexion = Carbon::now()->subHours(24);

        // Dispensadores offline y online
        $dispensadoresOffline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
            ->where(function ($q) use ($umbralDesconexion) {
                $q->where('ultimo_reporte', '<', $umbralDesconexion)
                ->orWhereNull('ultimo_reporte');
            })
            ->with('criadero')
            ->get();

        $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
            ->where('ultimo_reporte', '>=', $umbralDesconexion)
            ->with('criadero')
            ->get();

        // Calcular porcentajes
        $total = $dispensadoresOnline->count() + $dispensadoresOffline->count();
        $porcentajeOnline = $total > 0 ? round(($dispensadoresOnline->count() / $total) * 100, 1) : 0;

        // Mensaje dinámico
        $mensajeAdvertencia = null;
        if ($total === 0) {
            $mensajeAdvertencia = "No se encontraron dispensadores registrados en la plataforma.";
        } elseif ($dispensadoresOffline->isEmpty()) {
            $mensajeAdvertencia = "✅ Todos los dispensadores están conectados y funcionando correctamente.";
        } elseif ($dispensadoresOnline->isEmpty()) {
            $mensajeAdvertencia = "🚨 Todos los dispensadores están desconectados. Verifique la red o los dispositivos.";
        } else {
            $mensajeAdvertencia = "Algunos dispensadores presentan problemas de conexión.";
        }

        return view('public.reportes.salud_plataforma', compact(
            'dispensadoresOffline',
            'dispensadoresOnline',
            'porcentajeOnline',
            'total',
            'mensajeAdvertencia'
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
        abort_if(Auth::user()->rol !== 'Admin', 403, 'Acción no autorizada.');

        // ✅ Validación de fechas
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ], [
            'fecha_fin.after_or_equal' => '⚠️ La fecha final no puede ser anterior a la inicial.',
        ]);

        // 📅 Fechas por defecto
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now();
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)
            : $fechaFin->copy()->subMonth();

        // Limitar a hoy si se pone futuro
        if ($fechaFin->isFuture()) {
            $fechaFin = Carbon::now();
        }

        // 🔍 Filtros básicos
        $query = \App\Models\DispensadorEvento::with(['dispensador', 'usuario']);

        if ($request->filled('dispensador_id')) {
            $query->where('dispensador_id', $request->dispensador_id);
        }

        $query->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()]);

        // 📊 Ejecutamos la consulta
        $eventos = $query->latest()->paginate(20)->withQueryString();

        // 📡 Todos los dispensadores (para el filtro)
        $dispensadores = \App\Models\Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
            ->orderBy('modelo')->get();

        // ⚠️ Mensaje de advertencia si no hay registros
        $mensajeAdvertencia = null;

        if ($eventos->isEmpty()) {
            $primerEvento = \App\Models\DispensadorEvento::oldest('created_at')->first();
            $ultimoEvento = \App\Models\DispensadorEvento::latest('created_at')->first();

            $mensajeAdvertencia = "⚠️ No se encontraron eventos en el rango seleccionado.";

            if ($primerEvento && $ultimoEvento) {
                $mensajeAdvertencia .= "\nLos registros disponibles van desde el " .
                    $primerEvento->created_at->format('d/m/Y') . " hasta el " .
                    $ultimoEvento->created_at->format('d/m/Y') . ".";
            } elseif ($ultimoEvento) {
                $mensajeAdvertencia .= "\nEl último registro disponible fue el " .
                    $ultimoEvento->created_at->format('d/m/Y') . ".";
            } else {
                $mensajeAdvertencia .= "\nNo existen registros históricos todavía.";
            }
        }

        return view('public.reportes.historial_dispensador', [
            'eventos' => $eventos,
            'dispensadores' => $dispensadores,
            'request' => $request,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
            'mensajeAdvertencia' => $mensajeAdvertencia,
        ]);
    }

    public function reporteConsumoComida(Request $request)
    {
        // --- Validación de fechas ---
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ], [
            'fecha_fin.after_or_equal' => '⚠️ La fecha final no puede ser anterior a la inicial.',
        ]);

        // --- Rango de fechas actual ---
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : Carbon::now();
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))
            : $fechaFin->copy()->subMonth();

        // --- Rango anterior para comparar ---
        $duracion = $fechaInicio->diffInDays($fechaFin);
        $inicioAnterior = $fechaInicio->copy()->subDays($duracion + 1);
        $finAnterior = $fechaInicio->copy()->subDay();

        $criaderoActivoId = session('active_criadero_id');
        $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');

        // --- Datos actuales ---
        $consumoActual = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
            ->join('Tipos_Comida', 'Registros_Alimentacion.id_tipo_comida', '=', 'Tipos_Comida.id_tipo_comida')
            ->whereBetween('Registros_Alimentacion.created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->select('Tipos_Comida.nombre_comida', DB::raw('SUM(cantidad_dispensada_gramos) as total_consumido'))
            ->groupBy('Tipos_Comida.nombre_comida')
            ->orderBy('total_consumido', 'desc')
            ->get();

        // --- Datos del periodo anterior ---
        $consumoAnterior = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
            ->join('Tipos_Comida', 'Registros_Alimentacion.id_tipo_comida', '=', 'Tipos_Comida.id_tipo_comida')
            ->whereBetween('Registros_Alimentacion.created_at', [$inicioAnterior->startOfDay(), $finAnterior->endOfDay()])
            ->select('Tipos_Comida.nombre_comida', DB::raw('SUM(cantidad_dispensada_gramos) as total_consumido'))
            ->groupBy('Tipos_Comida.nombre_comida')
            ->get();

        // --- Calcular variación porcentual ---
        $variacion = 0;
        $totalActual = $consumoActual->sum('total_consumido');
        $totalAnterior = $consumoAnterior->sum('total_consumido');

        if ($totalAnterior > 0) {
            $variacion = round((($totalActual - $totalAnterior) / $totalAnterior) * 100, 1);
        }

        // --- Mensaje dinámico ---
        $mensajeAdvertencia = null;
        if ($consumoActual->isEmpty()) {
            $ultimo = RegistroAlimentacion::latest('created_at')->first();
            if ($ultimo) {
                $mensajeAdvertencia = "⚠️ No se encontraron registros en el rango seleccionado. El último registro disponible fue el "
                    . $ultimo->created_at->format('d/m/Y') . ".";
            } else {
                $mensajeAdvertencia = "⚠️ No existen registros de alimentación aún en este criadero.";
            }
        }

        // --- Preparar datos para el gráfico ---
        $labelsGrafico = $consumoActual->pluck('nombre_comida');
        $dataGrafico = $consumoActual->pluck('total_consumido');

        return view('public.reportes.consumo_comida', [
            'consumoPorTipo' => $consumoActual,
            'labelsGrafico' => $labelsGrafico,
            'dataGrafico' => $dataGrafico,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
            'variacion' => $variacion,
            'mensajeAdvertencia' => $mensajeAdvertencia,
            'totalActual' => $totalActual,
            'totalAnterior' => $totalAnterior,
        ]);
    }

    public function reporteConsumoComidaPdf(Request $request)
    {
        // 1. Reutilizamos la misma lógica de filtrado
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : Carbon::now();
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $fechaFin->copy()->subMonth();

        $criaderoActivoId = session('active_criadero_id');
        $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');

        $consumoPorTipo = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
            ->join('Tipos_Comida', 'Registros_Alimentacion.id_tipo_comida', '=', 'Tipos_Comida.id_tipo_comida')
            ->whereBetween('Registros_Alimentacion.created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->select('Tipos_Comida.nombre_comida', DB::raw('SUM(cantidad_dispensada_gramos) as total_consumido'))
            ->groupBy('Tipos_Comida.nombre_comida')
            ->orderBy('total_consumido', 'desc')
            ->get();

        $fechaReporte = Carbon::now()->format('d/m/Y');

        // 2. Cargamos la vista especial para el PDF
        $pdf = Pdf::loadView('public.reportes.pdf.consumo_comida', compact('consumoPorTipo', 'fechaReporte', 'fechaInicio', 'fechaFin'));

        // 3. Devolvemos el PDF para que se descargue
        return $pdf->download('reporte-consumo-comida-' . date('Y-m-d') . '.pdf');
    }
}