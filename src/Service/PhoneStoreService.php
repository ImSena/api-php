<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\PhoneStore;
use App\Service\Base\BaseService;
use Exception;
use PDOException;

class PhoneStoreService extends BaseService
{
    public function createPhoneStore(array $data, $isTransaction = false)
    {
        if($isTransaction){
            return $this->execute(function () use ($data) {
                $this->createInternalPhoneStore($data);
            }, $isTransaction);
        }

        return $this->createInternalPhoneStore($data);

    }

    private function createInternalPhoneStore(array $data)
    {
        try{
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
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}
