<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\StoreMediaService;
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

    public function initiateOnboarding()
    {
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->startOnboardingProcess();

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }

        $this->response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }

    public function login()
    {
        $storeService = new StoreService($this->pdo);
        $storeService = $storeService->createLogin();

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }


        $this->response::json([
            "success" => true,
            "message" => $storeService['message'],
            "redirect_url" => $storeService['url'],
        ]);
    }

    public function getAssets()
    {
        $storeService = new StoreService($this->pdo);

        $storeMediaService = new StoreMediaService($this->pdo);

        $storeService = $storeService->getAssets();
        $storeMediaService = $storeMediaService->getIdentity();

        if (isset($storeService['error'])) {
            return $this->errorResponse($storeService['error']);
        }

        if(isset($storeMediaService['error'])){
            return $this->errorResponse($storeMediaService['error']);
        }

        $assets = [...$storeMediaService, ...$storeService];

        $this->successResponse("Assets resgatados com sucesso", $assets);
    }

    public function getStatus()
    {
        $storeService = new StoreService($this->pdo);
        
        $result = $storeService->getStatus();

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->response::json([
            "success" => true,
            "is_locked" => (bool) $result['is_locked'],
            "reasons" => $result['locked_reasons']
        ]);
    }

}
