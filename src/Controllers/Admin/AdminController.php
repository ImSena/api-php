<?php

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Http\Response;
use App\Service\AccountAdminService;
use App\Service\AdminService;

class AdminController
{

    public function registerSuper(Request $request, Response $response)
    {
        $body = $request::body();

        $adminService = AdminService::create($body, true);

        if(isset($adminService['error'])){
            return $response::json([
                'success' => false,
                "message" => $adminService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $adminService
        ], 200);
    }

    public function register(Request $request, Response $response)
    {
        $body = $request::body();

        $adminService = AdminService::create($body, false);

        if(isset($adminService['error'])){
            return $response::json([
                'success' => false,
                "message" => $adminService['error']
            ], 400);
        }


        $response::json([
            'success' => true,
            'message' => $adminService
        ], 200);
    }

    public function login(Request $request, Response $response){
        $body = $request::body();

        $adminService = AdminService::login($body);

        if(isset($adminService['error'])){
            return $response::json([
                'success' => false,
                "message" => $adminService['error']
            ], 400);
        }

        if(isset($adminService['firstAccess'])){
            return $response::json([
                'success' => true,
                'message' => $adminService['message'],
                'firstAccess' => true,
            ], 200);
        }

        $response::json([
            'success' => true,
            'message' => $adminService['message'],
            'user' => $adminService['user'],
            'rule' => $adminService['rule'],
            'status' => $adminService['status'],
            'token' => $adminService['token']
        ], 200);
    }

    public function forgetAccess(Request $request, Response $response)
    {
        $body = $request::body();

        $adminService = AdminService::forgetPassword($body);

        if(isset($adminService['error'])){
            return $response::json([
                'success' => false,
                'message' => $adminService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $adminService
        ], 200);
    }

    public function resetPassword(Request $request, Response $response)
    {
        $body = $request::body();

        $accountService = AccountAdminService::resetPasswordAdmin($body);

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

    public function sendActiveAdmin(Request $request, Response $response)
    {
        $body = $request::body();

        $adminService = AdminService::activeAccountLink($body);

        if(isset($adminService['error'])){
            return $response::json([
                'success' => false,
                'message' => $adminService['error']
            ], 400);
        }

        $response::json([
            'sucess' => true,
            'message' => $adminService
        ], 200);
    }

    public function activeAccount(Request $request, Response $response)
    {
        $body = $request::body();

        $adminAccount = AccountAdminService::activeAccountAdmin($body);

        if(isset($adminAccount['error'])){
            return $response::json([
                'success' => false,
                'message' => $adminAccount['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $adminAccount
        ], 200);
    }
}