<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\AccountUserService;
use App\Service\UserService;

define('ROOT_PATH', realpath(__DIR__ .'/../..'));

// No UserController.php:
require_once ROOT_PATH . '/config.php';

class UserController
{
    public function register(Request $request, Response $response)
    {
        $data = $request::body();

        $userService = UserService::create($data);

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

    public function login(Request $request, Response $response)
    {
        $data = $request::body();

        $userService = UserService::login($data);

        if(isset($userService['error'])){
            return $response::json([
                "success" => false,
                "message" => $userService['error']
            ], 400);
        }

        if(isset($userService['firstAccess'])){
            return $response::json([
                "success" => true,
                "message" => $userService['message'],
                "firstAccess" => true
            ], 200);
        }

        $response::json([
            "success" => true,
            "message" => $userService['message'],
            "status" => $userService['status'],
            "token" => $userService['token']
        ], 200);
    }

    public function forgetAccess(Request $request, Response $response)
    {
        $body = $request::body();

        $userService = UserService::forgetPassword($body);

        if(isset($userService['error'])){
            return $response::json([
                'success' => false,
                'message' => $userService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $userService
        ], 200);
    }

    public function resetPassword(Request $request, Response $response)
    {
        $body = $request::body();

        $accountService = AccountUserService::resetPassword($body);

        if(isset($accountService['error'])){
            return $response::json([
                'success' => false,
                'message' => $accountService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $accountService
        ], 200);
    }

    public function sendActiveUser(Request $request, Response $response)
    {
        $body = $request::body();

        $userService = UserService::activeAccountLink($body);

        if(isset($userService['error'])){
            return $response::json([
                'success' => false,
                'message' => $userService['error']
            ], 400);
        }

        $response::json([
            'sucess' => true,
            'message' => $userService
        ], 200);
    }

    public function activeAccount(Request $request, Response $response)
    {
        $body = $request::body();

        $userAccount = AccountUserService::activeAccount($body);

        if(isset($userAccount['error'])){
            return $response::json([
                'success' => false,
                'message' => $userAccount['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $userAccount
        ], 200);
    }
}
