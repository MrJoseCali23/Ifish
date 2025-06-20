<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HorarioAlimentacion;
use App\Models\RegistroAlimentacion;
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
    protected $description = 'Revisa los horarios de alimentación y activa los comandos pendientes para los dispensadores.';

    /**
     * Ejecuta la lógica del comando.
     */
    public function handle()
    {
        $this->info('Iniciando revisión de horarios...');

        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        // NUEVA LÓGICA DE BÚSQUEDA:
        // Busca horarios activos, que coincidan con la hora y minuto actual,
        // Y que no se hayan ejecutado hoy.
        $horariosParaActivar = HorarioAlimentacion::where('activo', true)
            ->where('hora_programada', 'LIKE', $currentTime . ':%')
            ->where(function ($query) use ($today) {
                $query->whereNull('ultima_ejecucion') // Si nunca se ha ejecutado
                    ->orWhereDate('ultima_ejecucion', '<', $today); // O si la última vez fue antes de hoy
            })
            ->get();

        if ($horariosParaActivar->isEmpty()) {
            $this->info('No hay horarios para activar en este minuto.');
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

                // ¡AQUÍ ESTÁ LA PARTE COMPLETADA!
                // Creamos el registro en el historial para las estadísticas
                RegistroAlimentacion::create([
                'id_dispensador' => $dispensador->id_dispensador,
                'id_tipo_comida' => $horario->id_tipo_comida,
                'iniciado_por_usuario' => $horario->creado_por_usuario, // Guardamos quién programó originalmente el horario
                'cantidad_dispensada_gramos' => $horario->cantidad_gramos,
                'tipo_alimentacion' => 'Programada', // Marcamos que fue una ejecución automática
                'exitoso' => true, // Asumimos que la orden se dio con éxito
                ]);

                // Actualiza el horario para que no se repita hoy
                $horario->update(['ultima_ejecucion' => Carbon::now()]);
            }
        }
        $this->info('Revisión completada.');
        return 0;
    }
}
