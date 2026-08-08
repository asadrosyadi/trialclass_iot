<?php

use App\Http\Controllers\Api\LedStatusController;
use App\Http\Controllers\Api\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('sensor-readings', [SensorReadingController::class, 'index'])
        ->name('sensor-readings.index');
    Route::post('sensor-readings', [SensorReadingController::class, 'store'])
        ->name('sensor-readings.store');
    Route::get('sensor-readings/latest', [SensorReadingController::class, 'latest'])
        ->name('sensor-readings.latest');

    Route::get('led', [LedStatusController::class, 'show'])->name('led.show');
    Route::put('led', [LedStatusController::class, 'update'])->name('led.update');
    Route::post('led/toggle', [LedStatusController::class, 'toggle'])->name('led.toggle');
});
