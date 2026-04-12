<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryMovementController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SaleNoteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::apiResource('categories', CategoryController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);

Route::get('products/{product}/movements', [InventoryMovementController::class, 'byProduct']);
Route::post('products/{product}/images', [ProductImageController::class, 'store']);
Route::put('products/{product}/images/{productImage}', [ProductImageController::class, 'update']);
Route::delete('products/{product}/images/{productImage}', [ProductImageController::class, 'destroy']);

Route::get('inventory-movements', [InventoryMovementController::class, 'index']);
Route::post('inventory-movements', [InventoryMovementController::class, 'store']);

Route::apiResource('sales', SaleController::class)->only(['index', 'store', 'show']);
Route::patch('sales/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus']);
Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel']);
Route::post('sales/{sale}/notes', [SaleNoteController::class, 'store']);
