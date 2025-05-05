<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\BrandService;

class BrandController extends BaseController
{
    public function create()
    {
        $body = $this->request::body();

        $brand = new BrandService($this->pdo);
        $brand = $brand->create($body);

        if(isset($brand['error'])){
            return $this->errorResponse($brand['error']);
        }

        return $this->successResponse($brand);
    }

    public function getAll()
    {
        $brand = new BrandService($this->pdo);
        $brand = $brand->getAll();

        if(isset($brand['error'])){
            return $this->errorResponse($brand['error']);
        }

        return $this->successResponse($brand['message'], $brand['content']);
    }

    public function update($id)
    {
        $body = $this->request::body();

        $id = intval($id[0]);

        $brand = new BrandService($this->pdo);
        $brand = $brand->update($body, $id);

        if(isset($brand['error'])){
            return $this->errorResponse($brand['error']);
        }

        return $this->successResponse($brand);
    }

    public function delete($id)
    {
        $id = intval($id[0]);

        $brand = new BrandService($this->pdo);
        $brand = $brand->delete( $id);

        if(isset($brand['error'])){
            return $this->errorResponse($brand['error']);
        }

        return $this->successResponse($brand);
    }
}