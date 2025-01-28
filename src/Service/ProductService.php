<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Picture_products;
use App\Model\Product;
use App\Utils\Validator;
use App\Utils\ValidatorFiles;
use Exception;
use PDOException;

class ProductService
{
    public static function create(array $data, array $files)
    {
        try {

            $fields = Validator::validate([
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'price' => $data['price'] ?? '',
                'qtd_stock' => $data['qtd_stock'] ?? '',
                'sku' => $data['sku'] ?? '',
                'category' => $data['category'] ?? '',
            ]);

            if(strpos($fields['price'], ',')){
                $fields['price'] = preg_replace('/,/', '.', $fields['price']);
            }

            $files = ValidatorFiles::validate($files);

            $product = Product::create($fields);

            if (!$product) {
                throw new Exception("Não foi possível criar o produto.");
            }

            $uploads = FileProductService::upload($files, $product);
            
            $isUploadError = false;

            foreach($uploads as $path){
                $picturesImage = Picture_products::createImage([
                    'id_product' => $product,
                    'path' => $path
                ]);

                if(!$picturesImage){
                    $isUploadError = true;
                }
            }

            if($isUploadError){
                throw new Exception("Não foi possível salvar caminho da imagem");
            }

            $hasVideo = empty($data['video']) ? false : true;

            if($hasVideo){
                $picturesVideo = Picture_products::createVideo([
                    'id_product' => $product,
                    'path' => $data['video'],
                    'type' => "VIDEO"
                ]);

                if(!$picturesVideo){
                    throw new Exception("Não foi possível fazer upload do vídeo");
                }
            }

            return "Produto Cadastrado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function delete(array $data)
    {
        
        try{
            $fields = Validator::validate([
                "id_product" => $data['id_product']
            ]);

            $pdo = Product::getDatabaseConnection();
            $pdo->beginTransaction();

            $product = Product::deleteProduct($fields);

            if(!$product){
                throw new Exception("Não foi possível deletar o produto!");
            }

            $fileDeleted = FileProductService::deleteServer($fields['id_product']);

            if(!$fileDeleted){
                $pdo->rollBack();
                throw new Exception("Não foi possível deletar o produto");
            }
            $pdo->commit();
            return "Produto foi deletado com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
