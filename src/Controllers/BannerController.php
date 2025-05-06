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

        if(isset($result['error'])){
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

    public function editBanner($param)
    {
        $body = $this->request::body();
        $body['id_media'] = (int) $param[0];
        $BannerService = new BannerService($this->pdo);
        $result = $BannerService->editBanner($body);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        return $this->successResponse("Banner editado com sucesso", [], 204);
    }

    public function deleteBanner($param)
    {
        $idBanner = (int) $param[0];

        $bannerService = new BannerService($this->pdo);
        $result = $bannerService->deleteBanner($idBanner);

        if(isset($result['error'])){
            return $this->errorResponse($result['error']);
        }

        return $this->successResponse("Banner deletado com sucesso", [], 204);
    }
}