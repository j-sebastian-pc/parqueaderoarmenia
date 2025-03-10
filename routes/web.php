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

        // Rutas de carros con los nombres correctos
        Route::get('carros', [CarroController::class, 'index'])->name('carros.index');
        Route::post('carros', [CarroController::class, 'store'])->name('carros.store');
        Route::get('carros/create', [CarroController::class, 'create'])->name('carros.create');
        Route::get('carros/{id}', [CarroController::class, 'show'])->name('carros.show');
        Route::get('carros/{id}/edit', [CarroController::class, 'edit'])->name('carros.edit');
        Route::put('carros/{id}', [CarroController::class, 'update'])->name('carros.update');
        Route::delete('carros/{id}', [CarroController::class, 'destroy'])->name('carros.destroy');
        Route::put('carros/{id}/salida', [CarroController::class, 'registrarSalida'])->name('carros.salida');
        Route::get('/carros/buscar', [CarroController::class, 'buscarPorPlaca'])->name('carros.buscar');

        Route::resource('motos', MotoController::class);
        Route::post('motos/{id}/salida', [MotoController::class, 'registrarSalida'])->name('motos.salida');
    });

    // 🔹 Rutas para Administradores
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        // Rutas de Mensualidades
        Route::prefix('mensualidades')->group(function () {
            Route::get('/', [MensualidadController::class, 'index'])->name('mensualidades.index');
            Route::post('/store', [MensualidadController::class, 'store'])->name('mensualidades.store');
            Route::post('/renovar/{id}', [MensualidadController::class, 'renovarMensualidad'])->name('mensualidades.renovar');
            Route::post('/eliminar/{id}', [MensualidadController::class, 'eliminarMensualidad'])->name('mensualidades.eliminar');
            Route::get('/mensualidades', [MensualidadController::class, 'index'])->name('mensualidades.index');
        });
        // Rutas de Usuarios
        Route::resource('usuarios', UsuarioController::class);
    });
});