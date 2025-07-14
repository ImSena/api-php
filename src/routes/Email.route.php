<?php

namespace App\Routes;

use App\Controllers\EmailController;
use App\Http\Route;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "email",
    'middlewares'=> [LockedStore::class]
],function($prefix, $middlewares){
    Route::post("/$prefix", [EmailController::class, 'sendMailStore'], $middlewares);
});