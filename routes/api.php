<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'authenticatedUser']);
    Route::get('/users', [UserController::class, 'list']);
    Route::post('/users', [UserController::class, 'updateOrCreate']);
    Route::put('/users/bulk', [UserController::class, 'bulkUpdate']);
    Route::delete('/users/bulk', [UserController::class, 'bulkDelete']);
    Route::delete('/users/{id}', [UserController::class, 'delete'])->where('id', '[0-9]+');
    Route::get('/users/{id}/activities', [UserController::class, 'activities'])->where('id', '[0-9]+');
    
    Route::get('inventory/products', [InventoryController::class, 'listProducts']);
    Route::post('inventory/products', [InventoryController::class, 'updateOrCreateProduct']);
    Route::post('inventory/products/{id}/take', [InventoryController::class, 'takeProduct'])->where('id', '[0-9]+');
    Route::post('inventory/products/take-multiple', [InventoryController::class, 'takeProducts']);
    Route::post('inventory/products/{id}/add-stock', [InventoryController::class, 'addStock'])->where('id', '[0-9]+');
    Route::delete('inventory/products/{id}/', [InventoryController::class, 'deleteProduct'])->where('id', '[0-9]+');
    Route::put('inventory/products/bulk', [InventoryController::class, 'bulkUpdateProducts']);
    Route::delete('inventory/products/bulk', [InventoryController::class, 'bulkDeleteProducts']);
    Route::get('inventory/products/{id}/activities', [InventoryController::class, 'activities'])->where('id', '[0-9]+');
    
    Route::get('user/accesses', [AccessController::class, 'list']);
    
    Route::get('/activities', [ActivityController::class, 'activities']);
    Route::get('/activities/available-filters', [ActivityController::class, 'getAvailableActivityFilters']);
    
    // Ensure the show method exists in ImageController and the route is defined
    Route::resource('images', ImageController::class)->only(['show', 'store', 'destroy']);

    Route::get('/statistics/warnings', [StatisticsController::class, 'warnings']);
    Route::get('/statistics/total-products', [StatisticsController::class, 'totalProducts']);
    Route::get('/statistics/total-users', [StatisticsController::class, 'totalUsers']);
});
