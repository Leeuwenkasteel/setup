<?php
use Illuminate\Support\Facades\Route;
use Leeuwenkasteel\Setup\Http\Controllers\SetupController;

Route::middleware(['web'])->group(function () {
   Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
	Route::get('/instructions', [SetupController::class, 'instructions'])->name('setup.instructions');
});