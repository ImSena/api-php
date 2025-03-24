<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Variation;
use App\Utils\Validator;
use Exception;
use PDOException;

class VariationService
{
    public static function createVariation(array $data)
    {
        try {
            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $Variation = Variation::createVariation($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível criar atributo");
            }

            return "Atributo criado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAllVariations()
    {
        try {
            $Variation = Variation::getAllVariants();

            return [
                'message' => "Resgatado com sucesso",
                'content' => $Variation
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function updateVariation(array $data, int $id)
    {
        try {
            $fields = Validator::validate([
                "name" => $data['name']
            ]);

            $fields['id'] = $id;

            $Variation = Variation::updateVariation($fields);

            if (!$Variation) {
                throw new Exception("Não foi possível atualizar Variação");
            }

            return "Variação atualizada com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function deleteVariation(int $id)
    {
        try {
            $Variation = Variation::deleteVariation($id);

            if (!$Variation) {
                throw new Exception("Não foi possível deletar atributo");
            }

            return "Atributo deletado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function createValue(array $data)
    {
        try {
            $fields = Validator::validate([
                "id_variant_attribute" => $data['id_variant_attribute'] ?? '',
                "value" => $data['value'] ?? '',
                "viewer" => $data['viewer'] ?? 'LIST'
            ]);

            $Variation = Variation::createValue($fields);

            if(!$Variation){
                throw new Exception("Não foi possível adicionar valor a variação");
            }

            return "Variação criada com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getValueVariation(int $id)
    {
        try{

            $fields = Validator::validate([
                "id" => $id ?? ''
            ]);

            $Variation = Variation::getValuesVariation($fields['id']);

            return [
                "message" => "Valores dos atributos resgatados",
                "content" => $Variation
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function updateValueVariation(array $data, int $id)
    {
        try{
            $fields = Validator::validate([
                "id" => $id ?? '',
                "value" => $data['value'] ?? ''
            ]);

            $Variation = Variation::updateValue($fields);

            if(!$Variation){
                throw new Exception("Não foi possível atualizar valor");
            }

            return "Valor atualizado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function deleteValue(int $id)
    {
        try{
            $fields = Validator::validate([
                "id" => $id ?? ''
            ]);

            $Variation = Variation::deleteValue($fields['id']);

            if(!$Variation){
                throw new Exception("Não foi possível deletar o valor");
            }

            return "Valor deletado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
