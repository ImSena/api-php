<?php

namespace App\Service;

use App\Model\EmailStore;
use App\Service\Base\BaseService;
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
        return $this->execute(function(){
            $EmailStore = new EmailStore($this->pdo);

            $result = $EmailStore->getEmails();

            if(!$result){
                throw new Exception("Não foi possível realizar resgate dos emails");
            }

            return $result;
        });
    }
}
