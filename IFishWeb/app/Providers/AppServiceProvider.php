<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    // app/Providers/AppServiceProvider.php

    public function boot()
    {
        // ▼▼▼ AÑADE ESTE BLOQUE DE CÓDIGO ▼▼▼
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
