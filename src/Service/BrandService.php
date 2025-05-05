<?php

namespace App\Service;

use App\Model\Brand;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;


class BrandService extends BaseService
{
    public function create(array $data)
    {

        return $this->execute(function () use ($data) {
            $Brand = new Brand($this->pdo);

            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $Brand = $Brand->create($fields);

            if (!$Brand) {
                throw new Exception("Não foi possível Cadastrar Marca");
            }

            return "Marca criada com sucesso";
        });
    }

    public function getAll()
    {
        return $this->execute(function () {
            $Brand = new Brand($this->pdo);
            $Brand = $Brand->getAll();

            return [
                "message" => "Marcas Resgatadas",
                "content" => $Brand
            ];
        });
    }

    public function update(array $data, int $id)
    {
        return $this->execute(function () use ($data, $id) {
            $Brand = new Brand($this->pdo);
            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $fields['id'] = $id;

            $Brand = $Brand->update($fields);

            if (!$Brand) {
                throw new Exception("Não foi possível atualizar a marca");
            }

            return "Marca atualizada com sucesso";
        });
    }

    public function delete(int $id)
    {
        return $this->execute(function () use ($id) {

            $Brand = new Brand($this->pdo);
            $Brand = $Brand->delete($id);

            if (!$Brand) {
                throw new Exception("Não foi possível deletar a marca");
            }

            return "Marca deletada com sucesso";
        });
    }
}
