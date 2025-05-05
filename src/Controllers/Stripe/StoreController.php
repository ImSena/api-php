<?php

namespace App\Controllers\Stripe;

use App\Controllers\Base\BaseController;
use App\Service\StoreService;

class StoreController extends BaseController
{
    public function createStore()
    {
        $data = $this->request::body();
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->createStore($data);

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }

        $this->successResponse($storeService);
    }

    public function initiateOnboarding($param)
    {
        $storeId = isset($param[0]) ? $param[0] : null;
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->startOnboardingProcess($storeId);

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }

        $this->response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }

    public function login($param)
    {
        $storeId = isset($param[0]) ? $param[0] : null;
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->createLogin($storeId);

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }


        $this->response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }
}
