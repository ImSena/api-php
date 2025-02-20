<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Model\Admin;
use App\Model\Token_admin;
use App\Utils\Validator;
use Exception;
use PDOException;

class AccountAdminService
{
    public static function resetPasswordAdmin(array $data)
    {
        try {
            $fields = Validator::validate([
                "token" => $data['token'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['decoded']['error'])) {
                Token_admin::inactiveToken($data['token']);
                throw new Exception($token['decoded']['error']);
            }

            $tokenModel = Token_admin::select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível atualizar a senha, pois o link está expirado!");
            }
            $admin = Admin::updateAccess($fields, $token['decoded']['id_user']);
            if (!$admin) {
                throw new Exception("Não foi possível atualizar a senha. Tente novamente mais tarde");
            }
            $tokenModel = Token_admin::inactiveToken($data['token']);

            return "Senha alterada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function activeAccountAdmin(array $data)
    {
        try {
            $fields = Validator::validate([
                'token' => $data['token'] ?? ''
            ]);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['decoded']['error'])) {
                Token_admin::inactiveToken($data['token']);
                throw new Exception($token['decoded']['error']);
            }

            $tokenModel = Token_admin::select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível ativar a conta, pois o link está expirado!");
            }

            $admin = Admin::activeAdmin('ACTIVE', $token['decoded']['id_user']);

            if (!$admin) {
                throw new Exception("Não foi possível atualizar a conta. Tente novamente mais tarde");
            }

            $tokenModel = Token_admin::inactiveToken($data['token']);

            return "Conta ativada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
