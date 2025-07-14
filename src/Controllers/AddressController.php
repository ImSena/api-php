<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\AddressService;

class AddressController extends BaseController
{

    public function create()
    {

        $id = $this->request::getUserId();

        $body = $this->request::body();
        $body['id_user'] = $id;

        $addressService = new AddressService($this->pdo);
        $addressService = $addressService->create($body);

        if (isset($addressService['error'])) {
            return $this->errorResponse($addressService['error']);
        }

        return $this->successResponse($addressService);
    }

    public function getAll()
    {
        $id = $this->request::getUserId();
        $rule = $this->request::getRule();
        $data = [
            "id_user" => $id,
            "rule" => $rule
        ];

        $addressService = new AddressService($this->pdo);
        $addressService = $addressService->getAll($data);

        if (isset($addressService['error'])) {
            return $this->errorResponse($addressService['error']);
        }

        return $this->successResponse($addressService['message'], $addressService['content']);
    }

    public function update()
    {
        $id = $this->request::getUserId();

        $body = $this->request::body();
        $body['id_user'] = $id;

        $addressService = new AddressService($this->pdo);
        $addressService = $addressService->edit($body);

        if(isset($addressService['error'])){
            return $this->errorResponse($addressService['error']);
        }

        return $this->successResponse("Endereço editado com sucesso.");
    }

    // public function getById(Request $request, Response $response, $id)
    // {
    //     $id = intval($id[0]);

    //     $addressService = AddressService::getById($id);

    //     if(isset($addressService['error'])){
    //         return $response::json([
    //             'success' => false,
    //             "message" => $addressService['error']
    //         ], 400);
    //     }
    // }
}
