<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;
use PDO;

class CategoriesController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }
    public function createCategories(Request $request, Response $response)
    {
        $body = $request::body();

        $category = new CategoryService($this->pdo);
        $category = $category->createCategory($body);

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

    public function getCategories(Request $request, Response $response)
    {
        $category = new CategoryService($this->pdo);
        $category = $category->getAllCategories();

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

        $category = new CategoryService($this->pdo);
        $category = $category->update($body);

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

        $category = new CategoryService($this->pdo);
        $category = $category->delete($body);

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
