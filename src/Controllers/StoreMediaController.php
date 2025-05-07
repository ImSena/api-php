<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\StoreMediaService;

class StoreMediaController extends BaseController{
    
    public function create(){
        
        $data = $this->request::body();

        $StoreMediaService = new StoreMediaService($this->pdo);
        $result = $StoreMediaService->createMedia($data);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }
        
        return $this->successResponse($result);
    }
}