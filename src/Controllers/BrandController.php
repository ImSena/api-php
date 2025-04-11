<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\BrandService;
use PDO;

class BrandController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function create(Request $request, Response $response)
    {
        $body = $request::body();

        $brand = new BrandService($this->pdo);
        $brand = $brand->create($body);

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
        $brand = new BrandService($this->pdo);
        $brand = $brand->getAll();

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

        $brand = new BrandService($this->pdo);
        $brand = $brand->update($body, $id);

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

        $brand = new BrandService($this->pdo);
        $brand = $brand->delete( $id);

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