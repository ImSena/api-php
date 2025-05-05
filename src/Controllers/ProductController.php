<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\ProductService;

class ProductController extends BaseController
{
    public function create()
    {
        $body = $this->request::body();

        $productService = new ProductService($this->pdo);
        $productService = $productService->create($body);

        if (isset($productService['error'])) {
            return $this->errorResponse($productService['error']);
        }

        return $this->successResponse($productService);
    }
    public function getAll($param)
    {
        $params['page'] = isset($param[0]) ? intval($param[0]) : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAll($params['page']);

        if (isset($productService['error'])) {
            return $this->errorResponse($productService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ], 200);
    }
    public function getAllCategory($param)
    {
        $params = [];
        $params['id_category'] = isset($param[0]) ? $param[0] : 1;
        $params['page'] = isset($param[1]) ? intval($param[1]) : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAllCategory($params);

        if (isset($productService['error'])) {
            return $this->errorResponse($productService['error']);
        }

        $this->response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ], 200);
    }

    public function getAllBy($param)
    {
        $params = [];
        $params['type_by'] = isset($param[0]) ? $param[0] : 'category';
        $params['id_by'] = isset($param[1]) ? (int)$param[1] : 1;
        $params['page'] = isset($param[2]) ? (int)$param[2] : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getAllBy($params);

        if(isset($productService['error'])){
            return $this->errorResponse($productService['error']);
        }
        $this->response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content'],
            'page' => $productService['page'],
        ]);
    }

    public function getById($param)
    {
        $params = [];
        $params['id_product'] = isset($param[0]) ? (int) $param[0] : 1;

        $productService = new ProductService($this->pdo);
        $productService = $productService->getProductAndVariations($params);

        if(isset($productService['error'])){
            return $this->errorResponse($productService['error']);
        }

        return $this->successResponse("Produto resgatado com sucesso.", $productService);
    }
}
