<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Brand;
use App\Utils\Validator;
use Exception;
use PDOException;

class BrandService
{

    public static function create(array $data)
    {
        try{

            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $Brand = Brand::create($fields);

            if(!$Brand){
                throw new Exception("Não foi possível Cadastrar Marca");
            }

            return "Marca criada com sucesso";

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAll()
    {
        try{

            $Brand = Brand::getAll();

            return [
                "message" => "Marcas Resgatadas",
                "content" => $Brand
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function update(array $data, int $id)
    {
        try{

            $fields = Validator::validate([
                "name" => $data['name'] ?? ''
            ]);

            $fields['id'] = $id;

            $Brand = Brand::update($fields);

            if(!$Brand){
                throw new Exception("Não foi possível atualizar a marca");
            }

            return "Marca atualizada com sucesso";

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function delete(int $id)
    {
        try{

            $Brand = Brand::delete($id);

            if(!$Brand){
                throw new Exception("Não foi possível deletar a marca");
            }

            return "Marca deletada com sucesso";

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}