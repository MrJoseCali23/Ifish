<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HorarioAlimentacion;
use Carbon\Carbon;

class CheckScheduledFeedings extends Command
{
    /**
     * La firma del comando para la terminal.
     */
    protected $signature = 'ifish:check-feedings';

    /**
     * La descripción del comando.
     */
    protected $description = 'Verifica los horarios de alimentación pendientes y activa los comandos correspondientes.';

    /**
     * Ejecuta la lógica del comando.
     */
    public function handle()
    {
        $this->info('Iniciando revisión de horarios...');

        // Buscamos todos los horarios que están activos
        $horariosActivos = HorarioAlimentacion::where('activo', true)->get();
        
        $ahora = Carbon::now();
        $horariosParaActivar = collect();

        foreach ($horariosActivos as $horario) {
            // Comprobamos si la última ejecución NO fue hoy
            $ejecutadoHoy = $horario->ultima_ejecucion && Carbon::parse($horario->ultima_ejecucion)->isToday();

            // Comprobamos si la hora programada ya pasó o es el minuto actual
            $horaProgramada = Carbon::parse($horario->hora_programada);
            $horaPasada = $ahora->format('H:i') >= $horaProgramada->format('H:i');

            // La condición mágica: si no se ha ejecutado hoy y su hora ya pasó, ¡actívalo!
            if (!$ejecutadoHoy && $horaPasada) {
                $horariosParaActivar->push($horario);
            }
        }

        if ($horariosParaActivar->isEmpty()) {
            $this->info('No se encontraron horarios para activar.');
            return 0;
        }

        $this->info("¡Se encontraron {$horariosParaActivar->count()} horarios para activar!");

        foreach ($horariosParaActivar as $horario) {
            $dispensador = $horario->dispensador;
            if ($dispensador) {
                $this->line("-> Activando comando para dispensador: {$dispensador->mac_address}");
                
                // Pone la orden para que el ESP la recoja
                $dispensador->comando_pendiente = 'dispensar';
                $dispensador->comando_valor = $horario->cantidad_gramos;
                $dispensador->save();

                // Marcamos el horario como ejecutado para que no se repita hoy
                $horario->ultima_ejecucion = $ahora;
                $horario->save();
            }
        }

        $this->info('Revisión completada.');
        return 0;
    }
}