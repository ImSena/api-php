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
        $files = $request::files();

        $productService = ProductService::create($body, $files);

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

    public function delete(Request $request, Response $response)
    {

        $body = $request::body();

        $productService = ProductService::delete($body);

        if(isset($productService['error'])){
            return $response::json([
                'success' => false,
                'message' => $productService['error']
            ],400);
        }

        return $response::json([
            'success' => true,
            'message' => $productService
        ], 200);
    }
}
