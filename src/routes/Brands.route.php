<?php

use App\Controllers\BrandController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    "prefix" => "brands",
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [BrandController::class, 'create'], $middlewares);
    Route::delete("/$prefix/{param}", [BrandController::class, 'delete'], $middlewares);
    Route::put("/$prefix/{param}", [BrandController::class, "update"], $middlewares);
    Route::get("/$prefix", [BrandController::class, "getAll"]);
});