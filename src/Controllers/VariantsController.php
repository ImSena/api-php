<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\VariationService;

class VariantsController extends BaseController
{
    public function createVariant()
    {
        $body = $this->request::body();

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->createVariation($body);

        if (isset($variationService['error'])) {
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }

    public function getAllVariation()
    {
        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->getAllVariations();

        if (isset($variationService['error'])) {
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService['message'], $variationService['content']);
    }

    public function updateVariation($id)
    {
        $body = $this->request::body();
        $id = intval($id[0]);

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->updateVariation($body, $id);

        if (isset($variationService['error'])) {
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }

    public function deleteVariation($id)
    {
        $id = intval($id[0]);

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->deleteVariation($id);

        if (isset($variationService['error'])) {
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }

    public function addValueVariation() 
    {
        $body = $this->request::body();

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->createValue($body);

        if (isset($variationService['error'])) {
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }

    public function getValueVariation($id) 
    {
        $id = intval($id[0]);

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->getValueVariation($id);

        if(isset($variationService['error'])){
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService['message'], $variationService['content']);
    }

    public function updateValueVariation($id) 
    {
        $body = $this->request::body();
        $id = intval($id[0]);

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->updateValueVariation($body, $id);

        if(isset($variationService['error'])){
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }

    public function deleteValueVariation($id) 
    {
        $id = intval($id[0]);

        $variationService = new VariationService($this->pdo);
        $variationService = $variationService->deleteValue($id);

        if(isset($variationService['error'])){
            return $this->errorResponse($variationService['error']);
        }

        return $this->successResponse($variationService);
    }
}
