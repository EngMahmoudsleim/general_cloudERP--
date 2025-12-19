<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->prefix('inventory-reset')->name('inventory-reset.')->group(callback: function () {

    // Main interface
    Route::get('/', 'InventoryResetController@index')->name('index');

    // Execute Reset
    Route::post('/reset/execute', 'InventoryResetController@executeReset')->name('execute-reset');

    // Reset Mapping - simplified reset endpoint
    Route::post('/reset-mapping', 'InventoryResetController@resetMapping')->name('reset-mapping');

    // View Reset Details
    Route::get('/reset/{id}', 'InventoryResetController@showReset')->name('show-reset');

    // AJAX endpoints
    Route::get('/summary', 'InventoryResetController@getSummary')->name('summary');
    Route::get('/locations/{business_id}', 'InventoryResetController@getLocations')->name('locations');
    Route::get('/search-products', 'InventoryResetController@searchProducts')->name('search-products');
    Route::get('/negative-products', 'InventoryResetController@getNegativeInventoryProducts')->name('negative-products');

});

// Installation routes
Route::middleware(['web', 'auth'])->prefix('inventory-reset/install')->name('inventory-reset.install.')->group(function () {
    Route::get('/', 'InstallController@index')->name('index');
    Route::get('/update', 'InstallController@update')->name('update');
    Route::get('/uninstall', 'InstallController@uninstall')->name('uninstall');
});