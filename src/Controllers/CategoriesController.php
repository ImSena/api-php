<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;

class CategoriesController
{
    public function createCategories(Request $request, Response $response)
    {
        $body = $request::body();


        $category = CategoryService::createCategory($body);

        if (isset($category['error'])) {
            return $response::json([
                'success' => false,
                'message' => $category['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $category
        ], 200);
    }

    public function getAllParent(Request $request, Response $response)
    {

        $category = CategoryService::getAllParent();

        if (isset($category['error'])) {
            return $response::json([
                'success' => false,
                'message' => $category['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => "Categorias Pai resgatadas com sucesso!",
            'content' => $category
        ]);
    }

    public function getCategories(Request $request, Response $response)
    {
        $category = CategoryService::getAllCategories();

        if (isset($category['error'])) {
            return $response::json([
                'success' => false,
                'message' => $category['error']
            ], 400);
        }
        
        $response::json([
            'success' => true,
            'message' => "Categorias resgatadas com sucesso!",
            'content' => $category
        ]);
    }


    public function updateCategory(Request $request, Response $response){
        $body = $request::body();

        $category = CategoryService::update($body);

        if(isset($category['error'])){
            return $response::json([
                'success' => false,
                "message" => $category['error']
            ], 400);
        }

        $response::json([
            'success' => true,
        ], 204);
    }

    public function deleteCategory(Request $request, Response $response)
    {
        $body = $request::body();

        $category = CategoryService::delete($body);

        if (isset($category['error'])) {
            return $response::json([
                'success' => false,
                'message' => $category['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => "Categoria deletada com sucesso!",
        ]);
    }
}
