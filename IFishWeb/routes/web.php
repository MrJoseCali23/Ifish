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

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('inicio'))->name('inicio');
Route::view('/ayuda', 'ayuda')->name('ayuda');

// Invitaciones
Route::get('/invitacion/aceptar/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitacion/establecer-contraseña/{token}', [InvitationController::class, 'setPassword'])->name('invitation.set-password');

// Autenticación
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| RUTAS COMUNES (Cualquier usuario autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Selección de criadero
    Route::get('/seleccionar-criadero', [CriaderoSelectorController::class, 'showSelection'])->name('criaderos.select');
    Route::get('/seleccionar-criadero/{criadero}', [CriaderoSelectorController::class, 'selectCriadero'])->name('criaderos.set-active');

    // Datos en tiempo real (usado por AJAX)
    Route::get('/dispensadores/live-data', [DispensadorController::class, 'data'])->name('dispensadores.data');
});
/*
|--------------------------------------------------------------------------
| RUTAS GLOBALES COMPARTIDAS (Admin + Dueño)
|--------------------------------------------------------------------------
|
| Estos módulos son accesibles para ambos roles. El contenido mostrado
| dentro de cada vista se adapta dinámicamente según el rol del usuario.
|
*/
Route::middleware(['auth', 'criadero.selected'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Estadísticas globales
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');

    // Central de Reportes (contenido adaptable al rol)
    Route::controller(ReporteController::class)
        ->prefix('reportes')
        ->name('reportes.')
        ->group(function () {
            Route::get('/', 'index')->name('index');

            // ✅ NUEVO: habilitamos acceso a estas rutas para evitar errores
            Route::get('/criaderos', 'reporteCriaderosTabla')->name('criaderos.tabla');
            Route::get('/criaderos/pdf', 'reporteCriaderosPdf')->name('criaderos.pdf');
            Route::get('/salud-plataforma', 'reporteSaludPlataforma')->name('salud_plataforma');
            Route::get('/historial-dispensador', 'reporteHistorialDispensador')->name('historial_dispensador');
            Route::get('/historial-dispensador/pdf', 'reporteHistorialDispensadorPdf')->name('historial_dispensador.pdf');
        });
});

/*
|--------------------------------------------------------------------------
| RUTAS PARA DUEÑOS DE CRIADERO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'criadero.selected', 'role:Dueño'])->group(function () {

    // CRUDs del criadero
    Route::resource('estanques', EstanqueController::class);
    Route::resource('dispensadores', DispensadorController::class)->except(['create', 'store']);
    Route::resource('tipos_comida', TipoComidaController::class);
    Route::resource('horarios', HorarioAlimentacionController::class);

    // Alimentación manual
    Route::post('dispensadores/{dispensadore}/alimentar', [DispensadorController::class, 'manualFeed'])
        ->name('dispensadores.manualFeed');

    // Reportes específicos del Dueño
    Route::controller(ReporteController::class)->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/historial-alimentacion', 'historialAlimentacionForm')->name('historial_alimentacion.form');
        Route::get('/historial-alimentacion/pdf', 'historialAlimentacionPdf')->name('historial_alimentacion.pdf');
        Route::get('/consumo-comida', 'reporteConsumoComida')->name('consumo_comida');
        Route::get('/consumo-comida/pdf', 'reporteConsumoComidaPdf')->name('consumo_comida.pdf');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS PARA SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

    // Dashboard del admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión principal
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('criaderos', CriaderoController::class);
    Route::resource('dispensadores-inventario', DispensadorInventarioController::class);

    // Acciones especiales
    Route::get('dispensadores-archivados', [DispensadorInventarioController::class, 'indexArchivados'])->name('dispensadores-inventario.archivados');
    Route::get('dispensadores-inventario/{dispensadores_inventario}/historial', [DispensadorInventarioController::class, 'showHistory'])->name('dispensadores-inventario.history');
    Route::post('criaderos/{criadero}/archive', [CriaderoController::class, 'archive'])->name('criaderos.archive');
    Route::post('criaderos/{criadero}/restore', [CriaderoController::class, 'restore'])->name('criaderos.restore');
    Route::get('criaderos/{criadero}/assign', [CriaderoController::class, 'showAssignForm'])->name('criaderos.assignForm');
    Route::post('criaderos/{criadero}/assign', [CriaderoController::class, 'assignDispenser'])->name('criaderos.assign');
    Route::post('usuarios/{usuario}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
    Route::post('usuarios/{usuario}/send-reset-link', [UsuarioController::class, 'sendPasswordReset'])->name('usuarios.send-reset-link');

    // Reportes del Super Admin
    Route::controller(ReporteController::class)->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/criaderos', 'reporteCriaderosTabla')->name('criaderos.tabla');
        Route::get('/criaderos/pdf', 'reporteCriaderosPdf')->name('criaderos.pdf');
        Route::get('/salud-plataforma', 'reporteSaludPlataforma')->name('salud_plataforma');
        Route::get('/historial-dispensador', 'reporteHistorialDispensador')->name('historial_dispensador');
    });
});
