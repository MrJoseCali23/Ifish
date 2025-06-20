<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\WeatherController; // Mantengo esta línea por tu ruta del clima

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ruta por defecto de Laravel para autenticación de APIs
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Tu ruta personalizada del clima ---
Route::get('/weather/colomi', [WeatherController::class, 'getColomiWeather'])->name('weather.colomi');


// --- RUTAS PARA LA API DE IFISH (LAS QUE FALTABAN) ---

// Ruta para que el ESP reporte el nivel de comida
Route::post('/reportar-nivel', [ApiController::class, 'reportarNivel']);

// Ruta para que el ESP pregunte si hay comandos
Route::get('/dispensadores/{mac_address}/comando', [ApiController::class, 'getComando']);