<?php

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Http\Response;
use App\Service\AdminService;

class AdminController
{
    public function register(Request $request, Response $response)
    {
        $body = $request::body();

        $adminService = AdminService::create($body);

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
}