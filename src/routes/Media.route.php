<?php

use App\Controllers\MediaController;
use App\Http\Route;
use App\Middlewares\AuthAdmin;
use App\Middlewares\LockedStore;

Route::group([
    "prefix" => "media",
    "middlewares" => [LockedStore::class, AuthAdmin::class]
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