<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\AccountUserService;
use App\Service\UserService;
use PDO;

define('ROOT_PATH', realpath(__DIR__ .'/../..'));

require_once ROOT_PATH . '/config.php';

class UserController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }
    public function register(Request $request, Response $response)
    {
        $data = $request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->create($data);

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

        $userService = new UserService($this->pdo);
        $userService = $userService->login($data);

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
                "firstAccess" => true,
                "rule" => "USER",
            ], 200);
        }

        $response::json([
            "success" => true,
            "message" => $userService['message'],
            "status" => $userService['status'],
            "name" => $userService['name'],
            "token" => $userService['token'],
            "rule" => "USER"
        ], 200);
    }

    public function forgetAccess(Request $request, Response $response)
    {
        $body = $request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->forgetPassword($body);

        if(isset($userService['error'])){
            return $response::json([
                'success' => false,
                'message' => $userService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $userService,
            "type" => "USER"
        ], 200);
    }

    public function resetPassword(Request $request, Response $response)
    {
        $body = $request::body();

        $accountService = new AccountUserService($this->pdo);
        $accountService = $accountService->resetPassword($body);

        if(isset($accountService['error'])){
            return $response::json([
                'success' => false,
                'message' => $accountService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $accountService,
            "type" => "USER"
        ], 200);
    }

    public function sendActiveUser(Request $request, Response $response)
    {
        $body = $request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->activeAccountLink($body);

        if(isset($userService['error'])){
            return $response::json([
                'success' => false,
                'message' => $userService['error']
            ], 400);
        }

        $response::json([
            'sucess' => true,
            'message' => $userService,
            "type" => "USER"
        ], 200);
    }

    public function activeAccount(Request $request, Response $response)
    {
        $body = $request::body();

        $userAccount = new AccountUserService($this->pdo);
        $userAccount = $userAccount->activeAccount($body);

        if(isset($userAccount['error'])){
            return $response::json([
                'success' => false,
                'message' => $userAccount['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $userAccount,
            "type" => "USER"
        ], 200);
    }

    public function getAll(Request $request, Response $response, $id)
    {

        $id = isset($id[0]) ? $id[0] : 1;

        $userService = new UserService($this->pdo);
        $userService = $userService->getAllUsers($id);

        if(isset($userService['error'])){
            return $response::json([
                'success' => false,
                'message' => $userService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $userService['message'],
            'content' => $userService['content'],
            'pages' => $userService['pages'],
        ], 200);
    }

    
}
