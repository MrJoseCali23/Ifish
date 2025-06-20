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
        // --- NUEVO BLOQUE DE VALIDACIÓN Y MANEJO DE FECHAS ---

        // 1. Validación básica del formato y orden de las fechas
        $request->validate([
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
        ]);

        // 2. Definimos nuestros límites (no se puede buscar antes del primer registro ni después de hoy)
        $primerRegistro = RegistroAlimentacion::orderBy('created_at', 'asc')->first();
        $fechaMinima = $primerRegistro ? Carbon::parse($primerRegistro->created_at) : Carbon::now();
        $fechaMaxima = Carbon::now();

        // 3. Obtenemos las fechas del request o usamos valores por defecto (última semana)
        $fechaFin = $request->filled('fecha_fin')
                    ? Carbon::parse($request->input('fecha_fin'))
                    : $fechaMaxima->copy();

        $fechaInicio = $request->filled('fecha_inicio')
                    ? Carbon::parse($request->input('fecha_inicio'))
                    : $fechaMaxima->copy()->subWeek();

        // 4. Aplicamos nuestras reglas personalizadas para corregir las fechas
        if ($fechaFin->isFuture()) {
            $fechaFin = $fechaMaxima->copy(); // Si es futura, la seteamos a hoy
        }
        if ($fechaInicio->lessThan($fechaMinima)) {
            $fechaInicio = $fechaMinima->copy(); // Si es muy antigua, la seteamos a la fecha mínima
        }

        // --- FIN DEL BLOQUE DE VALIDACIÓN ---


        // --- CÁLCULOS (usan las fechas ya validadas y corregidas) ---

        // Total de Alimentaciones en el rango de fechas validado
        $totalAlimentacionesRango = RegistroAlimentacion::whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])->count();

        // El resto de tus cálculos...
        $totalProgramadoHoyKg = HorarioAlimentacion::where('activo', true)->sum('cantidad_gramos') / 1000;
        $dispensadorNivelBajo = Dispensador::orderBy('nivel_comida_actual_kg', 'asc')->first();
        $inventarioTotalKg = Dispensador::sum('nivel_comida_actual_kg');
        $diasRestantes = ($totalProgramadoHoyKg > 0) ? floor($inventarioTotalKg / $totalProgramadoHoyKg) : '∞';

        $consumoDiario = RegistroAlimentacion::query()
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(cantidad_dispensada_gramos) as total_gramos'))
            ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()]) // Siempre los últimos 7 días
            ->groupBy('fecha')->orderBy('fecha', 'asc')->get();

        $labelsConsumo = $consumoDiario->map(fn($item) => Carbon::parse($item->fecha)->format('d/m'));
        $dataConsumo = $consumoDiario->map(fn($item) => $item->total_gramos / 1000);

        $cargaPorDispensador = HorarioAlimentacion::query()
            ->join('Dispensadores', 'Horarios_Alimentacion.id_dispensador', '=', 'Dispensadores.id_dispensador')
            ->select('Dispensadores.mac_address', DB::raw('SUM(Horarios_Alimentacion.cantidad_gramos) as total_gramos'))
            ->where('Horarios_Alimentacion.activo', true)->groupBy('Dispensadores.mac_address')
            ->orderBy('total_gramos', 'desc')->get();

        $labelsCarga = $cargaPorDispensador->pluck('mac_address');
        $dataCarga = $cargaPorDispensador->map(fn($item) => $item->total_gramos / 1000);


        // --- PASAR TODOS LOS DATOS A LA VISTA ---
        return view('public.estadisticas.index', [
            'totalProgramadoHoyKg' => $totalProgramadoHoyKg,
            'dispensadorNivelBajo' => $dispensadorNivelBajo,
            'diasRestantes' => $diasRestantes,
            'totalAlimentacionesRango' => $totalAlimentacionesRango,
            'fechaInicio' => $fechaInicio->toDateString(), // Pasamos las fechas corregidas a la vista
            'fechaFin' => $fechaFin->toDateString(),
            'labelsConsumo' => $labelsConsumo,
            'dataConsumo' => $dataConsumo,
            'labelsCarga' => $labelsCarga,
            'dataCarga' => $dataCarga,
        ]);
    }
}