<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\CategoryService;

class CategoriesController extends BaseController
{
    public function createCategories()
    {
        $body = $this->request::body();

        $category = new CategoryService($this->pdo);
        $category = $category->createCategory($body);

        if (isset($category['error'])) {
            return $this->errorResponse($category['error']);
        }

        return $this->successResponse($category);
    }

    public function getCategories()
    {
        $rule = $this->request::getRule();
        $category = new CategoryService($this->pdo);
        $category = $category->getAllCategories($rule);

        if (isset($category['error'])) {
            return $this->errorResponse($category['error']);
        }
        
        return $this->successResponse("Categorias resgatadas com sucesso!", $category);
    }


    public function updateCategory(){
        $body = $this->request::body();

        $category = new CategoryService($this->pdo);
        $category = $category->update($body);

        if(isset($category['error'])){
            return $this->errorResponse($category['error']);
        }

        return $this->successResponse();
    }

    public function deleteCategory()
    {
        $body = $this->request::body();

        $category = new CategoryService($this->pdo);
        $category = $category->delete($body);

        if (isset($category['error'])) {
            return $this->errorResponse($category['error']);
        }

        return $this->successResponse("Categoria deletada com sucesso!");
    }
}
