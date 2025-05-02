<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LoginController,
    RegisterAdminController
};
use App\Http\Controllers\Admin\AdminController as AdminAdminController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\BranchSelectorController as AdminBranchSelectorController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\InvestmentHistoryController as AdminInvestmentHistoryController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\PartStockController as AdminPartStockController;
use App\Http\Controllers\Admin\PartstockSaleController as AdminPartstockSaleController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductSaleController as AdminProductSaleController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Manager\CustomerController as ManagerCustomerController;
use App\Http\Controllers\Manager\InvestorController as ManagerInvestorController;
use App\Http\Controllers\Manager\ManagerController as ManagerManagerController;
use App\Http\Controllers\Manager\PartStockController as ManagerPartStockController;
use App\Http\Controllers\Manager\PartstockSaleController as ManagerPartstockSaleController;
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Http\Controllers\Manager\ProductSaleController as ManagerProductSaleController;
use App\Http\Controllers\Manager\StockController as ManagerStockController;
use App\Http\Controllers\Worker\CustomerController as WorkerCustomerController;
use App\Http\Controllers\Worker\PartStockController as WorkerPartStockController;
use App\Http\Controllers\Worker\PartStockSaleController as WorkerPartStockSaleController;
use App\Http\Controllers\Worker\ProductController as WorkerProductController;
use App\Http\Controllers\Worker\ProductSaleController as WorkerProductSaleController;
use App\Http\Controllers\Worker\StockController as WorkerStockController;
use App\Http\Controllers\Worker\WorkerController as WorkerWorkerController;

/*
|---------------------------------------------------------------------- 
| Public Routes (For login and logout) 
|---------------------------------------------------------------------- 
*/
// use App\Http\Controllers\RegisterAdminController;

Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');


// Show Login Form
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Submit Login
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|---------------------------------------------------------------------- 
| Authenticated Routes (Only for logged-in users) 
|---------------------------------------------------------------------- 
*/
Route::middleware(['auth'])->group(function () {

    // Branch Selection for Admins
    Route::get('/admin/select-branch', [AdminBranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.select-branch.set');
    Route::get('/admin/switch-branch', [AdminBranchSelectorController::class, 'switchBranch'])->name('admin.switch-branch');
    Route::post('/admin/switch-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.switch-branch.set');

    /*
    |---------------------------------------------------------------------- 
    | Admin Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminAdminController::class, 'dashboard'])->name('dashboard');
        
        // Branch & User Management
        Route::resource('branches', AdminBranchController::class);
        Route::resource('users', AdminAdminController::class);

        // Customer Management
        Route::resource('customers', AdminCustomerController::class);

        // Inventory: Products, Stocks, Part Stocks
        Route::resource('products', AdminProductController::class);
        Route::resource('stocks', AdminStockController::class);
        Route::resource('partstocks', AdminPartStockController::class);
        
        // Sales Management
        Route::resource('product-sales', AdminProductSaleController::class);
        Route::resource('partstock-sales', AdminPartstockSaleController::class);

        // Investment Management
        Route::resource('investors', AdminInvestorController::class);
        Route::resource('investment-histories', AdminInvestmentHistoryController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Manager Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('dashboard', [ManagerManagerController::class, 'dashboard'])->name('dashboard');
        
        // Branch Inventory
        Route::resource('products', ManagerProductController::class);
        Route::post('products/{product}/update-payment', [ManagerProductController::class, 'updatePayment'])->name('products.updatePayment');

        Route::resource('stocks', ManagerStockController::class);

        Route::resource('partstocks', ManagerPartStockController::class);
        Route::post('partstocks/{partstock}/update-payment', [ ManagerPartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');

        // Sales Management
        Route::resource('product-sales', ManagerProductSaleController::class);
        Route::resource('partstock-sales', ManagerPartstockSaleController::class);

        // Customer & Investor Management
        Route::resource('customers', ManagerCustomerController::class);
        Route::resource('investors', ManagerInvestorController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Worker Routes 
    |---------------------------------------------------------------------- 
    */
    Route::middleware('checkRole:worker')->prefix('worker')->name('worker.')->group(function () {
        Route::get('dashboard', [WorkerWorkerController::class, 'dashboard'])->name('dashboard');
        
        // Branch Inventory
        Route::resource('products', WorkerProductController::class);
        Route::post('products/{product}/update-payment', [ManagerProductController::class, 'updatePayment'])->name('products.updatePayment');
        Route::resource('stocks', WorkerStockController::class);
        Route::resource('partstocks', WorkerPartStockController::class);
        Route::post('partstocks/{partstock}/update-payment', [ ManagerPartStockController::class, 'updatePayment'])->name('partstocks.updatePayment');

        // Sales Management
        Route::resource('product-sales', WorkerProductSaleController::class);
        Route::resource('partstock-sales', WorkerPartStockSaleController::class);

        // Customer Management
        Route::resource('customers', WorkerCustomerController::class);
    });

    /*
    |---------------------------------------------------------------------- 
    | Common Routes (For Admin, Manager, and Worker) 
    |---------------------------------------------------------------------- 
    */
    // Can be accessed by all roles: Admin, Manager, Worker
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Other routes for Admin, Manager, and Worker here
});

// Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
// Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');
