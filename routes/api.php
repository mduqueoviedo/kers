<?php

use App\Http\Controllers\DemoDataController;
use Illuminate\Support\Facades\Route;

Route::middleware('demo.api-key')
    ->controller(DemoDataController::class)
    ->group(function (): void {
        Route::delete('demo-data', 'destroy')->name('demo-data.destroy');
        Route::post('demo-data/seed', 'seed')->name('demo-data.seed');
    });
