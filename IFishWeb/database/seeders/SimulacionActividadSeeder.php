<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dispensador;
use App\Models\RegistroAlimentacion;
use App\Models\DispensadorEvento;
use Carbon\Carbon;

class SimulacionActividadSeeder extends Seeder
{
    public function run()
    {
        $dispensadores = Dispensador::with('criadero')->get();

        foreach ($dispensadores as $dispensador) {
            // Evento de creación/asignación
            DispensadorEvento::create([
                'dispensador_id' => $dispensador->id_dispensador,
                'tipo_evento' => 'asignacion_criadero',
                'descripcion' => 'Dispensador asignado al criadero ' . $dispensador->criadero->nombre,
                'user_id' => 1, // Super Admin
                'created_at' => Carbon::now()->subMonths(2),
            ]);

            // Simular 60 días de actividad
            for ($d = 60; $d >= 0; $d--) {
                $fecha = Carbon::now()->subDays($d);

                // Simular reportes de ESP
                if ($d % 2 == 0) { // Reporta cada 2 días para simular
                    $dispensador->update([
                        'nivel_comida_actual_kg' => rand(1, 20) + (rand(0, 99) / 100),
                        'temperatura_agua' => rand(18, 25) + (rand(0, 99) / 100),
                        'ultimo_reporte' => $fecha,
                    ]);
                }

                // Simular alimentaciones programadas
                foreach ($dispensador->horarios as $horario) {
                    RegistroAlimentacion::create([
                        'id_dispensador' => $dispensador->id_dispensador,
                        'id_tipo_comida' => $horario->id_tipo_comida,
                        'cantidad_dispensada_gramos' => $horario->cantidad_gramos,
                        'tipo_alimentacion' => 'Programada',
                        'iniciado_por_usuario' => $dispensador->criadero->user_id,
                        'created_at' => (clone $fecha)->setTimeFromTimeString($horario->hora_programada),
                    ]);
                }

                // Simular alguna alimentación manual
                if (rand(1, 10) == 1) {
                     RegistroAlimentacion::create([
                        'id_dispensador' => $dispensador->id_dispensador,
                        'id_tipo_comida' => $dispensador->current_tipo_comida_id,
                        'cantidad_dispensada_gramos' => rand(100, 500),
                        'tipo_alimentacion' => 'Manual',
                        'iniciado_por_usuario' => $dispensador->criadero->user_id,
                        'created_at' => (clone $fecha)->addHours(rand(9, 16)),
                    ]);
                }
            }
        }
    }
}
