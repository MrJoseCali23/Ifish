<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ruta por defecto de Laravel para autenticación de APIs (es bueno mantenerla)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Tu ruta personalizada del clima ---
Route::get('/weather/colomi', [WeatherController::class, 'getColomiWeather'])->name('weather.colomi');


// --- RUTAS PARA LA COMUNICACIÓN CON EL ESP ---

// Ruta para que el ESP reporte su estado (nivel de comida, temperatura)
Route::post('/reportar-nivel', [ApiController::class, 'reportarNivel']);

// Ruta para que el ESP pregunte si tiene comandos pendientes
// (Corregido para apuntar al método correcto 'obtenerComando')
Route::get('/dispensadores/{mac_address}/comando', [ApiController::class, 'obtenerComando']);
