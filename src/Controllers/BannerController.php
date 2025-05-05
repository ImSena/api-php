<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\BannerService;

class BannerController extends BaseController{
    public function create()
    {
        $body = $this->request::body();

        $BannerService = new BannerService($this->pdo);
        $result = $BannerService->create($body);

        if($result['error']){
            return $this->errorResponse($result['error']);
        }

        return $this->successResponse($result);
    }

    public function getBanners()
    {
        $BannerService = new BannerService($this->pdo);
        $result = $BannerService->getBanners();

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        return $this->successResponse("Banners resgatados com sucesso", $result);
    }
}