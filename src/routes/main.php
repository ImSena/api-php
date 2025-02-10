<?php

use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Controllers\Admin\AdminController;
use App\Controllers\CategoriesController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use App\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index']);

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
});

Route::group([
    "prefix" => "categories",
    'middlewares'=> [AuthAdmin::class]
],function($prefix, $middlewares){
    Route::post("/$prefix/create", [CategoriesController::class, 'createCategories'], $middlewares);
    Route::delete("/$prefix/delete", [CategoriesController::class, 'deleteCategory'], $middlewares);
    Route::put("/$prefix/update-category", [CategoriesController::class, "updateCategory"], $middlewares);
    Route::get("/$prefix/get-parents", [CategoriesController::class, "getAllParent"]);
    Route::get("/$prefix/get-categories", [CategoriesController::class, "getCategories"]);
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




