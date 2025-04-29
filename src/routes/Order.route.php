<?php

use App\Controllers\OrderController;
use App\Http\Route;
use App\Middlewares\AuthPermission;
use App\Middlewares\AuthUser;

Route::group([
    "prefix" => "order",
    "middlewares" => [AuthPermission::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [OrderController::class, "create"], [AuthUser::class]);
    Route::get("/$prefix/get-all/1", [OrderController::class, "getAllOrder"], $middlewares);
    Route::get("/$prefix/{param}", [OrderController::class, "getById"], $middlewares);
    Route::get("/$prefix/{param}/{param}", [OrderController::class, "getAll"], $middlewares);
});