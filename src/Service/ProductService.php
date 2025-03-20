<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
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
    public static function getAll()
    {
        try {

            $Products = Product::getAll();

            if (isset($Product['error'])) {
                throw new Exception($Products['error']);
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'branch' => $product['brand_name'],
                        'name' => $product['name'],
                        'variations' => []
                    ];
                }

                $path = Media::getPathToFile($product);
                $extension = MediaService::getExtension($product['file_type']);
                $product['image_path'] = $path . '.' . $extension;
                $result[$id_product]['variations'][] = [
                    'id_product_variant' => $product['id_product_variant'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'qtd_stock' => $product['qtd_stock'],
                    'discount' => $product['discount'],
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            $formattedResult = array_values($result);

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function getAllCategory($id)
    {
        try {

            $Products = Product::getAllCategory($id);

            if (isset($Product['error'])) {
                throw new Exception($Products['error']);
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'branch' => $product['brand_name'],
                        'name' => $product['name'],
                        'variations' => []
                    ];
                }

                $path = Media::getPathToFile($product);
                $extension = MediaService::getExtension($product['file_type']);
                $product['image_path'] = $path . '.' . $extension;
                // Adiciona a variação do produto
                $result[$id_product]['variations'][] = [
                    'id_product_variant' => $product['id_product_variant'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'qtd_stock' => $product['qtd_stock'],
                    'discount' => $product['discount'],
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            // Formata o resultado final
            $formattedResult = array_values($result);  // Remover chaves associativas, caso necessário

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getProduct($id)
    {
        try {
            $product = Product::getById($id);

            if (!$product) {
                throw new Exception("Não foi possível resgatar dados do produto");
            }

            
            $path = Media::getPathToFile($product);
            $extension = MediaService::getExtension($product['file_type']);
            $product['image_path'] = $path . '.' . $extension; 
                
            return ['message' => "Produto Resgatado", 'content' => $product];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
