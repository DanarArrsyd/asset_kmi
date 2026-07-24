<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterData\BrandController;
use App\Http\Controllers\MasterData\CategoryController;
use App\Http\Controllers\MasterData\DepartmentController;
use App\Http\Controllers\MasterData\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/asset/{asset}', [AssetController::class, 'show'])->name('asset.public');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/export', [AssetController::class, 'export'])->name('assets.export');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::get('/assets/{asset}/qr-print', [AssetController::class, 'qrPrint'])->name('assets.qr-print');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::get('/assets/{asset}/stock-opname/create', [StockOpnameController::class, 'create'])->name('stock-opname.create');
    Route::post('/assets/{asset}/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    foreach ([
        'categories' => CategoryController::class,
        'departments' => DepartmentController::class,
        'locations' => LocationController::class,
        'brands' => BrandController::class,
    ] as $prefix => $controller) {
        Route::get("/{$prefix}", [$controller, 'index'])->name("{$prefix}.index");
        Route::post("/{$prefix}", [$controller, 'store'])->name("{$prefix}.store");
        Route::put("/{$prefix}/{id}", [$controller, 'update'])->name("{$prefix}.update");
        Route::delete("/{$prefix}/{id}", [$controller, 'destroy'])->name("{$prefix}.destroy");
    }
});

require __DIR__.'/auth.php';
