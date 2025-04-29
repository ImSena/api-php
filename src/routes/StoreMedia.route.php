<?php

use App\Controllers\StoreMediaController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;

Route::group([
    "prefix" => "store-media",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [StoreMediaController::class, 'create'], $middlewares);
});
