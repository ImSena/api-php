<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Picture_products;
use App\Model\Product;
use App\Utils\Validator;
use Exception;
use PDOException;

class ProductService
{
    public static function create(array $data)
    {
        try {
            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? '',
                "products" => $data['products'] ?? '',
            ]);

            $Product = Product::create($fields);

            if (isset($Product['error'])) {
                throw new Exception($Product['error']);
            }

            return "Produto cadastrado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
