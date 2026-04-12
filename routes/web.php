<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LayupController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('suppliers', SupplierController::class)
    ->middleware(['auth', 'verified']);

Route::resource('layups', LayupController::class)
    ->middleware(['auth', 'verified']);

// Route::get('/layups/under-supplier', [LayupController::class, 'underSupplier'])->middleware(['auth', 'verified']);
Route::get('/supplier/{id}/layups', [LayupController::class, 'underSupplier'])->middleware(['auth', 'verified']);

// Route::get('/layup', function () {
//     return view('pages.layup.layup');
// })->middleware(['auth', 'verified'])->name('layup');
Route::get('/layer', function () {
    return view('pages.layer.layer');
})->middleware(['auth', 'verified'])->name('layer');

require __DIR__.'/auth.php';
