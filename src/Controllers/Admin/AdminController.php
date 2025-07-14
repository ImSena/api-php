<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\BaseController;
use App\Service\AccountAdminService;
use App\Service\AdminService;

class AdminController extends BaseController
{
    public function registerSuper()
    {
        $body = $this->request::body();

        $adminService = new AdminService($this->pdo);
        $adminService = $adminService->create($body, true);

        if(isset($adminService['error'])){
            return $this->errorResponse($adminService['error']);
        }

        return $this->successResponse($adminService);
    }

    public function register()
    {
        $body = $this->request::body();

        $adminService = new AdminService($this->pdo);
        $adminService = $adminService->create($body, false);

        if(isset($adminService['error'])){
            return $this->errorResponse($adminService['error']);
        }

        return $this->successResponse($adminService);
    }

    public function login(){
        $body = $this->request::body();
        $adminService = new AdminService($this->pdo);
        $adminService = $adminService->login($body);


        if(isset($adminService['error'])){
            return $this->errorResponse($adminService['error']);
        }

        if(isset($adminService['firstAccess'])){
            return $this->response::json([
                'success' => true,
                'message' => $adminService['message'],
                'firstAccess' => true,
                "rule" => "ADMIN"
            ], 200);
        }

        return $this->response::json([
            'success' => true,
            'message' => $adminService['message'],
            'user' => $adminService['user'],
            'rule' => $adminService['rule'],
            'status' => $adminService['status'],
            'token' => $adminService['token']
        ], 200);
    }

    public function forgetAccess()
    {
        $body = $this->request::body();
        $adminService = new AdminService($this->pdo);
        $adminService = $adminService->forgetPassword($body);

        if(isset($adminService['error'])){
            return $this->errorResponse($adminService['error']);
        }

        return $this->response::json([
            'success' => true,
            'message' => $adminService,
            "type" => "ADMIN"
        ], 200);
    }

    public function resetPassword()
    {
        $body = $this->request::body();

        $accountService = new AccountAdminService($this->pdo);
        $accountService = $accountService->resetPasswordAdmin($body);

        if(isset($accountService['error'])){
            return $this->errorResponse($accountService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $accountService,
            "type" => "ADMIN"
        ], 200);
    }

    public function sendActiveAdmin()
    {
        $body = $this->request::body();

        $adminService = new AdminService($this->pdo);
        $adminService = $adminService->activeAccountLink($body);

        if(isset($adminService['error'])){
            return $this->errorResponse($adminService);
        }

        $this->response::json([
            'sucess' => true,
            'message' => $adminService,
            "type" => "ADMIN"
        ], 200);
    }

    public function activeAccount()
    {
        $body = $this->request::body();

        $adminAccount = new AccountAdminService($this->pdo);
        $adminAccount = $adminAccount->activeAccountAdmin($body);

        if(isset($adminAccount['error'])){
            return $this->errorResponse($adminAccount['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $adminAccount,
            "type" => "ADMIN"
        ], 200);
    }
}