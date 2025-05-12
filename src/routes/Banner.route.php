<?php

use App\Controllers\BannerController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "banner",
    "middlewares" => [LockedStore::class, AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [BannerController::class, "create"], $middlewares);
    Route::get("/$prefix", [BannerController::class, "getBanners"]);
    Route::put("/$prefix/{param}", [BannerController::class, "editBanner"], $middlewares);
    Route::delete("/$prefix/{param}", [BannerController::class, "deleteBanner"], $middlewares);
});