<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB; // Asegúrate de tener esta importación
use Illuminate\Support\Facades\URL; // Y esta, para el HTTPS en producción

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Código para forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // El código que soluciona el error de la migración del ENUM
        try {
            DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        } catch (\Doctrine\DBAL\Exception\UnknownColumnType $e) {
            // Se puede ignorar este error si la columna ya está registrada.
        }
        
    }
}