<?php
use Illuminate\Support\Facades\Route;
use Leeuwenkasteel\Setup\Http\Controllers\SetupController;
use Leeuwenkasteel\Setup\Http\Controllers\PackagesController;
Route::middleware(['web'])->group(function () {
   Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
	Route::get('/instructions', [SetupController::class, 'instructions'])->name('setup.instructions');
	
	Route::middleware(['cauth'])->group(function () {
        Route::prefix('admin')->group(function () {
			Route::get('git-status/{packageName}', [PackagesController::class, 'getStatus']);
			Route::get('pull/{packageName}', [PackagesController::class, 'pull']);
			Route::resource('packages', PackagesController::class);
		});
    });
});