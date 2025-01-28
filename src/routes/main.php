<?php

use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Controllers\Admin\AdminController;
use App\Controllers\CategoriesController;
use App\Controllers\ProductController;
use App\Controllers\UserController;


Route::get('/', [UserController::class, 'teste']);

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

    //GET DEVEM ESTAR NO FINAL
    Route::get("/$prefix/{id}", [AdminController::class, 'register']);
});

Route::group([
    "prefix" => "categories",
    'middlewares'=> [AuthAdmin::class]
],function($prefix, $middlewares){
    Route::post("/$prefix/create", [CategoriesController::class, 'createCategories'], $middlewares);
    Route::delete("/$prefix/delete", [CategoriesController::class, 'deleteCategory'], $middlewares);
    Route::get("/$prefix/getParents", [CategoriesController::class, "getAllParent"], $middlewares);
});

Route::group([
    'prefix' => 'products',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::get(strval($prefix), [ProductController::class, 'getAll']);
    Route::post("/$prefix/create", [ProductController::class, 'create'], $middlewares);
    Route::delete("/$prefix", [ProductController::class, 'delete'] , $middlewares);
    Route::get("/$prefix/{id}", [ProductController::class, 'getProduct']);
});

Route::group([
    'prefix' => 'user',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [UserController::class, 'register']);
    Route::post("/$prefix/login", [UserController::class, 'login']);
});




