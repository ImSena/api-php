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

        $addressService = new AddressService($this->pdo);
        $addressService = $addressService->getAll($id);

        if (isset($addressService['error'])) {
            return $this->errorResponse($addressService['error']);
        }

        return $this->successResponse($addressService['message'], $addressService['content']);
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
