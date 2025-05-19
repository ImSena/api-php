<?php

use App\Controllers\UserController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Middlewares\LockedStore;

Route::group([
    'prefix' => 'user',
    'middlewares' => [LockedStore::class, AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [UserController::class, 'register'], [LockedStore::class]);
    Route::post("/$prefix/login", [UserController::class, 'login'], [LockedStore::class]);
    Route::post("/$prefix/forget-password", [UserController::class, 'forgetAccess'], [LockedStore::class]);
    Route::put("/$prefix/reset-password", [UserController::class, 'resetPassword'], [LockedStore::class]);

    Route::post("/$prefix/send-active-account", [UserController::class, 'sendActiveUser'], [LockedStore::class]);
    Route::put("/$prefix/active-account", [UserController::class, 'activeAccount'], [LockedStore::class]);
    Route::put("/$prefix", [UserController::class, "edit"], $middlewares);
    Route::get("/$prefix/{param}", [UserController::class, 'getAll'], [LockedStore::class, AuthAdmin::class]);
    Route::get("/$prefix/inactive/{param}", [UserController::class, 'getAllInactive'], [LockedStore::class, AuthAdmin::class]);
});