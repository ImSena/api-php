<?php

namespace App\Controllers\Stripe;

use App\Http\Request;
use App\Http\Response;
use App\Service\Stripe\StoreService;

class StoreController
{


    public function createStore(Request $request, Response $response)
    {
        $data = $request::body();

        $storeService = StoreService::createStore($data);

        if (isset($storeService['error'])) {
            return $response::json([
                "success" => false,
                "message" => $storeService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $storeService
        ]);
    }

    public function initiateOnboarding(Request $request, Response $response, $param)
    {
        $storeId = isset($param[0]) ? $param[0] : null;

        $storeService = StoreService::startOnboardingProcess($storeId);

        if (isset($storeService['error'])) {
            return $response::json([
                "success" => false,
                "message" => $storeService['error']
            ], 400);
        }


        $response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }

    public function login(Request $request, Response $response, $param)
    {
        $storeId = isset($param[0]) ? $param[0] : null;

        $storeService = StoreService::createLogin($storeId);

        if (isset($storeService['error'])) {
            return $response::json([
                "success" => false,
                "message" => $storeService['error']
            ], 400);
        }


        $response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }
}
