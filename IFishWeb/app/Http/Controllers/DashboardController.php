<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use App\Models\Criadero;
use App\Models\RegistroAlimentacion;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // ===============================================================
        // LÓGICA PARA EL DUEÑO DE CRIADERO (FILTRADA)
        // ===============================================================
        if ($user->rol === 'Dueño') {
            $criaderoActivoId = session('active_criadero_id');
            
            if (!$criaderoActivoId) {
                // Si aún no ha seleccionado un criadero, lo mandamos a la pantalla de selección
                return redirect()->route('criaderos.select');
            }

            $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');

            // Próximas 3 alimentaciones
            $data['proximosHorarios'] = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->whereTime('hora_programada', '>', Carbon::now()->format('H:i:s'))
                ->orderBy('hora_programada', 'asc')
                ->limit(3)
                ->get();

            // Dispensadores con nivel de comida crítico (< 15%)
            $data['dispensadoresCriticos'] = Dispensador::whereIn('id_dispensador', $dispensadorIds)
                ->where('nivel_comida_actual_kg', '<', (25 * 0.15)) // Asumiendo capacidad de 25kg
                ->get();

            // Plan Cumplido Hoy (%)
            $totalProgramadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)->where('activo', true)->count();
            $totalEjecutadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->whereNotNull('ultima_ejecucion')
                ->whereDate('ultima_ejecucion', Carbon::today())
                ->count();
            $data['planCumplidoHoy'] = ($totalProgramadoHoy > 0) ? round(($totalEjecutadoHoy / $totalProgramadoHoy) * 100) : 100;
        
        } 
        // ===============================================================
        // LÓGICA PARA EL SUPER ADMIN (NUEVA Y MEJORADA)
        // ===============================================================
        elseif ($user->rol === 'Admin') {
            // Tarjeta: Número Total de Criaderos Activos
            $data['totalCriaderosActivos'] = Criadero::where('estado', 'Activo')->count();

            // Tarjeta: Salud de la Plataforma (% Online)
            $totalDispensadores = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)->count();
            $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                        ->where('ultimo_reporte', '>=', Carbon::now()->subDay())
                                        ->count();
            $data['saludPlataforma'] = ($totalDispensadores > 0) ? round(($dispensadoresOnline / $totalDispensadores) * 100) : 100;

            // Tarjeta: Total de Alimentaciones (Hoy)
            $data['totalAlimentacionesHoy'] = RegistroAlimentacion::whereDate('created_at', Carbon::today())->count();
        }

        return view('dashboard', compact('data'));
    }
}
