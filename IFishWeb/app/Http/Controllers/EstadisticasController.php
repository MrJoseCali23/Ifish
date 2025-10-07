<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use App\Models\RegistroAlimentacion;
use App\Models\Estanque;
use App\Models\Criadero;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $viewData = []; 
        
        $rol = $user->rol;

        // ===============================================================
        // LÓGICA PARA EL SUPER ADMIN
        // ===============================================================
        if ($rol === 'Admin') {
            
            $viewData['totalCriaderosActivos'] = Criadero::where('estado', 'Activo')->count();

            $totalDispensadores = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)->count();
            $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                        ->where('ultimo_reporte', '>=', Carbon::now()->subDay())
                                        ->count();
            $viewData['saludPlataforma'] = ($totalDispensadores > 0) ? round(($dispensadoresOnline / $totalDispensadores) * 100) : 100;

            $viewData['totalAlimentacionesHoy'] = RegistroAlimentacion::whereDate('created_at', Carbon::today())->count();
            
            $crecimiento = Criadero::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'), DB::raw('count(*) as total'))
                                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                                ->groupBy('mes')
                                ->orderBy('mes', 'asc')
                                ->get();
            $viewData['labelsCrecimiento'] = $crecimiento->pluck('mes');
            $viewData['dataCrecimiento'] = $crecimiento->pluck('total');

            return view('public.estadisticas.index', ['rol' => $rol, 'data' => $viewData]);
        }
        
        // ===============================================================
        // LÓGICA PARA EL DUEÑO DE CRIADERO
        // ===============================================================
        if ($rol === 'Dueño') {
            $criaderoActivoId = session('active_criadero_id');
            $hoy = Carbon::today();

            // Lógica de filtro de fechas
            $request->validate(['fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio']);
            $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : $hoy->copy();
            $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $hoy->copy()->subWeek();
            if ($fechaFin->isFuture()) { $fechaFin = $hoy->copy(); }

            // Obtenemos los IDs de los dispensadores del criadero activo
            $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');

            // Tarjeta: Dispensador a Rellenar
            $viewData['dispensadorARellenar'] = Dispensador::where('criadero_id', $criaderoActivoId)
                                                    ->orderBy('nivel_comida_actual_kg', 'asc')
                                                    ->first();

            // Tarjeta: Plan Cumplido Hoy (%)
            $totalProgramadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)->where('activo', true)->count();
            $totalEjecutadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->whereNotNull('ultima_ejecucion')
                ->whereDate('ultima_ejecucion', $hoy)
                ->count();
            $viewData['planCumplidoHoy'] = ($totalProgramadoHoy > 0) ? round(($totalEjecutadoHoy / $totalProgramadoHoy) * 100) : 100;

            // Gráfico: Consumo de Alimento por Día (con filtro)
            $consumoDiario = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(cantidad_dispensada_gramos) as total_gramos'))
                ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
                ->groupBy('fecha')->orderBy('fecha', 'asc')->get();
            
            $viewData['labelsConsumo'] = $consumoDiario->map(fn($item) => Carbon::parse($item->fecha)->format('d/m'));
            $viewData['dataConsumo'] = $consumoDiario->map(fn($item) => $item->total_gramos / 1000); // En Kg

            // Pasamos las fechas del filtro a la vista
            $viewData['fechaInicio'] = $fechaInicio->toDateString();
            $viewData['fechaFin'] = $fechaFin->toDateString();

            return view('public.estadisticas.index', ['rol' => $rol, 'data' => $viewData]);
        }

        // Si el rol no es ninguno de los anteriores, lo mandamos al dashboard
        return redirect()->route('dashboard');
    }
}