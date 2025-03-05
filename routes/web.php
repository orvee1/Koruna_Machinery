<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/create-user', [AdminController::class, 'createUser'])->name('createUser');
    Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
});

// Worker Routes (Protected)
Route::middleware(['auth', 'checkRole:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales-history', [WorkerController::class, 'salesHistory'])->name('sales');
});
