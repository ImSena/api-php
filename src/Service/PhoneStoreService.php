<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\PhoneStore;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;
use PDOException;

class PhoneStoreService extends BaseService
{
    public function createPhoneStore(array $data, ?bool $isTransaction = null)
    {

        return $this->execute(function () use ($data) {
            $PhoneStore = new PhoneStore($this->pdo);

            $resultInactive = $PhoneStore->setIsDefault();

            if (!$resultInactive) {
                throw new Exception("Não foi possível inativar contato.");
            }

            $result = $PhoneStore->createPhone($data);

            if (!$result) {
                throw new Exception("Não foi possível inserir novo contato.");
            }

            return "Contato inserido com sucesso.";
        }, $isTransaction);
    }

    public function getPhones()
    {
        return $this->execute(function () {
            $PhoneStore = new PhoneStore($this->pdo);

            $result = $PhoneStore->getPhones();

            if (!$result) {
                throw new Exception("Não foi possível resgatar contatos.");
            }

            return $result;
        });
    }

    public function update(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "type" => $data['type'] ?? '',
                "number" => $data['number'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "is_show" => $data['is_show'] ?? '',
                "id" => $data['id'] ?? ''
            ]);

            $PhoneStore = new PhoneStore($this->pdo);

            if ($fields['is_default']) {
                $resultInactive = $PhoneStore->setIsDefault();

                if (!$resultInactive) {
                    throw new Exception("Não foi possível inativar contato.");
                }
            }

            $result = $PhoneStore->updatePhones($fields);

            if (!$result) {
                throw new Exception("Não foi possível atualizar contato.");
            }

            return "Contato atualizado com sucesso.";
        });
    }
}
