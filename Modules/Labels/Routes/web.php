<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::prefix('labels')->group(function() {
//     Route::get('/', 'LabelsController@index');
//            //Install Controller
//         Route::get('/install', [Modules\Labels\Http\Controllers\InstallController::class, 'index']);
//         Route::get('/install/update', [Modules\Labels\Http\Controllers\InstallController::class, 'update']);
//         Route::get('/install/uninstall', [Modules\Labels\Http\Controllers\InstallController::class, 'uninstall']);
//         Route::get('/labels/enhanced-show', [LabelsController::class, 'enhancedShow'])->name('labels.enhanced-show');
//         Route::get('/labels/enhanced-preview', [LabelsController::class, 'enhancedPreview'])->name('labels.enhanced-preview');

// });


Route::middleware('web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu')->group(function () {
 
Route::prefix('labels')->group(function() {
        
        Route::get('/', 'LabelsController@index');

        // Enhanced Print Labels Routes
        Route::get('/enhanced-show', [Modules\Labels\Http\Controllers\LabelsController::class, 'enhancedShow'])->name('labels.enhanced-show');
        Route::get('/enhanced-preview', [Modules\Labels\Http\Controllers\LabelsController::class, 'enhancedPreview'])->name('labels.enhanced-preview');

           //Install Controller
        Route::get('/install', [Modules\Labels\Http\Controllers\InstallController::class, 'index']);
        Route::get('/install/update', [Modules\Labels\Http\Controllers\InstallController::class, 'update']);
        Route::get('/install/uninstall', [Modules\Labels\Http\Controllers\InstallController::class, 'uninstall']);
        
    });
    
});
