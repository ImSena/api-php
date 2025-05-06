<?php

use App\Controllers\ProductController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    'prefix' => 'products',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [ProductController::class, 'create'], $middlewares);
    // Route::get("/$prefix/{param}", [ProductController::class, 'getProduct']);
    //primeiro parâmetro seria o que gostaria de buscar (por categoria, marca...), segundo é o id e o terceiro a pagina para o offset
    Route::get("/$prefix/get-all-by/{param}/{param}/{param}", [ProductController::class, 'getAllBy']);
    Route::get("/$prefix/get-by-id/{param}", [ProductController::class, 'getById']);
    Route::get("/$prefix/{param}", [ProductController::class, 'getAll']);
    Route::put("/$prefix/{param}", [ProductController::class, "update"], $middlewares);
    // Route::delete("/$prefix", [ProductController::class, 'delete'] , $middlewares);
});