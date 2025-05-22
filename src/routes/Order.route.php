<?php

use App\Controllers\OrderController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthPermission;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "order",
    "middlewares" => [LockedStore::class, AuthPermission::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [OrderController::class, "create"], [LockedStore::class, AuthUser::class]);
    Route::post("/$prefix/change-status/{param}", [OrderController::class, "changeStatus"], [LockedStore::class, AuthAdmin::class]);
    Route::get("/$prefix/get-quantity-status", [OrderController::class, "getQtdOrderStatus"], [AuthAdmin::class]);
    Route::get("/$prefix/{param}", [OrderController::class, "getById"], $middlewares);
    Route::get("/$prefix/{param}/{param}", [OrderController::class, "getAll"], $middlewares);
});