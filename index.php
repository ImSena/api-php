<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/routes/main.php";
use App\Core\Core;
use App\Http\Route;

date_default_timezone_set('America/Sao_Paulo');

Core::dispatch(Route::routes());