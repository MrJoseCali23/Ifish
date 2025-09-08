<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Criadero;
use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use App\Models\RegistroAlimentacion;
use App\Models\Estanque;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = []; // Aquí guardaremos todas las variables para la vista

        if ($user->rol === 'Admin') {
            // --- LÓGICA PARA EL SUPER ADMIN ---

            // Tarjeta: Número Total de Criaderos Activos
            $data['totalCriaderosActivos'] = Criadero::where('estado', 'Activo')->count();

            // Tarjeta: Salud de la Plataforma (% Online)
            $totalDispensadores = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)->count();
            $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->where('ultimo_reporte', '>=', Carbon::now()->subDay())
                ->count();
            $data['saludPlataforma'] = ($totalDispensadores > 0) ? round(($dispensadoresOnline / $totalDispensadores) * 100) : 100;

            // Tarjeta: Total de Alimentaciones (Hoy)
            $data['totalAlimentacionesHoy'] = RegistroAlimentacion::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->whereDate('created_at', Carbon::today())->count();
            
            // Gráfico: Crecimiento de la Plataforma (Criaderos creados por mes)
            $crecimiento = Criadero::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), DB::raw('count(*) as total'))
                ->groupBy('mes')->orderBy('mes', 'asc')->get();
            $data['labelsCrecimiento'] = $crecimiento->pluck('mes');
            $data['dataCrecimiento'] = $crecimiento->pluck('total');

        } else { // Si es 'Dueño'
            // --- LÓGICA PARA EL DUEÑO DE CRIADERO ---
            // Gracias al Global Scope, todas estas consultas ya están filtradas para su criadero.

            // Tarjeta: Dispensador a Rellenar
            $data['dispensadorARellenar'] = Dispensador::orderBy('nivel_comida_actual_kg', 'asc')->first();

            // Tarjeta: Plan Cumplido Hoy (%)
            $totalProgramadoHoyKg = HorarioAlimentacion::where('activo', true)->sum('cantidad_gramos') / 1000;
            $totalDispensadoProgramadoHoy = RegistroAlimentacion::where('tipo_alimentacion', 'Programada')
                ->whereDate('created_at', Carbon::today())->sum('cantidad_dispensada_gramos') / 1000;
            $data['planCumplidoHoy'] = ($totalProgramadoHoyKg > 0) ? round(($totalDispensadoProgramadoHoy / $totalProgramadoHoyKg) * 100) : 100;
            
            // Gráfico: Consumo de Alimento por Día (con filtro de fechas)
            $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : Carbon::now();
            $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $fechaFin->copy()->subWeek();
            
            $consumoDiario = RegistroAlimentacion::query()
                ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(cantidad_dispensada_gramos) as total_gramos'))
                ->whereBetween('created_at', [$fechaInicio, $fechaFin->endOfDay()])
                ->groupBy('fecha')->orderBy('fecha', 'asc')->get();
            
            $data['labelsConsumo'] = $consumoDiario->map(fn($item) => Carbon::parse($item->fecha)->format('d/m'));
            $data['dataConsumo'] = $consumoDiario->map(fn($item) => $item->total_gramos / 1000);
            $data['fechaInicio'] = $fechaInicio->toDateString();
            $data['fechaFin'] = $fechaFin->toDateString();
        }

        // Pasamos el rol y el array de datos a la vista
        return view('public.estadisticas.index', [
            'rol' => $user->rol,
            'data' => $data
        ]);
    }
}