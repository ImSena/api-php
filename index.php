<?php


require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/routes/main.php";
use App\Core\Core;
use App\Http\Route;

require_once('./src/Jwt/Jwt.php');
Core::dispatch(Route::routes());