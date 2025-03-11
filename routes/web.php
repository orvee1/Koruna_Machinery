<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/create-user', [AdminController::class, 'createUser'])->name('createUser');
    Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
    Route::resource('branches', BranchController::class);
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('customers.index');
});

// Worker Routes (Protected)
Route::middleware(['auth', 'checkRole:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales-history', [WorkerController::class, 'salesHistory'])->name('sales');
    Route::get('/worker/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/worker/customers', [CustomerController::class, 'store'])->name('customers.store');
});

Route::get('/homepage', [HomePageController::class, 'index'])->name('homepage');