<?php

use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Controllers\Admin\AdminController;
use App\Controllers\ProductController;
use App\Controllers\UserController;


Route::group([
    'prefix' => 'admin',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [AdminController::class, 'register']);
    Route::post("/$prefix/login", [AdminController::class, 'login']);
});

Route::group([
    'prefix' => 'products',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::get(strval($prefix), [ProductController::class, 'getAll']);
    Route::get("/$prefix/{id}", [ProductController::class, 'getProduct']);
    Route::post("/$prefix/create", [ProductController::class, 'create'], $middlewares);
    Route::delete("/$prefix/{id}/delete", [ProductController::class, 'delete'] , $middlewares);
});

Route::group([
    'prefix' => 'user',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [UserController::class, 'register']);
    Route::post("/$prefix/login", [UserController::class, 'login']);
});



