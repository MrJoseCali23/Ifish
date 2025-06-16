<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('inicio'); // pública
});
// 👇 Ruta pública
Route::view('/estadisticas', 'estadisticas')->name('estadisticas');
Route::view('/ayuda', 'ayuda')->name('ayuda');
// Secciones privadas (requieren login)

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/estanques', 'estanques')->name('estanques');
    Route::view('/especies', 'especies')->name('especies');
    Route::view('/arduino', 'arduino')->name('arduino');
    Route::view('/reportes', 'reportes')->name('reportes');
    Route::view('/configuracion', 'configuracion')->name('configuracion');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'isadmin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

require __DIR__.'/auth.php';
