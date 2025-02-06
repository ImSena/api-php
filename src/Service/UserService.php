<?php

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Utils\Validator;
use Exception;

class UserService{

    public static function register(array $data)
    {
        try{

            $fields = Validator::validate([
                "type" => $data['type'] ?? ''
            ]);

            if($fields['type'] == "legal"){
                $fields['person'] = Validator::validateLegalPerson([
                    "cnpj" => $data['cnpj'] ?? '',
                    "corporate" => $data['corporate_name'] ?? '',
                    "trade_name" => $data['trade_name'] ?? '',
                    "state_registration" => $data['state_registration'] ?? ''
                ]);
            }

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}