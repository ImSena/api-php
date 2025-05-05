<?php

namespace App\Service;

use App\Model\Banner;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class BannerService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function() use ($data){
            $fields = Validator::validate([
                "id_media" => $data['id_media'] ?? '',
                "is_mobile" => $data['is_mobile'] ?? '',
                "is_default" => $data['is_default'] ?? ''
            ]);

            if(isset($data['name'])){
                $fields['name'] = $data['name'];
            }

            $Banner = new Banner($this->pdo);

            $result = $Banner->create($fields);

            if(!$result){
                throw new Exception("Não foi possível criar Banners");
            }

            return "Banners criados com sucesso";
        });
    }

    public function getBanners()
    {
        return $this->execute(function(){
            $Banner = new Banner($this->pdo);

            $result = $Banner->getBanners();

            
        });
    }
}