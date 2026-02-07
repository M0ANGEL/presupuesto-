<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\FixedExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SettingsController;
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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    
    // Gastos
    Route::prefix('expenses')->group(function () {
        Route::get('/create', [ExpenseController::class, 'create'])
            ->name('expenses.create');
        Route::post('/', [ExpenseController::class, 'store'])
            ->name('expenses.store');
        Route::get('/', [ExpenseController::class, 'index'])
            ->name('expenses.index');
        Route::get('/{expense}', [ExpenseController::class, 'show'])
            ->name('expenses.show');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
            ->name('expenses.destroy');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])
            ->name('expenses.edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])
            ->name('expenses.update');
    });
    
    // Ingresos
    Route::prefix('incomes')->group(function () {
        Route::get('/create', [IncomeController::class, 'create'])
            ->name('incomes.create');
        Route::post('/', [IncomeController::class, 'store'])
            ->name('incomes.store');
        Route::get('/', [IncomeController::class, 'index'])
            ->name('incomes.index');
        Route::get('/{income}', [IncomeController::class, 'show'])
            ->name('incomes.show');
        Route::delete('/{income}', [IncomeController::class, 'destroy'])
            ->name('incomes.destroy');
        Route::get('/{income}/edit', [IncomeController::class, 'edit'])
            ->name('incomes.edit');
        Route::put('/{income}', [IncomeController::class, 'update'])
            ->name('incomes.update');
        Route::post('/{income}/pay', [IncomeController::class, 'markAsPaid'])
            ->name('incomes.pay');
        Route::post('/{income}/unpay', [IncomeController::class, 'markAsUnpaid'])
            ->name('incomes.unpay');
    });
    
    // Gastos Fijos
    Route::prefix('fixed-expenses')->group(function () {
        Route::get('/create', [FixedExpenseController::class, 'create'])
            ->name('fixed-expenses.create');
        Route::post('/', [FixedExpenseController::class, 'store'])
            ->name('fixed-expenses.store');
        Route::get('/', [FixedExpenseController::class, 'index'])
            ->name('fixed-expenses.index');
        Route::get('/{fixedExpense}', [FixedExpenseController::class, 'show'])
            ->name('fixed-expenses.show');
        Route::delete('/{fixedExpense}', [FixedExpenseController::class, 'destroy'])
            ->name('fixed-expenses.destroy');
        Route::get('/{fixedExpense}/edit', [FixedExpenseController::class, 'edit'])
            ->name('fixed-expenses.edit');
        Route::put('/{fixedExpense}', [FixedExpenseController::class, 'update'])
            ->name('fixed-expenses.update');
        Route::post('/{fixedExpense}/pay', [FixedExpenseController::class, 'markAsPaid'])
            ->name('fixed-expenses.pay');
        Route::post('/{fixedExpense}/unpay', [FixedExpenseController::class, 'markAsUnpaid'])
            ->name('fixed-expenses.unpay');
    });
    
    // Categorías
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');
        Route::get('/create', [CategoryController::class, 'create'])
            ->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])
            ->name('categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');
    });
    
    // Wallet (Billetera)
    Route::prefix('wallet')->group(function () {
        Route::post('/saving', [WalletController::class, 'moveToSaving'])
            ->name('wallet.saving');
        Route::post('/personal', [WalletController::class, 'moveToPersonal'])
            ->name('wallet.personal');
        Route::post('/stock', [WalletController::class, 'moveToStock'])
            ->name('wallet.stock');
        Route::post('/transfer', [WalletController::class, 'transferBetweenAccounts'])
            ->name('wallet.transfer');
        Route::get('/history', [WalletController::class, 'history'])
            ->name('wallet.history');
        Route::get('/adjust', [WalletController::class, 'showAdjustForm'])
            ->name('wallet.adjust');
        Route::post('/adjust', [WalletController::class, 'adjustBalance'])
            ->name('wallet.adjust.store');
    });
    
    // Transacciones - SOLUCIÓN SIMPLIFICADA
    // ELIMINA la ruta /filter y usa solo index con parámetros GET
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])
            ->name('transactions.index');
        // Route::get('/filter', [TransactionController::class, 'filter']) // ELIMINAR ESTA
        //     ->name('transactions.filter');
        Route::get('/{transaction}', [TransactionController::class, 'show'])
            ->name('transactions.show');
        Route::get('/export', [TransactionController::class, 'export'])
            ->name('transactions.export');
    });
    
    // Reportes (puedes mantenerlas o eliminarlas si no las usas)
    Route::prefix('reports')->group(function () {
        Route::get('/', function () {
            return view('reports.index');
        })->name('reports.index');
        
        Route::get('/monthly', function () {
            return view('reports.monthly');
        })->name('reports.monthly');
        
        Route::get('/categories', function () {
            return view('reports.categories');
        })->name('reports.categories');
        
        Route::get('/fixed-vs-variable', function () {
            return view('reports.fixed-vs-variable');
        })->name('reports.fixed-vs-variable');
    });
    
    // Configuración
   // Configuración
Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])
        ->name('settings.index');
    
    Route::put('/profile', [SettingsController::class, 'updateProfile'])
        ->name('settings.profile.update');
    
    // Categorías
    Route::get('/categories', [SettingsController::class, 'categories'])
        ->name('settings.categories');
    Route::post('/categories', [SettingsController::class, 'storeCategory'])
        ->name('settings.categories.store');
    Route::put('/categories/{category}', [SettingsController::class, 'updateCategory'])
        ->name('settings.categories.update');
    Route::delete('/categories/{category}', [SettingsController::class, 'destroyCategory'])
        ->name('settings.categories.destroy');
    
    // Notificaciones
    Route::get('/notifications', [SettingsController::class, 'notifications'])
        ->name('settings.notifications');
    Route::post('/notifications', [SettingsController::class, 'updateNotifications'])
        ->name('settings.notifications.update');
    
    // Backup
    Route::get('/backup', [SettingsController::class, 'backup'])
        ->name('settings.backup');
});
    
    // Rutas de API para AJAXß
    Route::prefix('api')->group(function () {
        Route::get('/monthly-summary', [DashboardController::class, 'monthlySummary'])
            ->name('api.monthly-summary');
        
        Route::get('/expenses-by-category', [DashboardController::class, 'expensesByCategory'])
            ->name('api.expenses-by-category');
        
        Route::get('/income-expense-chart', [DashboardController::class, 'incomeExpenseChart'])
            ->name('api.income-expense-chart');
        
        Route::get('/quick-stats', [DashboardController::class, 'quickStats'])
            ->name('api.quick-stats');
    });
});

require __DIR__.'/auth.php';