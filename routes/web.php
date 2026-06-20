<?php

if (!file_exists(database_path('database.sqlite'))) {
    touch(database_path('database.sqlite'));
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    } catch (\Exception $e) {
        // Evita errores si se ejecuta al mismo tiempo
    }
}

//define a que url puede ir el usuario
// Importa las herramientas necesarias de Laravel para crear rutas
use Illuminate\Support\Facades\Route;

// Importa el controlador del perfil de usuario
use App\Http\Controllers\ProfileController;

// Importa el controlador encargado de los reportes de incendios
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Página principal
|--------------------------------------------------------------------------
|
| Cuando alguien entra a la dirección principal del proyecto (/),
| Laravel mostrará la vista llamada "home".
|
*/
//el route view es un atajo
//mostrara el home
Route::view('/', 'home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| Esta es la pantalla principal después de iniciar sesión.
| Solo pueden acceder usuarios autenticados y verificados.
|
*/

//el get es el navegador pide una pagina 
Route::get('/dashboard', function () {

    // Muestra la vista dashboard.blade.php
    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');
//el auth exige sesion iniciada
//si no esta iniciada se dirigira a login

//verified exige que el email este verificado
//sino lo redigira a una paian de navegacion

//name() le da un nombre
//laravel buscara la url asociada el nombre

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
|
| Todas las rutas dentro de este grupo requieren que el usuario
| haya iniciado sesión.
|
*/
Route::middleware('auth')->group(function () {
    //todo lo que esta adentro , debe estra logueado por el usuario 

    /*
    |--------------------------------------------------------------------------
    | Perfil de usuario
    |--------------------------------------------------------------------------
    */

    // Muestra el formulario para editar el perfil

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Guarda los cambios realizados en el perfil
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Elimina la cuenta del usuario
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reportes de incendios
    |--------------------------------------------------------------------------
    */

    // Muestra el formulario para crear un nuevo reporte
    Route::get('/reportes/crear', [ReportController::class, 'create'])
        ->name('reports.create');

    // Recibe y guarda los datos enviados desde el formulario
    Route::post('/reportes', [ReportController::class, 'store'])
        ->name('reports.store');

    Route::get('/whatsapp-simulator', function () {
        return view('simulador');
    })->name('whatsapp.simulator');

});

/*
|--------------------------------------------------------------------------
| Rutas de autenticación
|--------------------------------------------------------------------------
|
| Este archivo contiene todas las rutas creadas por Laravel Breeze:
| - Login
| - Registro
| - Recuperar contraseña
| - Cerrar sesión
|
*/
require_once __DIR__.'/auth.php';
