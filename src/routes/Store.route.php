<?php

use App\Controllers\StoreController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    "prefix" => "store",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [StoreController::class, "createStore"], $middlewares);
    Route::post("/$prefix/onboarding", [StoreController::class, 'initiateOnboarding'], $middlewares);
    Route::get("/$prefix/status", [StoreController::class, 'getStatus']);
    Route::get("/$prefix/login", [StoreController::class, 'login'], $middlewares);
    Route::get("/$prefix", [StoreController::class, 'getAssets']);
});