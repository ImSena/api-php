<?php

use App\Controllers\WebhookController;
use App\Http\Route;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "webhook",
    "middlewares" => [LockedStore::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [WebhookController::class, 'getEvent'], $middlewares);
});