<?php

namespace App\Service;

use App\Model\AddressStore;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class AddressStoreService extends BaseService
{
    public function createAddress(array $data, $isTransaction = null)
    {
        return $this->execute(function() use ($data){
            $fields = Validator::validate([
                "public_area" => $data['public_area'],
                "number" => $data['number'],
                "district" => $data['district'],
                "city" => $data['city'],
                "state"=> $data['state'],
                "zip_code" => $data['zip_code'],
                "is_default" => $data['is_default'],
                "is_show" => $data['is_show']
            ]);
            
            $fields['complement'] = null;
            
            if(isset($data['complement']) && !empty($data['complement'])){
                $fields['complement'] = $data['complement'];
            }

            $AddressStore = new AddressStore($this->pdo);

            if($fields['is_default']){
                $result = $AddressStore->setIsDefault();

                if(!$result){
                    throw new Exception("Não foi possível deixar endereço como padrão");
                }
            }

            $resultAddress = $AddressStore->createAddress($data);

            if(!$resultAddress){
                throw new Exception("Não foi possível cadastrar endereço");
            }

            return "Endereço cadastrado com sucesso";
        }, $isTransaction);
    }
}