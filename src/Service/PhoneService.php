<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Phone;
use Exception;
use PDOException;

class PhoneService
{
    public static function getAllByIdUser(int $id){
        try{

            $Phone = new Phone();

            $Phone = $Phone->getAllByIdUser($id);

            if(!$Phone){
                throw new Exception("Não foi possível encontrar telefone");
            }

            return [
                'message' => "Contatos resgatados com sucesso.",
                'content' => $Phone
            ];
        }catch(PDOException $e){
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}