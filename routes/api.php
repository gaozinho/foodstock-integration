<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\IfoodBrokerController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:api')->group( function () {
    Route::post('ifood-broker', [IfoodBrokerController::class, 'save']);
    Route::delete('ifood-broker/{merchant_id}', [IfoodBrokerController::class, 'delete']);
});
