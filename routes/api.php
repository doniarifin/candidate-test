<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\LayupController;
use App\Http\Controllers\Api\LayerController;
use App\Http\Controllers\Api\ImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// api suppliers
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
Route::post('/suppliers', [SupplierController::class, 'store']);
Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

Route::get('/suppliers', [SupplierController::class, 'search']);

//export suppliers
Route::post('/suppliers/export', [SupplierController::class, 'export']);

//layup api
Route::get('/layups', [LayupController::class, 'index']);
Route::get('/layups/{id}', [LayupController::class, 'show']);
Route::post('/layups', [LayupController::class, 'store']);
Route::put('/layups/{id}', [LayupController::class, 'update']);
Route::delete('/layups/{id}', [LayupController::class, 'destroy']);

Route::get('/supplier/{id}/layups', [LayupController::class, 'getLayups']);

//export layups
Route::post('/layups/export', [LayupController::class, 'export']);

//layer api
Route::get('/layers', [LayerController::class, 'index']);
Route::get('/layers/{id}', [LayerController::class, 'show']);
Route::post('/layers', [LayerController::class, 'store']);
Route::put('/layers/{id}', [LayerController::class, 'update']);
Route::delete('/layers/{id}', [LayerController::class, 'destroy']);

//export layers
Route::post('/layers/export', [LayerController::class, 'export']);

//import
Route::post('/layups/import/preview', [ImportController::class, 'preview']);
Route::post('/layups/import/', [ImportController::class, 'import']);

