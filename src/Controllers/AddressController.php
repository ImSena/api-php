<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\AddressService;

class AddressController
{
    public function create(Request $request, Response $response)
    {

        $id = $request::getUserId();
   
        $body = $request::body();
        $body['id_user'] = $id;

        $addressService = AddressService::create($body);

        if(isset($addressService['error'])){
            return $response::json([
                'success' => false,
                "message" => $addressService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $addressService
        ]);
    }

    public function getAll(Request $request, Response $response)
    {
        $id = $request::getUserId();

        $addressService = AddressService::getAll($id);

        if(isset($addressService['error'])){
            return $response::json([
                'success' => false,
                "message" => $addressService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $addressService['message'],
            "content" => $addressService['content']
        ]);
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