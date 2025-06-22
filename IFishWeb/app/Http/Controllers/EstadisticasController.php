<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estanque;
use App\Models\HorarioAlimentacion;
use App\Models\RegistroAlimentacion;
use Carbon\Carbon;

class EstadisticasController extends Controller
{


    // app/Http/Controllers/EstadisticasController.php

    public function index(Request $request)
    {
        // --- VALIDACIÓN Y MANEJO DE FECHAS ---
        $request->validate([
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
        ]);
        $hoy = Carbon::today();
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio')) : $hoy->copy()->subWeek();
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->input('fecha_fin')) : $hoy->copy();
        if ($fechaFin->isFuture()) { $fechaFin = $hoy->copy(); }


        // --- CÁLCULO DE KPIS Y ESTADÍSTICAS ---

        // 1. "Dispensador a Rellenar"
        $dispensadorARellenar = Dispensador::orderBy('nivel_comida_actual_kg', 'asc')->first();

        // 2. "Almacenamiento de los dispensadores"
        $inventarioTotalKg = Dispensador::sum('nivel_comida_actual_kg');
        $totalProgramadoHoyKg = HorarioAlimentacion::where('activo', true)->sum('cantidad_gramos') / 1000;
        $diasRestantes = ($totalProgramadoHoyKg > 0) ? floor($inventarioTotalKg / $totalProgramadoHoyKg) : '∞';

        // 3. "Plan Cumplido Hoy (%)"
        $totalDispensadoProgramadoHoy = RegistroAlimentacion::where('tipo_alimentacion', 'Programada')
            ->whereDate('created_at', $hoy)
            ->sum('cantidad_dispensada_gramos') / 1000;
        $planCumplidoHoy = ($totalProgramadoHoyKg > 0)
                        ? round(($totalDispensadoProgramadoHoy / $totalProgramadoHoyKg) * 100)
                        : 100; // Si no hay nada programado, se cumplió al 100%

        // 4. "Alimentaciones Manuales y Automáticas (Hoy)"
        $alimentacionesHoy = RegistroAlimentacion::whereDate('created_at', $hoy)
            ->select('tipo_alimentacion', DB::raw('count(*) as total'))
            ->groupBy('tipo_alimentacion')
            ->pluck('total', 'tipo_alimentacion');
        $manualesHoy = $alimentacionesHoy->get('Manual', 0);
        $programadasHoy = $alimentacionesHoy->get('Programada', 0);


        // --- DATOS PARA EL GRÁFICO (CON RANGO DE BÚSQUEDA) ---

        // 5. "Comida gastada con rango de búsqueda"
        $consumoDiario = RegistroAlimentacion::query()
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(cantidad_dispensada_gramos) as total_gramos'))
            ->whereBetween('created_at', [$fechaInicio, $fechaFin->copy()->endOfDay()])
            ->groupBy('fecha')->orderBy('fecha', 'asc')->get();

        $labelsConsumo = $consumoDiario->map(fn($item) => Carbon::parse($item->fecha)->format('d/m'));
        $dataConsumo = $consumoDiario->map(fn($item) => $item->total_gramos / 1000); // En Kg


        // --- PASAR TODOS LOS DATOS A LA VISTA ---
        return view('public.estadisticas.index', [
            'dispensadorARellenar' => $dispensadorARellenar,
            'inventarioTotalKg' => $inventarioTotalKg,
            'diasRestantes' => $diasRestantes,
            'planCumplidoHoy' => $planCumplidoHoy,
            'manualesHoy' => $manualesHoy,
            'programadasHoy' => $programadasHoy,
            'labelsConsumo' => $labelsConsumo,
            'dataConsumo' => $dataConsumo,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ]);
    }
}