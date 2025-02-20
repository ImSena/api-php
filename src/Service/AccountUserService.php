<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Model\Token_user;
use App\Utils\Validator;
use Exception;
use PDOException;
use App\Model\User;

class AccountUserService
{
    public static function resetPassword(array $data)
    {
        try {
            $fields = Validator::validate([
                "token" => $data['token'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['decoded']['error'])) {
                Token_user::inactiveToken($data['token']);
                throw new Exception($token['decoded']['error']);
            }

            $tokenModel = Token_user::select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível atualizar a senha, pois o link está expirado!");
            }
            $admin = User::updateAccess($fields, $token['decoded']['id_user']);
            if (!$admin) {
                throw new Exception("Não foi possível atualizar a senha. Tente novamente mais tarde");
            }
            $tokenModel = Token_user::inactiveToken($data['token']);

            return "Senha alterada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function activeAccount(array $data)
    {
        try {
            $fields = Validator::validate([
                'token' => $data['token'] ?? ''
            ]);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['decoded']['error'])) {
                Token_user::inactiveToken($data['token']);
                throw new Exception($token['decoded']['error']);
            }

            $tokenModel = Token_user::select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível ativar a conta, pois o link está expirado!");
            }

            $user = User::activeUser('ACTIVE', $token['decoded']['id_user']);

            if (!$user) {
                throw new Exception("Não foi possível atualizar a conta. Tente novamente mais tarde");
            }

            $tokenModel = Token_user::inactiveToken($data['token']);

            return "Conta ativada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
