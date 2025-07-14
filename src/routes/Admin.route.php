<?php

use App\Controllers\Admin\AdminController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    'prefix' => 'admin',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    //rotas de crud
    Route::post("/$prefix/register", [AdminController::class, 'registerSuper']);
    //para criar superadmin basta descomentar
    // Route::post("/$prefix/register", [AdminController::class, "register"], $middlewares);
    Route::post("/$prefix/login", [AdminController::class, 'login']);
    Route::post("/$prefix/forget-password", [AdminController::class, 'forgetAccess']);
    Route::put("/$prefix/reset-password", [AdminController::class, 'resetPassword']);

    //ativar conta
    Route::post("/$prefix/send-active-account",[AdminController::class, 'sendActiveAdmin']);
    Route::put("/$prefix/active-account", [AdminController::class, 'activeAccount']);
});