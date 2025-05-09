<?php

use App\Controllers\Frete\ShippingController;
use App\Http\Route;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "shipping",
    "middlewares" => [LockedStore::class, AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [ShippingController::class, 'getQuote'], $middlewares);
});