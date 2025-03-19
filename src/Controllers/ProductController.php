<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\ProductService;

class ProductController
{
    public function create(Request $request, Response $response)
    {
        $body = $request::body();

        $productService = ProductService::create($body);

        if(isset($productService['error'])){
            return $response::json([
                'success'=> false,
                'message' => $productService['error'],
            ], 400);
        }

        return $response::json([
            'success' => true,
            'message' => $productService
        ], 200);
    }

    public function getAll(Request $request, Response $response){
        $productService = ProductService::getAll();

        if(isset($productService['error'])){
            return $response::json([
                'success'=> false,
                'message' => $productService['error'],
            ], 400);
        }

        return $response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content']
        ], 200);
    }

    public function getAllCategory(Request $request, Response $response, $id)
    {
        $id = intval($id[0]);
        $productService = ProductService::getAllCategory($id);

        if(isset($productService['error'])){
            return $response::json([
                'success'=> false,
                'message' => $productService['error'],
            ], 400);
        }

        return $response::json([
            'success' => true,
            'message' => $productService['message'],
            'content' => $productService['content']
        ], 200);
    }
}
