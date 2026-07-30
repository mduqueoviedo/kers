<?php

use App\Http\Controllers\DemoDataController;
use Illuminate\Support\Facades\Route;

Route::middleware('demo.api-key')
    ->controller(DemoDataController::class)
    ->group(function (): void {
        Route::post('demo-data/reset', 'reset')->name('demo-data.reset');
    });
