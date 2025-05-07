<?php

namespace App\Service;

use App\Model\PhoneStore;
use App\Service\Base\BaseService;
use Exception;

class PhoneStoreService extends BaseService
{
    public function createPhoneStore(array $data)
    {
        return $this->execute(function() use ($data){
            $PhoneStore = new PhoneStore($this->pdo);

            $resultInactive = $PhoneStore->setIsDefault();

            if(!$resultInactive){
                throw new Exception("Não foi possível inativar contato.");
            }

            $result = $PhoneStore->createPhone($data);

            if(!$result){
                throw new Exception("Não foi possível inserir novo contato.");
            }

            return "Contato inserido com sucesso.";

        }, true);
    }
}