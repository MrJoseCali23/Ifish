<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EstanqueController;
use App\Http\Controllers\DispensadorController;
use App\Http\Controllers\TipoComidaController;
use App\Http\Controllers\HorarioAlimentacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SuperAdmin\CriaderoController;
use App\Http\Controllers\SuperAdmin\DispensadorInventarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS ---
// Cualquiera puede acceder a estas rutas, sin iniciar sesión.
Route::get('/', function () {
    return view('inicio');
});
Route::view('/ayuda', 'ayuda')->name('ayuda');


// --- RUTAS DE AUTENTICACIÓN (Login, Registro, etc.) ---
require __DIR__.'/auth.php';


// --- RUTAS PROTEGIDAS (PARA CUALQUIER USUARIO LOGUEADO) ---
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Perfil del Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Estadísticas
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');

    // CRUDs de Operación del Criadero
    Route::resource('estanques', EstanqueController::class);
    Route::resource('dispensadores', DispensadorController::class);
    Route::resource('tipos_comida', TipoComidaController::class);
    Route::resource('horarios', HorarioAlimentacionController::class);
    
    // Acciones personalizadas
    Route::post('dispensadores/{dispensadore}/alimentar', [DispensadorController::class, 'manualFeed'])->name('dispensadores.manualFeed');
    Route::get('estanques/{estanque}/plan', [EstanqueController::class, 'showPlanForm'])->name('estanques.plan.edit');
    Route::post('estanques/{estanque}/plan', [EstanqueController::class, 'storePlan'])->name('estanques.plan.store');

    // Grupo de rutas para la sección de Reportes
    Route::controller(ReporteController::class)->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/historial-alimentacion', 'historialAlimentacionForm')->name('historial_alimentacion.form');
        Route::get('/criaderos', 'reporteCriaderosTabla')->name('criaderos.tabla');
        Route::get('/criaderos/pdf', 'reporteCriaderosPdf')->name('criaderos.pdf');
        Route::get('/salud-plataforma', 'reporteSaludPlataforma')->name('salud_plataforma');
        Route::get('/historial-alimentacion/pdf', 'historialAlimentacionPdf')->name('historial_alimentacion.pdf');

    });
});


// --- RUTAS PROTEGIDAS (SOLO PARA SUPER ADMIN) ---
Route::middleware(['auth', 'isadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('criaderos', CriaderoController::class);
    Route::resource('dispensadores-inventario', DispensadorInventarioController::class);
    
    // Acciones personalizadas de Super Admin
    Route::get('dispensadores-inventario/{dispensadores_inventario}/historial', [DispensadorInventarioController::class, 'showHistory'])->name('dispensadores-inventario.history');
    Route::get('dispensadores-archivados', [DispensadorInventarioController::class, 'indexArchivados'])->name('dispensadores-inventario.archivados');
    Route::post('criaderos/{criadero}/archive', [CriaderoController::class, 'archive'])->name('criaderos.archive');
    Route::post('criaderos/{criadero}/restore', [CriaderoController::class, 'restore'])->name('criaderos.restore');
    Route::get('criaderos/{criadero}/assign', [CriaderoController::class, 'showAssignForm'])->name('criaderos.assignForm');
    Route::post('criaderos/{criadero}/assign', [CriaderoController::class, 'assignDispenser'])->name('criaderos.assign');
});
