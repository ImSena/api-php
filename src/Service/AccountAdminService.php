<?php

namespace App\Service;

use App\Jwt\JwtAuth;
use App\Model\Admin;
use App\Model\TokenAdmin;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class AccountAdminService extends BaseService
{
    public function resetPasswordAdmin(array $data)
    {
        return $this->execute(function () use ($data) {
            $TokenAdmin = new TokenAdmin($this->pdo);
            $Admin = new Admin($this->pdo);
            $fields = Validator::validate([
                "token" => $data['token'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['error'])) {
                $TokenAdmin->inactiveToken($data['token']);
                throw new Exception($token['error']);
            }

            $tokenModel = $TokenAdmin->select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível atualizar a senha, pois o link está expirado!");
            }
            $admin = $Admin->updateAccess($fields, $token['decoded']['id_user']);
            if (!$admin) {
                throw new Exception("Não foi possível atualizar a senha. Tente novamente mais tarde");
            }
            $tokenModel = $TokenAdmin->inactiveToken($data['token']);

            return "Senha alterada com sucesso!";
        });
    }

    public function activeAccountAdmin(array $data)
    {
        return $this->execute(function () use ($data) {
            $TokenAdmin = new TokenAdmin($this->pdo);
            $Admin = new Admin($this->pdo);

            $fields = Validator::validate([
                'token' => $data['token'] ?? ''
            ]);

            $token = JwtAuth::verifyToken($fields['token']);

            if (is_array($token) && isset($token['error'])) {
                $TokenAdmin->inactiveToken($data['token']);
                throw new Exception($token['error']);
            }

            $tokenModel = $TokenAdmin->select($data['token']);

            if (isset($tokenModel['status']) && $tokenModel['status'] == 'INACTIVE') {
                throw new Exception("Não foi possível ativar a conta, pois o link está expirado!");
            }

            $admin = $Admin->activeAdmin('ACTIVE', $token['decoded']['id_user']);

            if (!$admin) {
                throw new Exception("Não foi possível atualizar a conta. Tente novamente mais tarde");
            }

            $tokenModel = $TokenAdmin->inactiveToken($data['token']);

            return "Conta ativada com sucesso!";
        });
    }
}
