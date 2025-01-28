<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Category;
use App\Utils\Validator;
use Exception;
use PDOException;

class CategoryService
{
    public static function createCategory(array $data){
        try{
            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "description"=> $data['description'] ?? ''
            ]);

            if(isset($data['parent_category'])) $fields['parent_category'] = $data['parent_category'];

            $category = Category::create($data);

            if(!$category){
                throw new Exception("Não foi possível cadastrar categoria");
            }

            return "Categoria Cadastrada com sucesso!";
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAllParent()
    {
        try{
            $category = Category::getAllParent();

            return $category;
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function delete(array $data)
    {
        try{

            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? ''
            ]);

            $category = Category::delete($fields);

            if(!$category){
                throw new Exception("Não foi possível deletar categoria");
            }

            return "Categoria deletada com sucesso!";
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}