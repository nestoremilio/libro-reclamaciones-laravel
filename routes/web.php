<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// --- Público ---
Route::view('/', 'reclamo')->name('reclamo');

// --- Autenticación ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// --- Panel administrativo ---
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/reclamaciones/{reclamacion}', [AdminController::class, 'show'])->name('show');
    Route::post('/reclamaciones/{reclamacion}/responder', [AdminController::class, 'responder'])->name('responder');
    Route::get('/reclamaciones/{reclamacion}/evidencia', [AdminController::class, 'evidencia'])->name('evidencia');
    Route::get('/reclamaciones/{reclamacion}/reporte', [AdminController::class, 'reporte'])->name('reporte');
});
