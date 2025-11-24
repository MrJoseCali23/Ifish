<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criadero;
use App\Models\TipoComida;
use App\Models\Estanque;
use App\Models\Dispensador;
use App\Models\HorarioAlimentacion;
use App\Models\RegistroAlimentacion;
use App\Models\DispensadorEvento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CriaderoDataSeeder extends Seeder
{
    /**
     * Run the database seeds for a single criadero.
     *
     * @param \App\Models\Criadero $criadero
     * @return void
     */
    public function run(Criadero $criadero, & $dispensadorCounter)
    {
        // 1. Create TipoComida for the Criadero
        $tiposComida = collect();
        for ($i = 1; $i <= 3; $i++) {
            $tiposComida->push(TipoComida::create([
                'nombre_comida' => 'Alimento ' . $criadero->nombre . ' #' . $i,
                'proveedor' => 'Proveedor ' . $criadero->ubicacion,
                'criadero_id' => $criadero->id,
            ]));
        }

        // 2. Create Estanques for the Criadero
        $estanques = collect();
        for ($i = 1; $i <= 5; $i++) {
            $estanques->push(Estanque::create([
                'nombre_estanque' => 'Estanque ' . chr(64 + $i),
                'criadero_id' => $criadero->id,
                'ubicacion' => 'Sector ' . $i,
                'dimensiones_metros' => rand(10, 50) . 'x' . rand(5, 25) . 'm',
                'creado_por_usuario' => $criadero->user_id,
            ]));
        }

        // 3. Create Dispensadores for the Estanques
        $dispensadores = collect();
        $functionalDispenserCreated = false;
        $minDispensers = 3;

        // Special dispenser for Jose Callisaya's "Criadero Principal"
        if ($criadero->nombre === 'Criadero Principal') {
            $dispensadores->push(Dispensador::create([
                'mac_address' => 'DC:4F:22:7D:8B:7C',
                'modelo' => 'Dispensador Funcional v1',
                'estado' => 'Activo',
                'criadero_id' => $criadero->id,
                'id_estanque' => $estanques->random()->id_estanque,
            ]));
            $functionalDispenserCreated = true;
        }
        
        $dispensersToCreate = $functionalDispenserCreated ? $minDispensers - 1 : $minDispensers;

        for ($i = 0; $i < $dispensersToCreate; $i++) {
            $dispensadores->push(Dispensador::create([
                'mac_address' => $this->generateMacAddress(),
                'modelo' => 'Dispensador Prueba ' . $dispensadorCounter++,
                'estado' => 'Activo',
                'criadero_id' => $criadero->id,
                'id_estanque' => $estanques->random()->id_estanque,
            ]));
        }
        
        // 4. Create Horarios and Simulation Data for each Dispensador
        $horas = ['08:00:00', '12:30:00', '17:00:00'];
        foreach($dispensadores as $dispensador) {
            // Skip creating simulation data for the functional dispenser
            if ($dispensador->modelo === 'Dispensador Funcional v1') {
                continue;
            }

            if ($tiposComida->isNotEmpty()) {
                $comida = $tiposComida->random();
                $dispensador->update(['current_tipo_comida_id' => $comida->id_tipo_comida]);

                // Create Horarios
                foreach ($horas as $hora) {
                    HorarioAlimentacion::create([
                        'id_dispensador' => $dispensador->id_dispensador,
                        'id_tipo_comida' => $comida->id_tipo_comida,
                        'hora_programada' => $hora,
                        'cantidad_gramos' => rand(200, 800),
                        'creado_por_usuario' => $criadero->user_id,
                        'activo' => true,
                    ]);
                }

                // Create Simulation Data
                DispensadorEvento::create([
                    'dispensador_id' => $dispensador->id_dispensador,
                    'tipo_evento' => 'asignacion_criadero',
                    'descripcion' => 'Dispensador asignado al criadero ' . $criadero->nombre,
                    'user_id' => 1, // Super Admin
                    'created_at' => Carbon::now()->subMonths(2),
                ]);

                for ($d = 60; $d >= 0; $d--) {
                    $fecha = Carbon::now()->subDays($d);
                    if ($d % 2 == 0) {
                        $dispensador->update([
                            'nivel_comida_actual_kg' => rand(1, 20) + (rand(0, 99) / 100),
                            'temperatura_agua' => rand(18, 25) + (rand(0, 99) / 100),
                            'ultimo_reporte' => $fecha,
                        ]);
                    }

                    foreach ($dispensador->horarios as $horario) {
                        RegistroAlimentacion::create([
                            'id_dispensador' => $dispensador->id_dispensador,
                            'id_tipo_comida' => $horario->id_tipo_comida,
                            'cantidad_dispensada_gramos' => $horario->cantidad_gramos,
                            'tipo_alimentacion' => 'Programada',
                            'iniciado_por_usuario' => $criadero->user_id,
                            'created_at' => (clone $fecha)->setTimeFromTimeString($horario->hora_programada),
                        ]);
                    }

                    if (rand(1, 10) == 1) {
                         RegistroAlimentacion::create([
                            'id_dispensador' => $dispensador->id_dispensador,
                            'id_tipo_comida' => $dispensador->current_tipo_comida_id,
                            'cantidad_dispensada_gramos' => rand(100, 500),
                            'tipo_alimentacion' => 'Manual',
                            'iniciado_por_usuario' => $criadero->user_id,
                            'created_at' => (clone $fecha)->addHours(rand(9, 16)),
                        ]);
                    }
                }
            }
        }
    }
    
    private function generateMacAddress()
    {
        do {
            $mac = implode(':', str_split(substr(str_shuffle('0123456789ABCDEF'), 0, 12), 2));
        } while ($mac === 'DC:4F:22:7D:8B:7C');
        return $mac;
    }
}