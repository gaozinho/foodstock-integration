<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\IfoodBrokerController;

use App\Http\Controllers\API\IfoodOrderDispatchController;
use App\Http\Controllers\API\IfoodOrderReadyController;
use App\Http\Controllers\API\IfoodOrderCancellationController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:api')->group( function () {
    Route::post('ifood-broker/dispatch', [IfoodOrderDispatchController::class, 'dispatchOrder']);
    Route::post('ifood-broker/readyToPickup', [IfoodOrderReadyController::class, 'readyOrder']);
    Route::post('ifood-broker/requestCancellation', [IfoodOrderCancellationController::class, 'request']);
    //Route::post('ifood-broker/readyToPickup', [IfoodOrderReadyController::class, 'readyOrder']);

    Route::post('ifood-broker', [IfoodBrokerController::class, 'save']);
    Route::delete('ifood-broker/{merchant_id}', [IfoodBrokerController::class, 'delete']);
});
