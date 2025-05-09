<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\PhoneStore;
use App\Service\Base\BaseService;
use Exception;
use PDOException;

class PhoneStoreService extends BaseService
{
    public function createPhoneStore(array $data, ?bool $isTransaction = null)
    {

        return $this->execute(function () use ($data) {
            $PhoneStore = new PhoneStore($this->pdo);

            $resultInactive = $PhoneStore->setIsDefault();

            if (!$resultInactive) {
                throw new Exception("Não foi possível inativar contato.");
            }

            $result = $PhoneStore->createPhone($data);

            if (!$result) {
                throw new Exception("Não foi possível inserir novo contato.");
            }

            return "Contato inserido com sucesso.";
        }, $isTransaction);
    }

    public function getPhones(){
        return $this->execute(function(){
            $PhoneStore = new PhoneStore($this->pdo);

            $result = $PhoneStore->getPhones();

            if(!$result){
                throw new Exception("Não foi possível resgatar contatos.");
            }

            return $result;
        });
    }
}
