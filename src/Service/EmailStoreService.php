<?php

namespace App\Service;

use App\Model\EmailStore;
use App\Service\Base\BaseService;
use Exception;

class EmailStoreService extends BaseService
{
    public function createEmail(array $data, bool $isTransaction = false)
    {
        if ($isTransaction) {
            return $this->execute(function () use ($data) {
                $this->createInternalEmailStore($data);
            }, $isTransaction);
        }

        return $this->createInternalEmailStore($data);
    }

    private function createInternalEmailStore(array $data)
    {
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
    }
}
