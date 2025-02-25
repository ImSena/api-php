<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\BrandService;

class BrandController
{
    public function create(Request $request, Response $response)
    {
        $body = $request::body();

        $brand = BrandService::create($body);

        if(isset($brand['error'])){
            return $response::json([
                "success" => false,
                "message" => $brand['error']
            ]);
        }

        $response::json([
            "success" => true,
            "message" => $brand
        ]);
    }

    public function getAll(Request $request, Response $response)
    {
        $body = $request::body();

        $brand = BrandService::getAll($body);

        if(isset($brand['error'])){
            return $response::json([
                "success" => false,
                "message" => $brand['error']
            ]);
        }

        $response::json([
            "success" => true,
            "message" => $brand['message'],
            "content" => $brand['content']
        ]);
    }

    public function update(Request $request, Response $response, $id)
    {
        $body = $request::body();

        $id = intval($id[0]);

        $brand = BrandService::update($body, $id);

        if(isset($brand['error'])){
            return $response::json([
                "success" => false,
                "message" => $brand['error']
            ]);
        }

        $response::json([
            "success" => true,
            "message" => $brand,
        ]);
    }

    public function delete(Request $request, Response $response, $id)
    {
        $id = intval($id[0]);

        $brand = BrandService::delete( $id);

        if(isset($brand['error'])){
            return $response::json([
                "success" => false,
                "message" => $brand['error']
            ]);
        }

        $response::json([
            "success" => true,
            "message" => $brand
        ]);
    }
}