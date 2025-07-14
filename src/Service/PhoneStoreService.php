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

            $fields = Validator::validatePhone([
                "type" => $data['type'] ?? '',
                "number" => $data['number'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "is_show" => $data['is_show'] ?? ''
            ]);

            $PhoneStore = new PhoneStore($this->pdo);

            $resultInactive = $PhoneStore->setIsDefault();

            if (!$resultInactive) {
                throw new Exception("Não foi possível inativar contato.");
            }

            $result = $PhoneStore->createPhone($fields);

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
            $fields = Validator::validatePhone([
                "type" => $data['type'] ?? '',
                "number" => $data['number'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "is_show" => $data['is_show'] ?? '',
                "id" => $data['id'] ?? ''
            ]);

            $PhoneStore = new PhoneStore($this->pdo);

            $phone = $PhoneStore->getPhone(intval($fields['id']));

            if(!$fields['is_default'] && $phone['is_default']){
                throw new Exception("É necesário ter pelo menos um endereço padrão");
            }

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

    public function delete(int $id){
        return $this->execute(function() use ($id){
            $phoneStore = new PhoneStore($this->pdo);

            $phone = $phoneStore->getPhone($id);

            if($phone['is_default']){
                throw new Exception("Não é possível deletar o contato padrão");
            }

            $result = $phoneStore->delete(intval($id));

            if(!$result){
                throw new Exception("Não foi possível deletar telefone");
            }

            return "Contato deletado com sucesso.";
        });
    }
}
