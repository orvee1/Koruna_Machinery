<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/create-user', [AdminController::class, 'createUser'])->name('createUser');
    Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
    Route::resource('branches', BranchController::class);
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('customers.index');
    // ProductController Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::get('/products/{product}/sales-history', [ProductController::class, 'show'])->name('products.show');

});

// Worker Routes (Protected)
Route::middleware(['auth', 'checkRole:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales-history', [WorkerController::class, 'salesHistory'])->name('sales');
    Route::get('/worker/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/worker/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('worker/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('worker/products', [ProductController::class, 'store'])->name('products.store');
});

Route::get('/homepage', [HomePageController::class, 'index'])->name('homepage');