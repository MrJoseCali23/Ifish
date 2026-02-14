<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // En app/Console/Kernel.php

   protected function schedule(Schedule $schedule)
    {
        // Le decimos a Laravel que, cuando el "despertador" suene,
        // ejecute nuestro comando personalizado 'ifish:check-feedings'.
        $schedule->command('ifish:check-feedings')
                 ->everyMinute()
                 ->sendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
