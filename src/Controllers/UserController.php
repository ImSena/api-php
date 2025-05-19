<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\AccountUserService;
use App\Service\UserService;

// define('ROOT_PATH', realpath(__DIR__ .'/../..'));

// require_once ROOT_PATH . "/config.php";

class UserController extends BaseController
{
    public function register()
    {
        $data = $this->request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->create($data);

        if(isset($userService['error'])){
            return $this->errorResponse($userService['error']);
        }

        return $this->successResponse($userService);
    }

    public function login()
    {
        $data = $this->request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->login($data);

        if(isset($userService['error'])){
            return $this->errorResponse($userService['error']);
        }

        if(isset($userService['firstAccess'])){
            return $this->response::json([
                "success" => true,
                "message" => $userService['message'],
                "firstAccess" => true,
                "rule" => "USER",
            ], 200);
        }

        $this->response::json([
            "success" => true,
            "message" => $userService['message'],
            "status" => $userService['status'],
            "name" => $userService['name'],
            "token" => $userService['token'],
            "rule" => "USER"
        ], 200);
    }

    public function forgetAccess()
    {
        $body = $this->request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->forgetPassword($body);

        if(isset($userService['error'])){
            return $this->errorResponse($userService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $userService,
            "type" => "USER"
        ], 200);
    }

    public function resetPassword()
    {
        $body = $this->request::body();

        $accountService = new AccountUserService($this->pdo);
        $accountService = $accountService->resetPassword($body);

        if(isset($accountService['error'])){
            return $this->errorResponse($accountService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $accountService,
            "type" => "USER"
        ], 200);
    }

    public function sendActiveUser()
    {
        $body = $this->request::body();

        $userService = new UserService($this->pdo);
        $userService = $userService->activeAccountLink($body);

        if(isset($userService['error'])){
            return $this->errorResponse($userService['error']);
        }

        $this->response::json([
            'sucess' => true,
            'message' => $userService,
            "type" => "USER"
        ], 200);
    }

    public function activeAccount()
    {
        $body = $this->request::body();

        $userAccount = new AccountUserService($this->pdo);
        $userAccount = $userAccount->activeAccount($body);

        if(isset($userAccount['error'])){
            return $this->errorResponse($userAccount['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $userAccount,
            "type" => "USER"
        ], 200);
    }

    public function getAll($id)
    {

        $id = isset($id[0]) ? $id[0] : 1;

        $userService = new UserService($this->pdo);
        $userService = $userService->getAllUsers($id);

        if(isset($userService['error'])){
            return $this->errorResponse($userService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $userService['message'],
            'content' => $userService['content'],
            'pages' => $userService['pages'],
        ], 200);
    }

    public function edit()
    {
        $body = $this->request::body();
        $body['id_user'] = $this->request::getUserId();
        $body['rule'] = $this->request::getRule();

        $userService = new UserService($this->pdo);
        $userResult = $userService->editUser($body);

        if(isset($userResult['error'])){
            $this->errorResponse($userResult['error']);
        }

        return $this->successResponse("Usuário editado com sucesso.");

        
    }
    
}
