<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'authenticatedUser']);
    Route::get('/users', [UserController::class, 'list']);
    Route::post('/users', [UserController::class, 'updateOrCreate']);
    Route::put('/users/bulk', [UserController::class, 'bulkUpdate']);
    Route::delete('/users/bulk', [UserController::class, 'bulkDelete']);
    Route::delete('/users/{id}', [UserController::class, 'delete'])->where('id', '[0-9]+');

    Route::get('inventory', [InventoryController::class, 'list']);

    Route::get('user/accesses', [AccessController::class, 'list']);
});
