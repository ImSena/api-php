<?php

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Http\Response;
use App\Service\AdminService;
use App\Service\ForgetPassword;

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

        $response::json([
            'success' => true,
            'message' => $adminService['message'],
            'user' => $adminService['user'],
            'rule' => $adminService['rule'],
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

        $adminService = ForgetPassword::resetPassword($body);

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

}