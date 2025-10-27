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
        // 🔹 LÓGICA PARA EL DUEÑO DE CRIADERO
        // ===============================================================
        if ($user->rol === 'Dueño') {
            $criaderoActivoId = session('active_criadero_id');

            if (!$criaderoActivoId) {
                return redirect()->route('criaderos.select');
            }

            $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');

            // 🕓 Próximas alimentaciones
            $data['proximosHorarios'] = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->whereTime('hora_programada', '>', Carbon::now()->format('H:i:s'))
                ->orderBy('hora_programada', 'asc')
                ->limit(3)
                ->get();

            // ⚠️ Dispensadores críticos (<15%)
            $data['dispensadoresCriticos'] = Dispensador::whereIn('id_dispensador', $dispensadorIds)
                ->where('nivel_comida_actual_kg', '<', (25 * 0.15)) // capacidad 25kg
                ->get();

            // 📈 Plan cumplido hoy
            $totalProgramadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->count();

            $totalEjecutadoHoy = HorarioAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                ->where('activo', true)
                ->whereNotNull('ultima_ejecucion')
                ->whereDate('ultima_ejecucion', Carbon::today())
                ->count();

            $data['planCumplidoHoy'] = ($totalProgramadoHoy > 0)
                ? round(($totalEjecutadoHoy / $totalProgramadoHoy) * 100)
                : 100;

            // 📊 Gráficos
            $labels = [];
            $valores = [];

            for ($i = 6; $i >= 0; $i--) {
                $fecha = Carbon::today()->subDays($i);
                $labels[] = $fecha->format('d/m');
                $valores[] = RegistroAlimentacion::whereIn('id_dispensador', $dispensadorIds)
                    ->whereDate('created_at', $fecha)
                    ->count();
            }

            $data['labelsFechas'] = $labels;
            $data['valoresAlimentaciones'] = $valores;

            $online = Dispensador::whereIn('id_dispensador', $dispensadorIds)
                ->where('ultimo_reporte', '>=', Carbon::now()->subHours(24))
                ->count();

            $offline = Dispensador::whereIn('id_dispensador', $dispensadorIds)
                ->where('ultimo_reporte', '<', Carbon::now()->subHours(24))
                ->count();

            $data['estadoDispensadores'] = [$online, $offline, 0];
        }

        // ===============================================================
        // 🔹 LÓGICA PARA EL SUPER ADMIN
        // ===============================================================
        elseif ($user->rol === 'Admin') {

            // 📊 Criaderos activos
            $data['totalCriaderosActivos'] = Criadero::where('estado', 'Activo')->count();

            // ❤️ Salud de la plataforma
            $totalDispensadores = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)->count();
            $dispensadoresOnline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->where('ultimo_reporte', '>=', Carbon::now()->subDay())
                ->count();

            $data['saludPlataforma'] = ($totalDispensadores > 0)
                ? round(($dispensadoresOnline / $totalDispensadores) * 100)
                : 100;

            // 🍽️ Alimentaciones de hoy
            $data['totalAlimentacionesHoy'] = RegistroAlimentacion::whereDate('created_at', Carbon::today())->count();

            // 📊 Gráficos
            $labels = [];
            $valores = [];

            for ($i = 6; $i >= 0; $i--) {
                $fecha = Carbon::today()->subDays($i);
                $labels[] = $fecha->format('d/m');
                $valores[] = RegistroAlimentacion::whereDate('created_at', $fecha)->count();
            }

            $data['labelsFechas'] = $labels;
            $data['valoresAlimentaciones'] = $valores;

            $online = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->where('ultimo_reporte', '>=', Carbon::now()->subHours(24))
                ->count();

            $offline = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                ->where('ultimo_reporte', '<', Carbon::now()->subHours(24))
                ->count();

            $mantenimiento = 0;
            $data['estadoDispensadores'] = [$online, $offline, $mantenimiento];
        }

        return view('dashboard', compact('data'));
    }
}
