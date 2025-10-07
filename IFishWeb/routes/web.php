<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EstanqueController;
use App\Http\Controllers\DispensadorController;
use App\Http\Controllers\TipoComidaController;
use App\Http\Controllers\HorarioAlimentacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SuperAdmin\CriaderoController;
use App\Http\Controllers\SuperAdmin\DispensadorInventarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CriaderoSelectorController;
use App\Http\Controllers\InvitationController;
// use App\Http\Controllers\Dueño\CriaderoController as DueñoCriaderoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS ---
Route::get('/', function () {
    return view('inicio');
})->name('inicio');
Route::view('/ayuda', 'ayuda')->name('ayuda');

Route::get('/invitacion/aceptar/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitacion/establecer-contraseña/{token}', [InvitationController::class, 'setPassword'])->name('invitation.set-password');

// --- RUTAS DE AUTENTICACIÓN ---
require __DIR__.'/auth.php';


// --- RUTAS PROTEGIDAS QUE NO REQUIEREN SELECCIÓN DE CRIADERO ---
// El usuario debe estar logueado, pero aquí es donde elige en qué criadero trabajar.
Route::middleware(['auth'])->group(function () {
    Route::get('/seleccionar-criadero', [CriaderoSelectorController::class, 'showSelection'])->name('criaderos.select');
    Route::get('/seleccionar-criadero/{criadero}', [CriaderoSelectorController::class, 'selectCriadero'])->name('criaderos.set-active');
    
    // // CRUD para que el Dueño gestione la lista de sus propios criaderos
    // Route::resource('mis-criaderos', DueñoCriaderoController::class)->names('dueño.criaderos');
});


// --- RUTAS PROTEGIDAS QUE SÍ REQUIEREN UN CRIADERO ACTIVO ---
// A tu grupo de rutas principal le hemos añadido nuestro nuevo "guardia": 'criadero.selected'.
Route::middleware(['auth', 'criadero.selected'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil del Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Estadísticas
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');

    // CRUDs de Operación del Criadero
    Route::resource('estanques', EstanqueController::class);
    Route::resource('dispensadores', DispensadorController::class)->except(['create', 'store']);
    Route::resource('tipos_comida', TipoComidaController::class);
    Route::resource('horarios', HorarioAlimentacionController::class);
    
    // Acciones personalizadas
    
    Route::post('dispensadores/{dispensadore}/alimentar', [DispensadorController::class, 'manualFeed'])->name('dispensadores.manualFeed');
    
    // Grupo de rutas para la sección de Reportes
    Route::controller(ReporteController::class)->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/historial-alimentacion', 'historialAlimentacionForm')->name('historial_alimentacion.form');
        Route::get('/criaderos', 'reporteCriaderosTabla')->name('criaderos.tabla');
        Route::get('/criaderos/pdf', 'reporteCriaderosPdf')->name('criaderos.pdf');
        Route::get('/salud-plataforma', 'reporteSaludPlataforma')->name('salud_plataforma');
        Route::get('/historial-alimentacion/pdf', 'historialAlimentacionPdf')->name('historial_alimentacion.pdf');
        Route::get('/historial-dispensador', 'reporteHistorialDispensador')->name('historial_dispensador');
        Route::get('/consumo-comida', 'reporteConsumoComida')->name('consumo_comida');
        Route::get('/consumo-comida/pdf', 'reporteConsumoComidaPdf')->name('consumo_comida.pdf');
    });
});


// --- RUTAS PROTEGIDAS SOLO PARA SUPER ADMIN ---
// Este grupo se queda igual, ya que el Super Admin no necesita seleccionar un criadero.
Route::middleware(['auth', 'isadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('criaderos', CriaderoController::class);
    Route::resource('dispensadores-inventario', DispensadorInventarioController::class);
    
    // Acciones personalizadas de Super Admin
    Route::get('dispensadores-archivados', [DispensadorInventarioController::class, 'indexArchivados'])->name('dispensadores-inventario.archivados');
    Route::get('dispensadores-inventario/{dispensadores_inventario}/historial', [DispensadorInventarioController::class, 'showHistory'])->name('dispensadores-inventario.history');
    Route::post('criaderos/{criadero}/archive', [CriaderoController::class, 'archive'])->name('criaderos.archive');
    Route::post('criaderos/{criadero}/restore', [CriaderoController::class, 'restore'])->name('criaderos.restore');
    Route::get('criaderos/{criadero}/assign', [CriaderoController::class, 'showAssignForm'])->name('criaderos.assignForm');
    Route::post('criaderos/{criadero}/assign', [CriaderoController::class, 'assignDispenser'])->name('criaderos.assign');
    Route::post('usuarios/{usuario}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
    Route::post('usuarios/{usuario}/send-reset-link', [UsuarioController::class, 'sendPasswordReset'])->name('usuarios.send-reset-link');
    
});