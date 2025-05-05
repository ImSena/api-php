<?php

use App\Controllers\BannerController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    "prefix" => "banner",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [BannerController::class, "create"], $middlewares);
    Route::get("/$prefix", [BannerController::class, "getBanners"]);
});