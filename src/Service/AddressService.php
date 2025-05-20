<?php

namespace App\Service;

use App\Model\Address;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class AddressService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function () use ($data) {
            $Address = new Address($this->pdo);
            $fields = Validator::validate([
                "id_user" => $data['id_user'] ?? '',
                "public_area" => $data['public_area'] ?? '',
                "number" => $data['number'] ?? '',
                "district" => $data['district'] ?? '',
                "city" => $data['city'] ?? '',
                "state" => $data['state'] ?? '',
                "zip_code" => $data['zip_code'] ?? '',
                "is_default" => $data['is_default'] ?? true
            ]);

            $fields['complement'] = $data['complement'] ?? null;

            $resultCreated = $Address->create($fields);

            if (!$resultCreated) {
                throw new Exception("Não foi possível criar endereço");
            }

            return "Endereço criado com sucesso";
        }, true);
    }

    public function edit(array $data){
        return $this->execute(function() use ($data){
            $Address = new Address($this->pdo);

            $fields = Validator::validate([
                "id_user" => $data['id_user'] ?? '',
                "id_address" => $data['id_address'] ?? '',
                "public_area" => $data['public_area'] ?? '',
                "number" => $data['number'] ?? '',
                "district" => $data['district'] ?? '',
                "city" => $data['city'] ?? '',
                "state" => $data['state'] ?? '',
                "zip_code" => $data['zip_code'] ?? '',
                "is_default" => $data['is_default'] ?? false
            ]);

            $fields['complement'] = $data['complement'] ?? null;

            $resultEdit = $Address->edit($fields);

            if(!$resultEdit){
                throw new Exception("Não foi possível editar endereço do usuário.");
            }

            return "Endereço atualizado com sucesso";
        }, true);
    }

    public function getAll(array $data)
    {
        return $this->execute(function () use ($data) {
            $Address = new Address($this->pdo);
            $Address = $Address->getAll($data);

            if (!$Address) {
                throw new Exception("Não foi possível encontrar endereços");
            }

            return [
                'message' => 'Endereços encontrados com sucesso',
                'content' => $Address
            ];
        });
    }

    public function getById(int $id)
    {
        return $this->execute(function () use ($id) {
            $Address = new Address($this->pdo);
            $Address = $Address->getById($id);

            if (!$Address) {
                throw new Exception("Não foi possível encontrar o endereço correspondente");
            }

            return [
                'message' => "Endereço encontrado",
                'content' => $Address
            ];
        });
    }

    public function getByUser(int $id){
        return $this->execute(function () use ($id){
            $Address = new Address($this->pdo);
            $Address = $Address->getByUser($id);

            if(!$Address){
                throw new Exception("Não foi possível resgatar endereço do usuário.");
            }

            return $Address;
        });
    }
}
