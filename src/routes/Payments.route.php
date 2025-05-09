<?php

use App\Controllers\PaymentsController;
use App\Http\Route;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "payments",
    "middlewares" => [LockedStore::class, AuthUser::class]
], function($prefix, $middlewares){
    Route::get("/$prefix/pay/{param}", [PaymentsController::class, "pay"], $middlewares);
    Route::get("/$prefix", [PaymentsController::class, 'getPayments'], $middlewares);
    Route::get("/$prefix/{param}", [PaymentsController::class, 'getDetails'], $middlewares);
});