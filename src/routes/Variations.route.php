<?php

use App\Controllers\VariantsController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "variations",
    "middlewares" => [LockedStore::class, AuthAdmin::class]
], function($prefix, $middlewares){
    //variações
    Route::post("/$prefix/create-variation", [VariantsController::class, "createVariant"], $middlewares);
    Route::get("/$prefix/get-variations", [VariantsController::class, "getAllVariation"]);
    Route::put("/$prefix/variation/{param}", [VariantsController::class, "updateVariation"], $middlewares);
    Route::delete("/$prefix/variation/{param}", [VariantsController::class, "deleteVariation"], $middlewares);
    //valores das variações
    Route::post("/$prefix/create-value", [VariantsController::class, "addValueVariation"], $middlewares);
    Route::get("/$prefix/get-values/{param}", [VariantsController::class, "getValueVariation"]);
    Route::put("/$prefix/value/{param}", [VariantsController::class, "updateValueVariation"], $middlewares);
    Route::delete("/$prefix/delete-value/{param}", [VariantsController::class, "deleteValueVariation"], $middlewares);
});
