<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Model\Admin;
use App\Model\Token;
use App\Model\Token_admin;
use App\Utils\SendEmail;
use App\Utils\Validator;
use Exception;
use PDOException;

class AdminService
{
    public static function create(array $data, bool $isSuper)
    {
        try {
            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "email" => $data['email'] ?? '',
                "password" => $data['password'] ?? '',
                "permission" => $data['permission'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);
            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            if ($isSuper) {
                $fields['permission'] = "SUPER";
            }

            $admin = Admin::create($fields);

            if (!$admin) {
                throw new Exception("Não foi possível criar um administrador");
            }

            return "Administrador cadastrado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function login(array $data)
    {
        try {
            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);

            $admin = Admin::select($fields);

            if (!$admin) {
                throw new Exception("Usuário ou senha incorretas");
            }

            if (!password_verify($data['password'], $admin['password'])) {
                throw new Exception("Usuário ou senha incorretas");
            }

            $firstAccess = $admin['status'] == 'INACTIVE' ? true : false;

            if ($firstAccess) {
                self::activeAccountLink($admin);
                return[
                    'message' => self::activeAccountLink($admin),
                    'firstAccess' => true,
                ];
            } else {
                $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', $admin['status'], '2 hours');

                return [
                    'message' => 'Login efetuado com sucesso!',
                    'user' => $admin['name'],
                    'rule' => 'admin',
                    'token' => $token,
                    'status' => $admin['status'] === "ACTIVE" ? true : false,
                ];
            }
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function activeAccountLink(array $data)
    {
        try {

            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
            ]);

            $admin = Admin::select($fields);

            if(!$admin){
                throw new Exception("Usuário não encontrado!");
            }

            if($admin['status'] == "ACTIVE"){
                throw new Exception("Usuário já está ativo");
            }

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], $admin['permission'], $admin['status'], '30 minutes');

            $fields = [
                'id_admin' => $admin['id_admin'],
                'token' => $token,
                'type' => 'ACTIVE'
            ];

            $token_admin = Token_admin::inactiveAll($admin['id_admin'], $fields['type']);

            $token_admin = Token_admin::create($fields);

            if (!$token_admin) {
                throw new Exception("Não foi possível gerar link de ativação de conta");
            }

            $info_user = [
                'name' => $data['name'],
                'email' => $data['email'],
                'token' => $token
            ];
            $sendMail = SendEmail::sendMail($info_user, 'active');

            if (!$sendMail) {
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }

            return "Foi enviado um link para ativar sua conta!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function forgetPassword(array $data)
    {

        try {
            $fields = Validator::validate([
                "email" => $data['email'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);
            $fields['type'] = "FORGET";

            $admin = Admin::select($fields);

            if (!$admin) {
                throw new Exception("Usuário não encontrado.");
            }

            $fields['id_admin'] = $admin['id_admin'];

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', $admin['status'], '15 minutes');

            $info_user = [
                'name' => $admin['name'],
                'email' => $admin['email'],
                'token' => $token
            ];

            $fields['token'] = $token;

            Token_admin::inactiveAll($fields['id_admin'], $fields['type']);

            $token = Token_admin::create($fields);

            if (!$token) {
                throw new Exception("Não foi possível gerar o link. Tente novamente mais tarde");
            }

            $sendMail = SendEmail::sendMail($info_user, 'forget');

            if (!$sendMail) {
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }


            return "Foi enviado um link de recuperação para o email.";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
