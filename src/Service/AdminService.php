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

            if($isSuper){
                $fields['permission'] = "SUPER";
            }

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

    public static function login(array $data)
    {
        try{
            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);

            $admin = Admin::select($fields);

            if(!$admin){
                throw new Exception("Usuário ou senha incorretas");
            }

            if(!password_verify($data['password'], $admin['password'])){
                throw new Exception("Usuário ou senha incorretas");
            }

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', '2 hours');

            return [
                'message'=> 'Login efetuado com sucesso!',
                'user' => $admin['name'],
                'rule' => 'admin',
                'token' => $token
            ];

        }catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function forgetPassword(array $data)
    {

        try{
            $fields = Validator::validate([
                "email" => $data['email'] ?? ''
            ]);
    
            $fields['email'] = Validator::validateEmail($fields['email']);
            $fields['type'] = "FORGET";
    
            $admin = Admin::select($fields);

            if(!$admin){
                throw new Exception("Usuário não encontrado.");
            }
        
            $fields['id_admin'] = $admin['id_admin'];

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', '15 minutes');

            $info_user = [
                'name' => $admin['name'],
                'email' => $admin['email'],
                'token' => $token
            ];

            $fields['token'] = $token;

            $token = Token_admin::create($fields);

            if(!$token){
                throw new Exception("Não foi possível gerar o link. Tente novamente mais tarde");
            }
            
            $sendMail = SendEmail::sendMail($info_user, 'forget');

            if(!$sendMail){
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }


            return "Foi enviado um link de recuperação para o email.";
        }catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }

    }

}
