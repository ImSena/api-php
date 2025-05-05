<?php

namespace App\Service;

use App\Model\Phone;
use App\Service\Base\BaseService;
use Exception;
class PhoneService extends BaseService
{
    public function getAllByIdUser(int $id)
    {
        return $this->execute(function() use ($id){
            $Phone = new Phone($this->pdo);
    
            $Phone = $Phone->getAllByIdUser($id);
    
            if (!$Phone) {
                throw new Exception("Não foi possível encontrar telefone");
            }
    
            return [
                'message' => "Contatos resgatados com sucesso.",
                'content' => $Phone
            ];
        });
    }
}
