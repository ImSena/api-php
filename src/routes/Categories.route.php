<?php

use App\Controllers\CategoriesController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\HasAdmin;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "categories",
    'middlewares'=> [LockedStore::class, AuthAdmin::class]
],function($prefix, $middlewares){
    Route::post("/$prefix/create", [CategoriesController::class, 'createCategories'], $middlewares);
    Route::delete("/$prefix/delete", [CategoriesController::class, 'deleteCategory'], $middlewares);
    Route::put("/$prefix/update-category", [CategoriesController::class, "updateCategory"], $middlewares);
    // Route::get("/$prefix/get-parents", [CategoriesController::class, "getAllParent"]);
    Route::get("/$prefix/get-categories", [CategoriesController::class, "getCategories"], [LockedStore::class, HasAdmin::class]);
});