<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BicicletaController;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\MensualidadController;
use App\Http\Controllers\MotoController;
use App\Http\Controllers\UsuarioController;

// Redirigir a login si se accede a la raíz
Route::get('/', fn() => redirect()->route('login'));

// 🔹 Rutas de Autenticación
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// 🔹 Dashboard (Requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');


    // 🔹 Rutas de Bicicletas, Carros y Motos
    Route::prefix('vehiculos')->group(function () {
        Route::resource('bicicletas', BicicletaController::class);
        Route::post('bicicletas/{id}/salida', [BicicletaController::class, 'registrarSalida'])->name('bicicletas.salida');

        Route::resource('carros', CarroController::class);
        Route::post('carros/{id}/salida', [CarroController::class, 'registrarSalida'])->name('carros.salida');

        Route::resource('motos', MotoController::class);
        Route::post('motos/{id}/salida', [MotoController::class, 'registrarSalida'])->name('motos.salida');
    });

    // 🔹 Rutas para Administradores
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::resource('mensualidades', MensualidadController::class);
        Route::resource('usuarios', UsuarioController::class);
    });
});