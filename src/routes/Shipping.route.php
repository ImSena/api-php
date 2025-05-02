<?php

use App\Controllers\Frete\ShippingController;
use App\Http\Route;
use App\Middlewares\AuthUser;

Route::group([
    "prefix" => "shipping",
    "middlewares" => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [ShippingController::class, 'getQuote'], $middlewares);
});