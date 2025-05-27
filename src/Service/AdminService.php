<?php

namespace App\Service;

use App\Jwt\JwtAuth;
use App\Model\Admin;
use App\Model\TokenAdmin;
use App\Service\Base\BaseService;
use App\Utils\SendEmail;
use App\Utils\Validator;
use DateTime;
use Exception;

class AdminService extends BaseService
{
    public function create(array $data, bool $isSuper)
    {
        return $this->execute(function () use ($data, $isSuper) {
            $Admin = new Admin($this->pdo);

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

            $admin = $Admin->create($fields);

            if (!$admin) {
                throw new Exception("Não foi possível criar um administrador");
            }

            return "Administrador cadastrado com sucesso";
        });
    }

    public function login(array $data)
    {
        return $this->execute(function () use ($data) {
            $Admin = new Admin($this->pdo);

            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
                'password' => $data['password'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);

            $admin = $Admin->select($fields);

            if (!$admin) {
                throw new Exception("Usuário ou senha incorretas");
            }

            if (!password_verify($data['password'], $admin['password'])) {
                throw new Exception("Usuário ou senha incorretas");
            }

            $firstAccess = $admin['status'] == 'INACTIVE' ? true : false;

            if ($firstAccess) {
                return [
                    'message' => self::activeAccountLink($admin, true),
                    'firstAccess' => true,
                ];
            } else {
                $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', $admin['status'], '7 days');

                return [
                    'message' => 'Login efetuado com sucesso!',
                    'user' => $admin['name'],
                    'rule' => 'admin',
                    'token' => $token,
                    'status' => $admin['status'] === "ACTIVE" ? true : false,
                ];
            }
        });
    }

    public function activeAccountLink(array $data, bool $sendEmail = false)
    {
        return $this->execute(function () use ($data, $sendEmail) {
            $Admin = new Admin($this->pdo);
            $TokenAdmin = new TokenAdmin($this->pdo);
            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
            ]);

            $admin = $Admin->select($fields);

            if (!$admin) {
                throw new Exception("Usuário não encontrado!");
            }

            if ($admin['status'] == "ACTIVE") {
                throw new Exception("Usuário já está ativo");
            }

            if ($sendEmail) {
                $tokenStatus = $TokenAdmin->selectLastToken($admin);

                if ($tokenStatus) {
                    $dateCreated = new DateTime($tokenStatus['created_at']);
                    $dateNow = new DateTime('now');
                    $diff = $dateCreated->diff($dateNow);

                    if ($diff->i < 30 && $diff->h == 0 && $diff->days == 0) {
                        return "Por favor, valide sua conta para que possa utilizá-la!";
                    }
                }
            }

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], $admin['permission'], $admin['status'], '30 minutes');

            $fields = [
                'id_admin' => $admin['id_admin'],
                'token' => $token,
                'type' => 'ACTIVE'
            ];

            $token_admin = $TokenAdmin->inactiveAll($admin['id_admin'], $fields['type']);

            $token_admin = $TokenAdmin->create($fields);

            if (!$token_admin) {
                throw new Exception("Não foi possível gerar link de ativação de conta");
            }

            $info_user = [
                'name' => $admin['name'],
                'email' => $admin['email'],
                'link' => URL_EMAIL . "active-account?token=" . $token
            ];

            $sendMail = $this->getNotifier()->sendActiveAccount($info_user);

            if (!$sendMail) {
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }

            return "Foi enviado um link para ativar sua conta!";
        });
    }

    public function forgetPassword(array $data)
    {
        return $this->execute(function () use ($data) {
            $Admin = new Admin($this->pdo);
            $TokenAdmin = new TokenAdmin($this->pdo);

            $fields = Validator::validate([
                "email" => $data['email'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['email']);
            $fields['type'] = "FORGET";

            $admin = $Admin->select($fields);

            if (!$admin) {
                throw new Exception("Usuário não encontrado.");
            }

            $fields['id_admin'] = $admin['id_admin'];

            $token = JwtAuth::renderToken($admin['name'], $admin['id_admin'], 'admin', $admin['status'], '15 minutes');

            $info_user = [
                'name' => $admin['name'],
                'email' => $admin['email'],
                'link' => URL_EMAIL . "active-account?token=" . $token,
                'type' => 'FORGET'
            ];

            $fields['token'] = $token;

            $TokenAdmin->inactiveAll($fields['id_admin'], $fields['type']);

            $token = $TokenAdmin->create($fields);

            if (!$token) {
                throw new Exception("Não foi possível gerar o link. Tente novamente mais tarde");
            }

            $sendMail = $this->getNotifier()->sendResetPassword($info_user);

            if (!$sendMail) {
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }


            return "Foi enviado um link de recuperação para o email.";
        });
    }

    public function getInfoAdmin($permission = 'SUPER')
    {

        return $this->execute(function () use ($permission) {
            $permissions = [
                'SUPER',
                'FINANCE',
                'COMMON',
            ];

            if (!in_array($permission, $permissions)) {
                throw new Exception("Permissão inexistente");
            }

            $Admin = new Admin($this->pdo);
            $result = $Admin->getInfoAdmin($permission);

            return $result;
        });
    }
}
