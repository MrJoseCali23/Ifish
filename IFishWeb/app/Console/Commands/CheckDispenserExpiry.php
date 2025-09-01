<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dispensador;
use Carbon\Carbon;

class CheckDispenserExpiry extends Command
{
    protected $signature = 'ifish:check-expiry';
    protected $description = 'Revisa los dispensadores activos y los desactiva si su fecha de disponibilidad ha pasado.';

    public function handle()
    {
        $this->info('Buscando dispensadores expirados...');

        $dispensadoresExpirados = Dispensador::where('estado', 'Activo')
                                    ->whereNotNull('disponible_hasta')
                                    ->where('disponible_hasta', '<', Carbon::now())
                                    ->get();

        if ($dispensadoresExpirados->isEmpty()) {
            $this->info('No se encontraron dispensadores expirados.');
            return 0;
        }

        $this->info("Se encontraron {$dispensadoresExpirados->count()} dispensadores para desactivar.");

        foreach ($dispensadoresExpirados as $dispensador) {
            $dispensador->update(['estado' => 'Inactivo']);
            $this->line("-> Dispensador {$dispensador->mac_address} ha sido puesto como 'Inactivo'.");
        }

        $this->info('Proceso completado.');
        return 0;
    }
}