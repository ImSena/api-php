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
    // Route::get("/$prefix/stripe", [StoreController::class, "getStripe"]);
    Route::get("/$prefix/login", [StoreController::class, 'login'], $middlewares);
    Route::get("/$prefix", [StoreController::class, 'getAssets']);

    Route::put("/$prefix", [StoreController::class, 'updateStore'], $middlewares);
    Route::put("/$prefix/theme", [StoreController::class, 'updateTheme'], $middlewares);
    Route::put("/$prefix/address/{param}", [StoreController::class, 'updateAddress'], $middlewares);
    Route::put("/$prefix/phone/{param}", [StoreController::class, "updatePhone"], $middlewares);
    Route::put("/$prefix/email/{param}", [StoreController::class, 'updateEmail'], $middlewares);
    Route::put("/$prefix/sociais", [StoreController::class, "updateSociais"], $middlewares);
});