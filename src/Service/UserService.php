<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Jwt\JwtAuth;
use App\Model\Token_user;
use App\Utils\SendEmail;
use App\Utils\Validator;
use Exception;
use App\Model\User;
use DateTime;
use PDOException;

class UserService{

    public static function create(array $data)
    {
        try{
            $fields = Validator::validate([
                "type" => $data['type'] ?? '',
                "username" => $data['username'] ?? '',
                "email" => $data['email'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $person = $data['person'];
            if($fields['type'] == "LEGAL"){
                $fields['person'] = Validator::validateLegalPerson([
                    "cnpj" => $person['cnpj'] ?? '',
                    "corporate_name" => $person['corporate_name'] ?? '',
                    "trade_name" => $person['trade_name'] ?? '',
                    "state_registration" => $person['state_registration'] ?? 'ISENTO'
                ]);
            }else{
                $fields['person'] = Validator::validateNaturalPerson([
                    "cpf" => $person['cpf'] ?? '',
                    'dt_birth' => $person['dt_birth'] ?? '',
                    "gender" => $person['gender'] ?? ''
                ]);
            }

            $address = $data['address'];
            $fields['address'] = Validator::validateAddress([
                "public_area" => $address['public_area'] ?? '',
                "number" => $address['number'] ?? '',
                "district" => $address['district'] ?? '',
                "city" => $address['city'] ?? '',
                "state" => $address['state'] ?? '',
                "zip_code" => $address['zip_code'] ?? ''
            ]);

            $phone = $data['phone'];

            $fields['phone'] = Validator::validatePhone([
                "type" => $phone['type'] ?? '',
                "number" => $phone['number'] ?? ''
            ]);

            self::isUserExists($fields);

            $user = User::create($fields);

            if(!$user){
                throw new Exception("Não foi possível criar a conta. Tente novamente mais tarde");
            }

            return "Conta criada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private static function isUserExists(array $user){
        try{

            $fields = [];

            $fields['login'] = $user['email'];

            $userModel = User::select($fields);

            if($userModel){
                throw new Exception("Usuário já cadastrado! Realize seu login");
            }

            switch($user['type']){
                case 'LEGAL':
                    $fields['login'] = $user['person']['cnpj'];
                break;
                case 'NATURAL':
                    $fields['login'] = $user['person']['cpf'];
                break;
                default:
                    throw new Exception("Não foi possível criar a conta, pois verificação de conta falhou. Tente novamente mais tarde");
            }

            $userModel = User::select($fields);

            if($userModel){
                throw new Exception("Usuário já cadastrado! Realize seu login");
            }
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public static function login(array $data)
    {
        try{

            $fields = Validator::validate([
                "login" => $data['login'] ?? '',
                "password" => $data['password'] ?? '',
                "type" => $data['type'] ?? 'EMAIL'
            ]);

            switch($fields['type'])
            {
                case "EMAIL": 
                    $fields['login'] = Validator::validateEmail($fields['login']);
                break;
                case "CPF":
                    $fields['login'] = Validator::validateCPF($fields['login']);
                break;
                case "CNPJ":
                    $fields['login'] = Validator::validateCNPJ($fields['login']);
                break;
            }

            $user = User::select($fields);

            if(!$user)
            {
                throw new Exception("Usuário ou senha incorretos.");
            }

            if(!password_verify($fields['password'], $user['password']))
            {
                throw new Exception("Usuário ou senha incorretos.");
            }

            $firstAccess = $user['status'] === "INACTIVE" ? true : false;

            if($firstAccess){
                return [
                    "message" => self::activeAccountLink($fields, true),
                    "firstAccess" => true
                ];
            }else{
                $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'user', $user['status'], '7 days');
                return [
                    "message" => "login efetuado com sucesso!",
                    "status" => $user['status'],
                    "name" => $user['username'],
                    "token" => $token,
                ];
            }


        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function activeAccountLink(array $data, bool $sendEmail = false)
    {
        try {

            $fields = Validator::validate([
                "login" => $data['login'] ?? '',
            ]);

            $user = User::select($fields);

            if (!$user) {
                throw new Exception("Usuário não encontrado!");
            }

            if ($user['status'] == "ACTIVE") {
                throw new Exception("Usuário já está ativo");
            }

            if ($sendEmail) {
                $tokenStatus = Token_user::selectLastToken($user);

                if ($tokenStatus) {
                    $dateCreated = new DateTime($tokenStatus['created_at']);
                    $dateNow = new DateTime('now');
                    $diff = $dateCreated->diff($dateNow);

                    if ($diff->i >= 30 || $diff->h > 0 || $diff->days > 0) {
                        return "Por favor, valide sua conta para que possa usá-la";
                    } else {
                        return "Foi enviado um link de ativação para o seu email!";
                    }
                }
            }

            $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'USER', $user['status'], '30 minutes');

            $fields = [
                'id_user' => $user['id_user'],
                'token' => $token,
                'type' => 'ACTIVE'
            ];

            $token_user = Token_user::inactiveAll($user['id_user'], $fields['type']);

            $token_user = Token_user::create($fields);

            if (!$token_user) {
                throw new Exception("Não foi possível gerar link de ativação de conta");
            }

            $info_user = [
                'name' => $user['username'],
                'email' => $user['email'],
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
                "login" => $data['login'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['login']);
            $fields['type'] = "FORGET";

            $user = User::select($fields);

            if (!$user) {
                throw new Exception("Usuário não encontrado.");
            }

            $fields['id_user'] = $user['id_user'];

            $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'USER', $user['status'], '15 minutes');

            $info_user = [
                'name' => $user['username'],
                'email' => $user['email'],
                'token' => $token,
                'type' => 'FORGET',
            ];

            $fields['token'] = $token;

            Token_user::inactiveAll($fields['id_user'], $fields['type']);

            $token = Token_user::create($fields);

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