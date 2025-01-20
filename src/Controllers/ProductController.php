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
                'success'=> true,
                'message' => $productService['error'],
            ], 400);
        }

        return $response::json([
            'success' => true,
            'message' => $productService
        ], 200);
    }
}
