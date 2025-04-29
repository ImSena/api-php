<?php

use App\Http\Route;
use App\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/teste', [HomeController::class, 'teste']);

//admin
require_once __DIR__ . "/Admin.route.php";

//user
require_once __DIR__ . "/User.route.php";

//address

require_once __DIR__ . "/Addresss.route.php";

//categories

require_once __DIR__ . "/Categories.route.php";

//media

require_once __DIR__ . "/Media.route.php";

//products

require_once __DIR__ . "/Products.route.php";

//brands

require_once __DIR__ . "/Brands.route.php";

//variations

require_once __DIR__ . "/Variations.route.php";

//order

require_once __DIR__ . "/Order.route.php";

//payments

require_once __DIR__ . "/Payments.route.php";

//store

require_once __DIR__ . "/Store.route.php";

//store Media 

require_once __DIR__ . "/StoreMedia.route.php";

//webhook

require_once __DIR__ . "/Webhook.route.php";