<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\AddressStoreService;
use App\Service\EmailStoreService;
use App\Service\PhoneStoreService;
use App\Service\SocialStoreService;
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

        if (isset($storeMediaService['error'])) {
            return $this->errorResponse($storeMediaService['error']);
        }

        $assets = [...$storeMediaService, ...$storeService];

        $this->successResponse("Assets resgatados com sucesso", $assets);
    }

    public function getStatus()
    {
        $storeService = new StoreService($this->pdo);

        $result = $storeService->getStatus();

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->response::json([
            "success" => true,
            "is_locked" => (bool) $result['is_locked'],
            "reasons" => $result['locked_reasons']
        ]);
    }

    public function updateStore()
    {
        $body = $this->request::body();

        $storeService = new StoreService($this->pdo);

        $result = $storeService->updateStore($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updateAddress($params)
    {
        $id = $params[0];

        $body = $this->request::body();
        $body['id'] = $id;
        $addressService = new AddressStoreService($this->pdo);

        $result = $addressService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updatePhone($params)
    {
        $id = $params[0];
        $body = $this->request::body();
        $body['id'] = $id;
        $phoneService = new PhoneStoreService($this->pdo);

        $result = $phoneService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updateTheme()
    {
        $body = $this->request::body();

        $storeService = new StoreService($this->pdo);
        $result = $storeService->updateTheme($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updateEmail($params)
    {
        $body = $this->request::body();

        $id = $params[0];

        $body['id'] = $id;

        $emailStoreService = new EmailStoreService($this->pdo);

        $result = $emailStoreService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updateSociais()
    {
        $body = $this->request::body();

        $socialService = new SocialStoreService($this->pdo);

        $result = $socialService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }
}

// public function getStripe()
// {
//     $storeService = new StoreService($this->pdo);

//     $result = $storeService->getStatusStripe();

//     echo json_encode($result);
// }