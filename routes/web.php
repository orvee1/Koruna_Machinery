<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartStockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Models\Customer;
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
    Route::get('/admin/branches/{branch}', [BranchController::class, 'show'])->name('admin.branches.show');
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
   
    // Customer Routes
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');  // List all customers
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');  // Show the form to create a new customer
    Route::post('/admin/customers', [CustomerController::class, 'store'])->name('admin.customers.store');  // Store a new customer
    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');  // Show a specific customer's details
    Route::get('/admin/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');  // Edit an existing customer
    Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');  // Update the customer

    // Product Routes
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');  // List all products
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');  // Show the form to create a new product
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');  // Store a new product
    Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');  // Show a specific product's details
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');  // Edit an existing product
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');  // Update the product
    Route::post('products/{product}/update-payment', [ProductController::class, 'updatePayment'])->name('admin.products.updatePayment');
    // Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('admin.products.adjustStock');

    // Stock Routes
    Route::get('/admin/stocks', [StockController::class, 'index'])->name('admin.stocks.index');  // List all stock entries
    Route::get('/admin/stocks/create', [StockController::class, 'create'])->name('admin.stocks.create');  // Show the form to create a new stock entry
    Route::post('/admin/stocks', [StockController::class, 'store'])->name('admin.stocks.store');  // Store a new stock entry
    Route::get('/admin/stocks/{stock}', [StockController::class, 'show'])->name('admin.stocks.show');  // Show a specific stock entry's details
    Route::get('/admin/stocks/{stock}/edit', [StockController::class, 'edit'])->name('admin.stocks.edit');  // Edit an existing stock entry
    Route::put('/admin/stocks/{stock}', [StockController::class, 'update'])->name('admin.stocks.update');  // Update the stock entry
    Route::delete('/admin/stocks/{stock}', [StockController::class, 'destroy'])->name('admin.stocks.destroy');  // Delete a stock entry

    // Part Stock Routes
    Route::get('/admin/partstocks', [PartStockController::class, 'index'])->name('admin.partstocks.index');  // List all part stock entries
    Route::get('/admin/partstocks/create', [PartStockController::class, 'create'])->name('admin.partstocks.create');  // Show the form to create a new part stock entry
    Route::post('/admin/partstocks', [PartStockController::class, 'store'])->name('admin.partstocks.store');  // Store a new part stock entry
    Route::get('/admin/partstocks/{partStock}', [PartStockController::class, 'show'])->name('admin.partstocks.show');  // Show a specific part stock entry's details
    Route::get('/admin/partstocks/{partStock}/edit', [PartStockController::class, 'edit'])->name('admin.partstocks.edit');  // Edit an existing part stock entry
    Route::put('/admin/partstocks/{partStock}', [PartStockController::class, 'update'])->name('admin.partstocks.update');  // Update the part stock entry