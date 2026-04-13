<?php

use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Api\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if ($user && $user->user_type === 'administrativo' && $user->estado === 'activo') {
        return redirect()->route('admin.dashboard');
    }

    if ($user) {
        auth()->guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    return redirect()->route('admin.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'store'])->name('admin.login.store');
});

Route::prefix('storefront')->group(function () {
    Route::get('/bootstrap', [StorefrontController::class, 'bootstrap']);
    Route::get('/categories/{categoryId}/products', [StorefrontController::class, 'categoryProducts']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', AdminPageController::class)->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('admin.logout');

    Route::prefix('api')->name('admin.api.')->group(function () {
        require base_path('routes/admin-api.php');
    });
});

Route::fallback(function () {
    return redirect('/');
});
