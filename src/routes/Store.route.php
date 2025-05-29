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
    Route::post("/$prefix/address", [StoreController::class, 'insertAddressStore'], $middlewares);
    Route::post("/$prefix/email", [StoreController::class, "insertEmail"], $middlewares);
    Route::post("/$prefix/phone", [StoreController::class, "insertPhone"], $middlewares);
    Route::post("/$prefix/social", [StoreController::class, "insertSocial"]);
    Route::get("/$prefix/status", [StoreController::class, 'getStatus']);
    // Route::get("/$prefix/stripe", [StoreController::class, "getStripe"]);
    Route::get("/$prefix/login", [StoreController::class, 'login'], $middlewares);
    Route::get("/$prefix/stripe", [StoreController::class, "activeAccountStripe"], $middlewares);
    Route::get("/$prefix", [StoreController::class, 'getAssets']);

    Route::put("/$prefix", [StoreController::class, 'updateStore'], $middlewares);
    Route::put("/$prefix/theme", [StoreController::class, 'updateTheme'], $middlewares);
    Route::put("/$prefix/address/{param}", [StoreController::class, 'updateAddress'], $middlewares);
    Route::put("/$prefix/phone/{param}", [StoreController::class, "updatePhone"], $middlewares);
    Route::put("/$prefix/email/{param}", [StoreController::class, 'updateEmail'], $middlewares);
    Route::put("/$prefix/social/{param}", [StoreController::class, "updateSocial"]);

    Route::delete("/$prefix/address/{param}", [StoreController::class, "deleteAddress"], $middlewares);
    Route::delete("/$prefix/email/{param}", [StoreController::class, "deleteEmail"], $middlewares);
    Route::delete("/$prefix/phone/{param}", [StoreController::class, "deletePhone"], $middlewares);
    Route::delete("/$prefix/social/{param}", [StoreController::class, "deleteSocial"]);
});