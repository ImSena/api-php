<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use UserService;

define('ROOT_PATH', realpath(__DIR__ .'/../..'));

// No UserController.php:
require_once ROOT_PATH . '/config.php';

class UserController
{
    public function register(Request $request, Response $response)
    {
        $data = $request::body();

        $userService = UserService::register($data);

        if(isset($userService['error'])){
            return $response::json([
                "success" => false,
                "message" => $userService['error']
            ], 400);
        }

        return $response::json([
            'success' => true,
            'message' => $userService
        ]);
    }
}
