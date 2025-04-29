<?php

use App\Controllers\WebhookController;
use App\Http\Route;

Route::group([
    "prefix" => "webhook",
    "middlewares" => []
], function($prefix, $middlewares){
    Route::post("/$prefix", [WebhookController::class, 'getEvent']);
});