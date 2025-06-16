<?php

namespace App\Service;

use App\Jwt\JwtAuth;
use App\Model\Phone;
use App\Model\TokenUser;
use App\Service\Base\BaseService;
use App\Utils\SendEmail;
use App\Utils\Validator;
use Exception;
use App\Model\User;
use DateTime;

require_once __DIR__ . "/../../config.php";
class UserService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function () use ($data) {
            $User = new User($this->pdo);

            $fields = Validator::validate([
                "type" => $data['type'] ?? '',
                "username" => $data['username'] ?? '',
                "email" => $data['email'] ?? '',
                "password" => $data['password'] ?? ''
            ]);

            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

            $person = $data['person'];
            if ($fields['type'] == "LEGAL") {
                $fields['person'] = Validator::validateLegalPerson([
                    "cnpj" => $person['cnpj'] ?? '',
                    "corporate_name" => $person['corporate_name'] ?? '',
                    "trade_name" => $person['trade_name'] ?? '',
                ]);

                $fields['person']["state_registration"] = isset($person['state_registration']) ? $person['state_registration'] : 'ISENTO';
            } else {
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

            if (isset($data['address']['complement'])) {
                $fields["complement"] = $address['complement'];
            }

            $phone = $data['phone'];

            $fields['phone'] = Validator::validatePhone([
                "type" => $phone['type'] ?? '',
                "number" => $phone['number'] ?? ''
            ]);

            $result = $this->isUserExists($fields);

            if(isset($result['error'])){
                throw new Exception($result['error']);
            }

            $user = $User->create($fields);

            if (!$user) {
                throw new Exception("Não foi possível criar a conta. Tente novamente mais tarde");
            }

            return "Conta criada com sucesso!";
        });
    }

    private function isUserExists(array $user)
    {
        return $this->execute(function () use ($user) {
            $User = new User($this->pdo);

            $fields = [];

            $fields['login'] = $user['email'];

            $userModel = $User->select($fields);

            if ($userModel) {
                throw new Exception("Usuário já cadastrado! Realize seu login");
            }

            switch ($user['type']) {
                case 'LEGAL':
                    $fields['login'] = $user['person']['cnpj'];
                    break;
                case 'NATURAL':
                    $fields['login'] = $user['person']['cpf'];
                    break;
                default:
                    throw new Exception("Não foi possível criar a conta, pois verificação de conta falhou. Tente novamente mais tarde");
            }

            $userModel = $User->select($fields);

            if ($userModel) {
                throw new Exception("Usuário já cadastrado! Realize seu login");
            }

            return true;
        });
    }

    public function login(array $data)
    {
        return $this->execute(function () use ($data) {
            $User = new User($this->pdo);

            $fields = Validator::validate([
                "login" => $data['login'] ?? '',
                "password" => $data['password'] ?? '',
                "type" => $data['type'] ?? 'EMAIL'
            ]);

            switch ($fields['type']) {
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

            $user = $User->select($fields);

            if (!$user) {
                throw new Exception("Usuário ou senha incorretos.");
            }

            if (!password_verify($fields['password'], $user['password'])) {
                throw new Exception("Usuário ou senha incorretos.");
            }

            $firstAccess = $user['status'] === "INACTIVE" ? true : false;

            if ($firstAccess) {
                return [
                    "message" => $this->activeAccountLink($fields, true),
                    "firstAccess" => true
                ];
            } else {
                $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'user', $user['status'], '7 days');
                return [
                    "message" => "login efetuado com sucesso!",
                    "status" => $user['status'],
                    "name" => $user['username'],
                    "token" => $token,
                ];
            }
        });
    }

    public function activeAccountLink(array $data, bool $sendEmail = false)
    {
        return $this->execute(function () use ($data, $sendEmail) {
            $User = new User($this->pdo);
            $TokenUser = new TokenUser($this->pdo);

            $fields = Validator::validate([
                "login" => $data['login'] ?? '',
            ]);

            $user = $User->select($fields);

            if (!$user) {
                throw new Exception("Usuário não encontrado!");
            }

            if ($user['status'] == "ACTIVE") {
                throw new Exception("Usuário já está ativo");
            }

            if ($sendEmail) {
                $tokenStatus = $TokenUser->selectLastToken($user);

                if ($tokenStatus) {
                    $dateCreated = new DateTime($tokenStatus['created_at']);
                    $dateNow = new DateTime('now');
                    $diff = $dateCreated->diff($dateNow);

                    if ($diff->i < 30 && $diff->h == 0 && $diff->days == 0) {
                        return "Por favor, valide sua conta para que possa usá-la";
                    }
                }
            }

            $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'USER', $user['status'], '30 minutes');

            $fields = [
                'id_user' => $user['id_user'],
                'token' => $token,
                'type' => 'ACTIVE'
            ];

            $token_user = $TokenUser->inactiveAll($user['id_user'], $fields['type']);

            $token_user = $TokenUser->create($fields);

            if (!$token_user) {
                throw new Exception("Não foi possível gerar link de ativação de conta");
            }

            $info_user = [
                'name' => $user['username'],
                'email' => $user['email'],
                'link' => URL_EMAIL . "active-account?token=".$token
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
            $User = new User($this->pdo);
            $TokenUser = new TokenUser($this->pdo);

            $fields = Validator::validate([
                "login" => $data['login'] ?? ''
            ]);

            $fields['email'] = Validator::validateEmail($fields['login']);
            $fields['type'] = "FORGET";

            $user = $User->select($fields);

            if (!$user) {
                throw new Exception("Usuário não encontrado.");
            }

            $fields['id_user'] = $user['id_user'];

            $token = JwtAuth::renderToken($user['username'], $user['id_user'], 'USER', $user['status'], '15 minutes');

            $info_user = [
                'name' => $user['username'],
                'email' => $user['email'],
                'link' => URL_EMAIL . "reset-password?token=".$token,
                'type' => 'FORGET',
            ];

            $fields['token'] = $token;

            $TokenUser->inactiveAll($fields['id_user'], $fields['type']);
            
            $sendMail = $this->getNotifier()->sendResetPassword($info_user);

            if (!$sendMail) {
                throw new Exception("Não foi possível enviar o email de recuperação. Tente novamente mais tarde");
            }

            return "Foi enviado um link de recuperação para o email.";
        });
    }

    public function getAllUsers($id)
    {
        return $this->execute(function () use ($id) {
            $User = new User($this->pdo);

            $user = $User->selectAll($id);

            if (!$user) {
                throw new Exception("Usuários não encontrados.");
            }

            foreach ($user as $key => &$value) {
                if ($value['person_type'] === 'Física') {
                    unset($value['corporate_name']);
                    unset($value['trade_name']);
                } else {
                    unset($value['dt_birth']);
                    unset($value['gender']);
                }
            }

            $totalUserActive = $User->getTotalUsers();

            if ($totalUserActive === false) {
                throw new Exception("Não foi possível resgatar usuários ativos");
            }

            $totalUserInactive = $User->getTotalUsers(false);

            if($totalUserInactive === false){
                throw new Exception("Não foi possível resgatar usuários inativos.");
            }

            $totalUsers = intval($totalUserActive['total']) + intval($totalUserInactive['total']);

            $pages = [
                "limit" => 25,
                "inactives" => $totalUserInactive['total'],
                "actives" => $totalUserActive['total'],
                "total" => $totalUsers
            ];

            return ['message' => "Users resgatados", "content" => $user, "pages" => $pages];
        });
    }

    public function getById(int $id)
    {
        return $this->execute(function () use ($id) {
            $user = new User($this->pdo);
            $PhoneService = new PhoneService($this->pdo);
            $User = $user->getById($id);

            if (!$User) {
                throw new Exception("Não foi possível resgatar usuário");
            }

            $phoneService = $PhoneService->getAllByIdUser($id);

            if ($User['person_type'] == "Física") {
                unset($User['cnpj']);
                unset($User['corporate_name']);
                unset($User['trade_name']);
                unset($User['state_registration']);
            } else {
                unset($User['cpf']);
                unset($User['dt_birth']);
                unset($User['gender']);
            }

            foreach ($phoneService['content'] as $phone) {
                unset($phone['id_user']);
                $User['contact'][] = $phone;
            }

            return [
                "message" => "Usuário resgatado com sucesso",
                "content" => $User
            ];
        });
    }

    public function editUser(array $data)
    {
        return $this->execute(function() use ($data){
            $fields = Validator::validate([
                "username" => $data['username'] ?? '',
                "type" => $data['type'] ?? '',
                "person" => $data['person'] ?? '',
                "contact" => $data['contact'] ?? '',
                "id_user" => $data['id_user'] ?? ''
            ]);
            $person = $fields['person'];

            if($fields['type'] == "NATURAL"){

                $fields['person'] = $fields['person'] = Validator::validateNaturalPerson([
                    "cpf" => $person['cpf'] ?? '',
                    'dt_birth' => $person['dt_birth'] ?? '',
                    "gender" => $person['gender'] ?? ''
                ]);
            }else if($fields['type'] == "LEGAL"){
                $fields['person'] = Validator::validateLegalPerson([
                    "cnpj" => $person['cnpj'] ?? '',
                    "corporate_name" => $person['corporate_name'] ?? '',
                    "trade_name" => $person['trade_name'] ?? '',
                    "state_registration" => $person['state_registration'] ?? 'ISENTO'
                ]);
            }else{
                throw new Exception("Tipo de user incorreto.");
            }

            $fields['contact'] = Validator::validate([
                "id_phone" => $fields['contact']['id_phone'] ?? '',
                "type" => $fields['contact']['type'] ?? '',
                "number" => $fields['contact']['number'] ?? ''
            ]);

            $user = $this->getById($fields['id_user']);

            if(isset($user['error'])){
                throw new Exception("Não foi possível encontrar usuário.");
            }

            $user = $user['content'];

            $type_user = $user['person_type'] == "Jurídica" ? "LEGAL" : "NATURAL";

            if($type_user !== $fields['type'])
            {
                throw new Exception("É necessário que o tipo de usuário seja o mesmo cadastrado para que ocorra a edição.");
            }

            $fields['person']['id'] = $user['id_person'];

            $User = new User($this->pdo);

            $resultEdit = $User->updateUser($fields);

            if(!$resultEdit){
                throw new Exception("Não foi possível editar usuário.");
            }

            $phone = new Phone($this->pdo);

            $resultPhone = $phone->edit($data);

            if(!$resultPhone){
                throw new Exception("Não foi possível editar telefone");
            }

            return "Usuário editado com sucesso.";

        }, true);
    }
}
