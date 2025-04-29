<?php

use App\Controllers\Stripe\StoreController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    "prefix" => "store",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [StoreController::class, "createStore"], $middlewares);
    Route::post("/$prefix/{param}/onboarding", [StoreController::class, 'initiateOnboarding'], $middlewares);
    Route::get("/$prefix/{param}/login", [StoreController::class, 'login'], $middlewares);
});