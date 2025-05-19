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
            $EmailStore = new EmailStore($this->pdo);

            $inactiveResult = $EmailStore->setIsDefault();

            if (!$inactiveResult) {
                throw new Exception("Não foi possível inativar emails.");
            }

            $result = $EmailStore->createEmailStore($data);

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
}
