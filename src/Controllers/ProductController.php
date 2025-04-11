<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\ProductService;
use PDO;

class ProductController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }
    public function create(Request $request, Response $response)
    {
        $body = $request::body();

        $productService = new ProductService($this->pdo);
        $productService = $productService->create($body);

        if (isset($productService['error'])) {
            return $response::json([
                'success' => false,
                'message' => $productService['error'],
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $productService
        ], 200);
    }
    public function getAll(Request $request, Response $response, $param)
    {
        $params['page'] = isset($param[0]) ? intval($param[0]) : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAll($params['page']);

        if (isset($productService['error'])) {
            return $response::json([
                'success' => false,
                'message' => $productService['error'],
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ], 200);
    }
    public function getAllCategory(Request $request, Response $response, $param)
    {
        $params = [];
        $params['id_category'] = isset($param[0]) ? $param[0] : 1;
        $params['page'] = isset($param[1]) ? intval($param[1]) : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAllCategory($params);

        if (isset($productService['error'])) {
            return $response::json([
                'success' => false,
                'message' => $productService['error'],
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ], 200);
    }

    public function getAllBy(Request $request, Response $response, $param)
    {
        $params = [];
        $params['type_by'] = isset($param[0]) ? $param[0] : 'category';
        $params['id_by'] = isset($param[1]) ? (int)$param[1] : 1;
        $params['page'] = isset($param[2]) ? (int)$param[2] : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAllBy($params);

        if(isset($productService['error'])){
            return $response::json([
                'success' => false,
                'message' => $productService['error']
            ], 400);
        }
        $response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ]);
    }

    public function getById(Request $request, Response $response, $param)
    {
        $params = [];
        $params['id_product'] = isset($param[0]) ? (int) $param[0] : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getProductAndVariations($params);

        if(isset($productService['error'])){
            return $response::json([
                'success' => false,
                'message' => $productService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => "Produto resgatado com sucesso.",
            'content' => $productService,
        ]);
    }
}
