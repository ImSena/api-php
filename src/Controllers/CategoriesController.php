<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;

class CategoriesController
{
    public static function createCategories(Request $request, Response $response)
    {
        $body = $request::body();


        $category = CategoryService::createCategory($body);

        if(isset($category['error'])){
            return $response::json([
                'success' => false,
                'message' => $category['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $category
        ],200);
    }

    public static function getAllParent(Request $request, Response $response)
    {

        $category = CategoryService::getAllParent();

        if(isset($category['error'])){
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

    public static function deleteCategory(Request $request, Response $response)
    {
        $body = $request::body();

        $category = CategoryService::delete($body);

        if(isset($category['error'])){
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