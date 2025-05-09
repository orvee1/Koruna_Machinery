<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterAdminController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController as AdminAdminController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\BranchSelectorController as AdminBranchSelectorController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\InvestmentHistoryController as AdminInvestmentHistoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductSaleController as AdminProductSaleController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\PartStockController as AdminPartStockController;
use App\Http\Controllers\Admin\PartstockSaleController as AdminPartstockSaleController;

// Manager Controllers
use App\Http\Controllers\Manager\ManagerController as ManagerManagerController;
use App\Http\Controllers\Manager\CustomerController as ManagerCustomerController;
use App\Http\Controllers\Manager\InvestorController as ManagerInvestorController;
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Http\Controllers\Manager\ProductSaleController as ManagerProductSaleController;
use App\Http\Controllers\Manager\StockController as ManagerStockController;
use App\Http\Controllers\Manager\PartStockController as ManagerPartStockController;
use App\Http\Controllers\Manager\PartstockSaleController as ManagerPartstockSaleController;

// Worker Controllers
use App\Http\Controllers\Worker\WorkerController as WorkerWorkerController;
use App\Http\Controllers\Worker\CustomerController as WorkerCustomerController;
use App\Http\Controllers\Worker\ProductController as WorkerProductController;
use App\Http\Controllers\Worker\ProductSaleController as WorkerProductSaleController;
use App\Http\Controllers\Worker\StockController as WorkerStockController;
use App\Http\Controllers\Worker\PartStockController as WorkerPartStockController;
use App\Http\Controllers\Worker\PartStockSaleController as WorkerPartStockSaleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Registration for Admin
Route::get('/register-admin', [RegisterAdminController::class, 'showForm'])->name('register-admin');
Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('register-admin.store');

// Authentication
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Branch Selection for Admins
    Route::get('/admin/select-branch', [AdminBranchSelectorController::class, 'show'])->name('admin.select-branch');
    Route::post('/admin/select-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.select-branch.set');
    Route::get('/admin/switch-branch', [AdminBranchSelectorController::class, 'switchBranch'])->name('admin.switch-branch');
    Route::post('/admin/switch-branch', [AdminBranchSelectorController::class, 'set'])->name('admin.switch-branch.set');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware(['checkRole:admin', 'ensureAdminBranchSelected'])
        ->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('dashboard', [AdminAdminController::class, 'dashboard'])->name('dashboard');

            // Branch & User Management
            Route::resource('branches', AdminBranchController::class);
            // Assuming a dedicated UserController exists; adjust if needed.
            Route::resource('users', AdminAdminController::class);
            Route::get('admin/users/{user}', [AdminAdminController::class, 'show'])->name('admin.users.show');


            // Customer Management
            Route::resource('customers', AdminCustomerController::class);

            // Inventory: Products
            Route::resource('products', AdminProductController::class);
            Route::post('products/{product}/update-payment', [AdminProductController::class, 'updatePayment'])
                ->name('products.updatePayment');

            // Inventory: Stocks
            Route::resource('stocks', AdminStockController::class);
            Route::post('stocks/{stock}/update-payment', [AdminStockController::class, 'updatePayment'])
                ->name('stocks.updatePayment');

            // Inventory: Part Stocks
            Route::resource('partstocks', AdminPartStockController::class);
            Route::post('partstocks/{partstock}/update-payment', [AdminPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Sales Management
            Route::resource('product-sales', AdminProductSaleController::class);
            Route::resource('partstock-sales', AdminPartstockSaleController::class);

            // Investment Management
            Route::resource('investors', AdminInvestorController::class);
            Route::resource('investment-histories', AdminInvestmentHistoryController::class);
        });

    /*
    |--------------------------------------------------------------------------
    | Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('manager')
        ->middleware('checkRole:manager')
        ->name('manager.')
        ->group(function () {
            Route::get('dashboard', [ManagerManagerController::class, 'dashboard'])->name('dashboard');

            // Branch Inventory
            Route::resource('products', ManagerProductController::class);
            Route::post('products/{product}/update-payment', [ManagerProductController::class, 'updatePayment'])
                ->name('products.updatePayment');

            Route::resource('stocks', ManagerStockController::class);

            Route::resource('partstocks', ManagerPartStockController::class);
            Route::post('partstocks/{partstock}/update-payment', [ManagerPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Sales Management
            Route::resource('product-sales', ManagerProductSaleController::class);
            Route::resource('partstock-sales', ManagerPartstockSaleController::class);

            // Customer & Investor Management
            Route::resource('customers', ManagerCustomerController::class);
            Route::resource('investors', ManagerInvestorController::class);
        });

    /*
    |--------------------------------------------------------------------------
    | Worker Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('worker')
        ->middleware('checkRole:worker')
        ->name('worker.')
        ->group(function () {
            Route::get('dashboard', [WorkerWorkerController::class, 'dashboard'])->name('dashboard');

            // Branch Inventory
            Route::resource('products', WorkerProductController::class);
            Route::post('products/{product}/update-payment', [WorkerProductController::class, 'updatePayment'])
                ->name('products.updatePayment');

            Route::resource('stocks', WorkerStockController::class);

            Route::resource('partstocks', WorkerPartStockController::class);
            Route::post('partstocks/{partstock}/update-payment', [WorkerPartStockController::class, 'updatePayment'])
                ->name('partstocks.updatePayment');

            // Sales Management
            Route::resource('product-sales', WorkerProductSaleController::class);
            Route::resource('partstock-sales', WorkerPartStockSaleController::class);

            // Customer Management
            Route::resource('customers', WorkerCustomerController::class);
        });

});
