<?php

use App\Controllers\UserController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;

Route::group([
    'prefix' => 'user',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [UserController::class, 'register']);
    Route::post("/$prefix/login", [UserController::class, 'login']);
    Route::post("/$prefix/forget-password", [UserController::class, 'forgetAccess']);
    Route::put("/$prefix/reset-password", [UserController::class, 'resetPassword']);

    Route::post("/$prefix/send-active-account", [UserController::class, 'sendActiveUser']);
    Route::put("/$prefix/active-account", [UserController::class, 'activeAccount']);
    Route::get("/$prefix/{param}", [UserController::class, 'getAll'], [AuthAdmin::class]);
    Route::get("/$prefix/inactive/{param}", [UserController::class, 'getAllInactive'], [AuthAdmin::class]);
});