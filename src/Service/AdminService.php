<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Admin;
use App\Utils\Validator;
use Exception;
use PDOException;

class AdminService
{
    public static function create(array $data)
    {
        try {

            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "email" => $data['email'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $admin = Admin::create($fields);

            if (!$admin) {
                throw new Exception("Não foi possível criar um administrador");
            }

            return "Administrador cadastrado com sucesso";
        }catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
