<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware(['auth', 'checkRole:admin'])->group(function () {
//     // Branch routes
//     Route::resource('admin/branches', BranchController::class);
// });

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('branches', BranchController::class);
});



// Admin Routes
// Route::middleware(['auth', 'checkRole:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User Creation
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');  // Show the form to create a new user
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');  // Store a new user

    // View, Edit, and Update Users
    Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');  // Show a specific user's details
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');  // Edit an existing user
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');  // Update the user
   
