<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Dispensador;

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
        try {
            DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        } catch (\Exception $e) {
        }

        Paginator::useBootstrapFive();

        // Compartir alertas de dispensadores críticos con todas las vistas relevantes
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->rol === 'Dueño') {
                $criaderoActivoId = session('active_criadero_id');
                $dispensadoresCriticos = collect(); // Por defecto, una colección vacía

                if ($criaderoActivoId) {
                    $dispensadorIds = Dispensador::where('criadero_id', $criaderoActivoId)->pluck('id_dispensador');
                    
                    // Lógica para dispensadores críticos (< 15% de 25kg)
                    $dispensadoresCriticos = Dispensador::whereIn('id_dispensador', $dispensadorIds)
                        ->where('nivel_comida_actual_kg', '<', (25 * 0.15))
                        ->get();
                }
                
                $view->with('dispensadoresCriticos', $dispensadoresCriticos);
            }
        });
    }
}