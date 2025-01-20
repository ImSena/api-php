<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Product;
use App\Utils\Validator;
use Exception;
use PDOException;

class ProductService
{
    public static function create(array $data)
    {
        try{
            $fields = Validator::validate([
                'name' => $data['name'] ?? '',
                'value' => $data['value'] ?? '',
                'qtd' => $data['qtd'] ?? ''
            ]);

            $product = Product::create($fields);

            if(!$product){
                throw new Exception("Não foi possível criar o produto.");
            }

            return "Produto Cadastrado com sucesso!";
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
        
    }
}