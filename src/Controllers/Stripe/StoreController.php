<?php

namespace App\Controllers\Stripe;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\Stripe\StoreService;
use PDO;

class StoreController
{

    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function createStore(Request $request, Response $response)
    {
        $data = $request::body();
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->createStore($data);

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
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->startOnboardingProcess($storeId);

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
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->createLogin($storeId);

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
