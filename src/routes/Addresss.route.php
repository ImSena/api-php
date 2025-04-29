<?php

use App\Controllers\AddressController;
use App\Http\Route;
use App\Middlewares\AuthUser;

Route::group([
    'prefix' => 'address',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [AddressController::class, 'create'], $middlewares);
    Route::put("/$prefix/update", [AddressController::class, 'update'], $middlewares);
    Route::get("/$prefix", [AddressController::class, 'getAll'], $middlewares);
});