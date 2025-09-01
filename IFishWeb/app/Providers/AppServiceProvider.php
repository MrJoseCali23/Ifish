<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB; // Asegúrate de tener esta importación // Y esta, para el HTTPS en producción

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
        // El código que soluciona el error de la migración del ENUM
        try {
            DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        } catch (\Exception $e) {
            // Ignorar el error si el tipo ya está registrado
        }
    }
}