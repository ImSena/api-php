<?php

namespace App\Service;

use App\Model\SociaisStore;
use Exception;

class SocialStoreService extends BannerService{

    public function createSocial(array $data, bool $isTransaction = false)
    {
        return $this->execute(function() use ($data){
            $SocialStore = new SociaisStore($this->pdo);

            $find = $SocialStore->findByType($data['type']);

            if($find){
                throw new Exception("Midia social já cadastrada");
            }

            $result = $SocialStore->createSociais($data);

            if(!$result){
                throw new Exception("Não foi possível cadastrar rede social.");
            }

            return "Rede social cadastrada com sucesso.";
        }, $isTransaction);
    }

}