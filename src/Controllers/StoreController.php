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

    public function insertAddressStore()
    {
        $addressStore = new AddressStoreService($this->pdo);

        $body = $this->request::body();

        $result = $addressStore->createAddress($body);

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

    public function deleteAddress($param)
    {
        $addressStore = new AddressStoreService($this->pdo);

        $id = $param[0];
        $result = $addressStore->deleteAddress($id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }
    //telefone
    public function insertPhone()
    {
        $body = $this->request::body();
        
        $phoneService = new PhoneStoreService($this->pdo);

        $result = $phoneService->createPhoneStore($body);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function updatePhone($params)
    {
        $id = intval($params[0]);
        $body = $this->request::body();
        $body['id'] = $id;
        $phoneService = new PhoneStoreService($this->pdo);

        $result = $phoneService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function deletePhone($params)
    {
        $id = intval($params[0]);
        $phoneService = new PhoneStoreService($this->pdo);

        $result = $phoneService->delete($id);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    //email

    public function insertEmail()
    {
        $body = $this->request::body();
        $emailService = new EmailStoreService($this->pdo);

        $result = $emailService->createEmail($body);

        if(isset($result['error'])){
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

    public function deleteEmail($params)
    {
        $id = intval($params[0]);

        $emailStoreService = new EmailStoreService($this->pdo);

        $result = $emailStoreService->delete($id);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    //redes sociais

    public function insertSocial()
    {
        $body = $this->request::body();

        $SocialService = new SocialStoreService($this->pdo);

        $result = $SocialService->createSocial($body);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);

    }

    public function updateSocial($params)
    {
        $type = $params[0];
        $body = $this->request::body();
        $body['type'] = $type;

        $socialService = new SocialStoreService($this->pdo);

        $result = $socialService->update($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function deleteSocial($param){
        $type = $param[0];

        $SocialService = new SocialStoreService($this->pdo);

        $result = $SocialService->delete($type);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result);
    }

    public function activeAccountStripe()
    {
        $store = new StoreService($this->pdo);

        $result = $store->updateAccountStripe();

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        $this->successResponse($result['message'], [$result['url']]);
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
}