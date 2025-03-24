<?php

use App\Controllers\AddressController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\AuthUser;
use App\Controllers\Admin\AdminController;
use App\Controllers\BrandController;
use App\Controllers\CategoriesController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use App\Controllers\HomeController;
use App\Controllers\MediaController;
use App\Controllers\OrderController;
use App\Controllers\PaymentsController;
use App\Controllers\VariantsController;
use App\Middlewares\AuthPermission;

Route::get('/', [HomeController::class, 'index']);

//admin
Route::group([
    'prefix' => 'admin',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    //rotas de crud
    Route::post("/$prefix/register", [AdminController::class, 'registerSuper']);
    //para criar superadmin basta descomentar
    // Route::post("/$prefix/register", [AdminController::class, "register"], $middlewares);
    Route::post("/$prefix/login", [AdminController::class, 'login']);
    Route::post("/$prefix/forget-password", [AdminController::class, 'forgetAccess']);
    Route::put("/$prefix/reset-password", [AdminController::class, 'resetPassword']);

    //ativar conta
    Route::post("/$prefix/send-active-account",[AdminController::class, 'sendActiveAdmin']);
    Route::put("/$prefix/active-account", [AdminController::class, 'activeAccount']);
});

//user
Route::group([
    'prefix' => 'user',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/register", [UserController::class, 'register']);
    Route::post("/$prefix/login", [UserController::class, 'login']);
    Route::post("/$prefix/forget-password", [UserController::class, 'forgetAccess']);
    Route::put("/$prefix/reset-password", [UserController::class, 'resetPassword']);

    Route::post("/$prefix/send-active-account", [UserController::class, 'sendActiveUser']);
    Route::put("/$prefix/active-account", [UserController::class, 'activeAccount']);
    Route::get("/$prefix/{param}", [UserController::class, 'getAll'], [AuthAdmin::class]);
    Route::get("/$prefix/inactive/{param}", [UserController::class, 'getAllInactive'], [AuthAdmin::class]);
});

//address

Route::group([
    'prefix' => 'address',
    'middlewares' => [AuthUser::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [AddressController::class, 'create'], $middlewares);
    Route::put("/$prefix/update", [AddressController::class, 'update'], $middlewares);
    Route::get("/$prefix", [AddressController::class, 'getAll'], $middlewares);
});


//categories
Route::group([
    "prefix" => "categories",
    'middlewares'=> [AuthAdmin::class]
],function($prefix, $middlewares){
    Route::post("/$prefix/create", [CategoriesController::class, 'createCategories'], $middlewares);
    Route::delete("/$prefix/delete", [CategoriesController::class, 'deleteCategory'], $middlewares);
    Route::put("/$prefix/update-category", [CategoriesController::class, "updateCategory"], $middlewares);
    Route::get("/$prefix/get-parents", [CategoriesController::class, "getAllParent"]);
    Route::get("/$prefix/get-categories", [CategoriesController::class, "getCategories"]);
});

//media

Route::group([
    "prefix" => "media",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    //Folders
    Route::post("/$prefix/get-content-folder", [MediaController::class, "getContentFolder"], $middlewares);
    Route::post("/$prefix/create-folder", [MediaController::class, "createFolder"], $middlewares);
    Route::put("/$prefix/rename-folder", [MediaController::class, "renameFolder"], $middlewares);
    Route::put("/$prefix/move-folder", [MediaController::class, "moveFolder"], $middlewares);
    Route::put("/$prefix/move-folder-trash" , [MediaController::class, "moveFolderTrash"], $middlewares);
    Route::put("/$prefix/restore-folder", [MediaController::class, "restoreFolder"], $middlewares);
    Route::delete("/$prefix/folder", [MediaController::class, "deleteFolder"], $middlewares);

    //Files
    Route::post("/$prefix/upload-file", [MediaController::class, "uploadFile"], $middlewares);
    Route::put("/$prefix/rename-file", [MediaController::class, "renameFile"], $middlewares);
    Route::put("/$prefix/move-file", [MediaController::class, "moveFile"], $middlewares);
    Route::put("/$prefix/move-file-trash", [MediaController::class, "moveFileTrash"], $middlewares);
    Route::put("/$prefix/restore-file", [MediaController::class, "restoreFile"], $middlewares);
    Route::delete("/$prefix/file", [MediaController::class, "deleteFile"], $middlewares);

    // Route::get("/$prefix/get-folder/{param}", [MediaController::class], $middlewares);
});

//products
Route::group([
    'prefix' => 'products',
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [ProductController::class, 'create'], $middlewares);
    // Route::delete("/$prefix", [ProductController::class, 'delete'] , $middlewares);
    // Route::get("/$prefix/{param}", [ProductController::class, 'getProduct']);
    //primeiro parâmetro seria o que gostaria de buscar (por categoria, marca...), segundo é o id e o terceiro a pagina para o offset
    Route::get("/$prefix/get-all-by/{param}/{param}/{param}", [ProductController::class, 'getAllBy']);
    //parametro para a páginação
    Route::get("/$prefix/{param}", [ProductController::class, 'getAll']);
});

//brands
Route::group([
    "prefix" => "brands",
    'middlewares' => [AuthAdmin::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/create", [BrandController::class, 'create'], $middlewares);
    Route::delete("/$prefix/{param}", [BrandController::class, 'delete'], $middlewares);
    Route::put("/$prefix/{param}", [BrandController::class, "update"], $middlewares);
    Route::get("/$prefix", [BrandController::class, "getAll"]);
});

//variations
Route::group([
    "prefix" => "variations",
    "middlewares" => [AuthAdmin::class]
], function($prefix, $middlewares){
    //variações
    Route::post("/$prefix/create-variation", [VariantsController::class, "createVariant"], $middlewares);
    Route::get("/$prefix/get-variations", [VariantsController::class, "getAllVariation"], $middlewares);
    Route::put("/$prefix/variation/{param}", [VariantsController::class, "updateVariation"], $middlewares);
    Route::delete("/$prefix/variation/{param}", [VariantsController::class, "deleteVariation"], $middlewares);
    //valores das variações
    Route::post("/$prefix/create-value", [VariantsController::class, "addValueVariation"], $middlewares);
    Route::get("/$prefix/get-values/{param}", [VariantsController::class, "getValueVariation"], $middlewares);
    Route::put("/$prefix/value/{param}", [VariantsController::class, "updateValueVariation"], $middlewares);
    Route::delete("/$prefix/delete-value/{param}", [VariantsController::class, "deleteValueVariation"], $middlewares);
});

Route::group([
    "prefix" => "order",
    "middlewares" => [AuthPermission::class]
], function($prefix, $middlewares){
    Route::post("/$prefix", [OrderController::class, "create"], [AuthUser::class]);
    Route::get("/$prefix/{param}", [OrderController::class, "getById"], $middlewares);
    Route::get("/$prefix/{param}/{param}", [OrderController::class, "getAll"], $middlewares);
});

Route::group([
    "prefix" => "payments",
    "middlewares" => [AuthPermission::class]
], function($prefix, $middlewares){
    Route::post("/$prefix/pay/{param}", [PaymentsController::class, "pay"], $middlewares);
    Route::get("/$prefix", [PaymentsController::class, 'getPayments'], $middlewares);
    Route::get("/$prefix/{param}", [PaymentsController::class, 'getDetails'], $middlewares);
});