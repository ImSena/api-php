<?php

use App\Controllers\PaymentsController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "payments",
    "middlewares" => [LockedStore::class, AuthUser::class]
], function($prefix, $middlewares){
    Route::get("/$prefix/pay/{param}", [PaymentsController::class, "pay"], $middlewares);
    Route::get("/$prefix", [PaymentsController::class, 'getPayments'], $middlewares);
    Route::get("/$prefix/report", [PaymentsController::class, 'getReport'], [AuthAdmin::class]);
    Route::get("/$prefix/{param}", [PaymentsController::class, 'getDetails'], $middlewares);
});