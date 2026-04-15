<?php

use App\Http\Controllers\ApiEvaluatorController;
use Illuminate\Support\Facades\Route;

Route::middleware('api-token')->prefix('v1')->group(function () {
    Route::get('/models', [ApiEvaluatorController::class, 'models']);
    Route::get('/listings', [ApiEvaluatorController::class, 'listings']);
    Route::post('/evaluate', [ApiEvaluatorController::class, 'evaluate']);
});
