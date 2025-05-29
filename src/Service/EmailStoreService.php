<?php

namespace App\Service;

use App\Model\EmailStore;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class EmailStoreService extends BaseService
{
    public function createEmail(array $data, ?bool $isTransaction = null)
    {
        return $this->execute(function () use ($data) {

            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "is_show" => $data['is_show'] ?? ''
            ]);

            $EmailStore = new EmailStore($this->pdo);

            $inactiveResult = $EmailStore->setIsDefault();

            if (!$inactiveResult) {
                throw new Exception("Não foi possível inativar emails.");
            }

            $result = $EmailStore->createEmailStore($fields);

            if (!$result) {
                throw new Exception("Não foi possível criar email para a loja.");
            }

            return "Email cadastrado com sucesso";
        }, $isTransaction);
    }

    public function getEmails()
    {
        return $this->execute(function () {
            $EmailStore = new EmailStore($this->pdo);

            $result = $EmailStore->getEmails();

            if (!$result) {
                throw new Exception("Não foi possível realizar resgate dos emails");
            }

            return $result;
        });
    }

    public function getEmailDefault()
    {
        return $this->execute(function() {
            $EmailStore = new EmailStore($this->pdo);

            $result = $EmailStore->getDefault();

            if(!$result){
                throw new Exception("Não foi possível resgatar e-email");
            }

            return $result;
        });
    }

    public function update(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "email" => $data['email'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "is_show" => $data['is_show'] ?? '',
                "id" => $data['id'] ?? ''
            ]);

            $EmailStore = new EmailStore($this->pdo);

            $email = $EmailStore->getEmail(intval($data['id']));

            if(!$fields['is_default'] && $email['is_default']){
                throw new Exception("É necessário que haja pelo menos um e-mail padrão");
            }

            if ($fields['is_default']) {
                $inactiveResult = $EmailStore->setIsDefault();

                if (!$inactiveResult) {
                    throw new Exception("Não foi possível inativar emails.");
                }
            }

            $result = $EmailStore->updateEmail($fields);

            if (!$result) {
                throw new Exception("Não foi possível editar email");
            }

            return "E-mail editado com sucesso.";
        });
    }

    public function delete(int $id){
        return $this->execute(function() use ($id){
            $EmailStore = new EmailStore($this->pdo);

            $email = $EmailStore->getEmail($id);

            if(!$email){
                throw new Exception("Não foi possível buscar endereço");
            }

            if($email['is_default']){
                throw new Exception("Não foi possível deletar endereço padrão");
            }

            $result = $EmailStore->delete($id);

            if(!$result){
                throw new Exception("Não foi possível deletar endereço");
            }

            return "E-mail deletado com sucesso";
        });
    }
}
