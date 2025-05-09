<?php

use App\Controllers\AddressController;
use App\Http\Route;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    'prefix' => 'address',
    'middlewares' => [LockedStore::class, AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [AddressController::class, 'create'], $middlewares);
    Route::put("/$prefix/update", [AddressController::class, 'update'], $middlewares);
    Route::get("/$prefix", [AddressController::class, 'getAll'], $middlewares);
});