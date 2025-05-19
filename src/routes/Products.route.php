<?php

use App\Controllers\ProductController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\LockedStore;

Route::group([
    'prefix' => 'products',
    'middlewares' => [LockedStore::class, AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [ProductController::class, 'create'], $middlewares);
    // Route::get("/$prefix/{param}", [ProductController::class, 'getProduct']);
    //primeiro parâmetro seria o que gostaria de buscar (por categoria, marca...), segundo é o id e o terceiro a pagina para o offset
    Route::get("/$prefix/get-all-by/{param}/{param}/{param}", [ProductController::class, 'getAllBy'], [LockedStore::class]);
    Route::get("/$prefix/get-by-id/{param}", [ProductController::class, 'getById'], [LockedStore::class]);
    Route::get("/$prefix/search/{param}", [ProductController::class, 'search']);
    Route::get("/$prefix/{param}", [ProductController::class, 'getAll'], [LockedStore::class]);
    Route::put("/$prefix/{param}", [ProductController::class, "update"], $middlewares);
    // Route::delete("/$prefix", [ProductController::class, 'delete'] , $middlewares);
});