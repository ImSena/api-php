<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Address;
use App\Utils\Validator;
use Exception;
use PDOException;

class AddressService
{
    public static function create(array $data)
    {
        try{

            $fields = Validator::validate([
                "id_user" => $data['id_user'] ?? '',
                "public_area" => $data['public_area'] ?? '',
                "number" => $data['number'] ?? '',
                "district" => $data['district'] ?? '',
                "city" => $data['city'] ?? '',
                "state" => $data['state'] ?? '',
                "zip_code" => $data['zip_code'] ?? ''
            ]);

            $fields['complement'] = $data['complement'] ?? null;

            $Address = Address::create($fields);

            if(!$Address){
                throw new Exception("Não foi possível criar endereço");
            }

            return "Endereço criado com sucesso";


        }catch(PDOException $e){
            return[
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }   
    
    public static function getAll(int $id)
    {
        try{
            $Address = Address::getAll($id);

            if(!$Address){
                throw new Exception("Não foi possível encontrar endereços");
            }

            return [
                'message' => 'Endereços encontrados com sucesso',
                'content' => $Address
            ];

        }catch(PDOException $e){
            return[
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}