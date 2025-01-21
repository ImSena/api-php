<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Model\Admin;
use App\Model\Token;
use App\Utils\Validator;
use Exception;
use PDOException;

class ForgetPassword
{
    public static function resetPassword(array $data) {
        try{

            $fields = Validator::validate([
                "token" => $data['token'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $token = JwtAuth::verifyToken($fields['token']);

            if(is_array($token) && isset($token['error'])){
                throw new Exception($token['error']);
            }

            $tokenModel = Token::select($data['token']);

            if(isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE'){
                throw new Exception("Não foi possível atualizar a senha, pois o link está expirado!");
            }

            if($token->rule === 'admin'){
                $admin = Admin::updateAccess($fields, $token->id_user);
                if(!$admin){
                    throw new Exception("Não foi possível atualizar a senha. Tente novamente mais tarde");
                }
            }else if($token->rule === 'user'){

            }

            $tokenModel = Token::inactive($data['token']);

            return "Senha alterada com sucesso!";

        }catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
