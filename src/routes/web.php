<?php

use Illuminate\Support\Facades\Route;
use mpba\Modules\Http\Controllers\ModuleControlController;

Route::middleware(config('modules.admin.middleware', ['web', 'auth']))
    ->prefix(config('modules.admin.prefix', 'admin/modules'))
    ->name('modules.control.')
    ->group(function (): void {
        Route::get('/', [ModuleControlController::class, 'index'])->name('index');
        Route::post('/sync', [ModuleControlController::class, 'sync'])->name('sync');
        Route::post('/bulk', [ModuleControlController::class, 'bulk'])->name('bulk');
        Route::get('/{module}', [ModuleControlController::class, 'show'])->name('show');
        Route::put('/{module}', [ModuleControlController::class, 'update'])->name('update');
        Route::post('/{module}/enable', [ModuleControlController::class, 'enable'])->name('enable');
        Route::post('/{module}/disable', [ModuleControlController::class, 'disable'])->name('disable');
    });
