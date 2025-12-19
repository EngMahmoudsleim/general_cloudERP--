<?php
/*
|--------------------------------------------------------------------------
| ExchangeItem Module Web Routes
|--------------------------------------------------------------------------
|
| This file is located at: Modules/ExchangeItem/Routes/web.php
|
*/

use Modules\ExchangeItem\Http\Controllers\ExchangeController;
use Modules\ExchangeItem\Http\Controllers\InstallController;

/*
|--------------------------------------------------------------------------
| Module Installation Routes
|--------------------------------------------------------------------------
| These routes use minimal middleware to avoid accessing exchangeitem tables
| before they are created during installation.
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('exchangeitem')->name('exchangeitem.')->group(function () {
        // Installation routes - minimal middleware to avoid table access issues
        Route::get('/install', [InstallController::class, 'index'])->name('install');
        Route::get('/install/update', [InstallController::class, 'update'])->name('install.update');
        Route::get('/install/uninstall', [InstallController::class, 'uninstall'])->name('install.uninstall');
    });
});

/*
|--------------------------------------------------------------------------
| Main ExchangeItem Routes
|--------------------------------------------------------------------------
| These routes are only loaded if the ExchangeItem module is installed.
| This prevents middleware from accessing non-existent tables.
*/
Route::middleware([
    'web',              // Web middleware group
    'auth',             // Standard authentication
    'SetSessionData',   // Set session data for POS
    'language',         // Language localization
    'timezone',         // Timezone handling
    'AdminSidebarMenu'  // Admin sidebar menu setup
])->group(function () {

    // Only load these routes if the ExchangeItem module is installed
    if (\App\System::getProperty('ExchangeItem_version')) {

        Route::prefix('exchangeitem')->name('exchangeitem.')->group(function () {

            // Main ExchangeItem Pages
            Route::get('/', [ExchangeController::class, 'index'])->name('index');
            Route::get('/create', [ExchangeController::class, 'create'])->name('create');

            // Store route - ensure this matches your JavaScript AJAX calls
            Route::post('/store', [ExchangeController::class, 'store'])->name('store');
            Route::post('/', [ExchangeController::class, 'store'])->name('store_alias');

            // AJAX/API Routes (specific routes before parameterized ones)
            Route::get('/list', [ExchangeController::class, 'getExchanges'])->name('list');
            Route::post('/search-transaction', [ExchangeController::class, 'searchTransaction'])->name('search_transaction');

            // Individual Exchange Routes (parameterized routes last to avoid conflicts)
            Route::get('/{id}', [ExchangeController::class, 'show'])->name('show');

            // Print routes
            Route::get('/{id}/print', [ExchangeController::class, 'printReceipt'])->name('print');
            Route::get('/{id}/print-receipt', [ExchangeController::class, 'printReceipt'])->name('print-receipt');
            Route::get('/{id}/print-only', [ExchangeController::class, 'printReceiptOnly'])->name('print-only');

            // Management routes
            Route::post('/{id}/cancel', [ExchangeController::class, 'cancel'])->name('cancel');
            Route::delete('/{id}', [ExchangeController::class, 'destroy'])->name('destroy');
        });
    }
});

