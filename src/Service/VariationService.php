<?php

namespace App\Service;

use App\Model\Variation;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;
class VariationService extends BaseService
{
    public function createVariation(array $data)
    {
        return $this->execute(function () use ($data) {
            $Variation = new Variation($this->pdo);
            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $Variation = $Variation->createVariation($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível criar atributo");
            }

            return "Atributo criado com sucesso";
        });
    }

    public function getAllVariations()
    {
        return $this->execute(function () {
            $Variation = new Variation($this->pdo);

            $Variations = $Variation->getAllVariants();

            foreach ($Variations as &$variation) {
                $variation['values'] = $Variation->getValuesVariation($variation['id_variant_attribute']);
            }

            return [
                'message' => "Resgatado com sucesso",
                'content' => $Variations
            ];
        });
    }

    public function updateVariation(array $data, int $id)
    {
        return $this->execute(function () use ($data, $id) {
            $Variation = new Variation($this->pdo);

            $fields = Validator::validate([
                "name" => $data['name']
            ]);

            $fields['id'] = $id;

            $Variation = $Variation->updateVariation($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível atualizar Variação");
            }

            return "Variação atualizada com sucesso";
        });
    }

    public function deleteVariation(int $id)
    {
        return $this->execute(function () use ($id) {
            $Variation = new Variation($this->pdo);

            $Variation = $Variation->deleteVariation($id);

            if (!$Variation) {
                throw new Exception("Não foi possível deletar atributo");
            }

            return "Atributo deletado com sucesso";
        });
    }

    public function createValue(array $data)
    {
        return $this->execute(function () use ($data) {
            $Variation = new Variation($this->pdo);

            $fields = Validator::validate([
                "id_variant_attribute" => $data['id_variant_attribute'] ?? '',
                "value" => $data['value'] ?? '',
                "viewer" => $data['viewer'] ?? 'LIST'
            ]);

            $Variation = $Variation->createValue($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível adicionar valor a variação");
            }

            return "Variação criada com sucesso";
        });
    }

    public function getValueVariation(int $id)
    {
        return $this->execute(function () use ($id) {
            $Variation = new Variation($this->pdo);

            $fields = Validator::validate([
                "id" => $id ?? ''
            ]);

            $Variation = $Variation->getValuesVariation($fields['id']);

            return [
                "message" => "Valores dos atributos resgatados",
                "content" => $Variation
            ];
        });
    }

    public function updateValueVariation(array $data, int $id)
    {
        return $this->execute(function () use ($data) {
            $Variation = new Variation($this->pdo);

            $fields = Validator::validate([
                "id" => $id ?? '',
                "value" => $data['value'] ?? ''
            ]);

            $Variation = $Variation->updateValue($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível atualizar valor");
            }

            return "Valor atualizado com sucesso";
        });
    }

    public function deleteValue(int $id)
    {
        return $this->execute(function () use ($id) {
            $Variation = new Variation($this->pdo);

            $fields = Validator::validate([
                "id" => $id ?? ''
            ]);

            $Variation = $Variation->deleteValue($fields['id']);

            if (!$Variation) {
                throw new Exception("Não foi possível deletar o valor");
            }

            return "Valor deletado com sucesso";
        });
    }
}
