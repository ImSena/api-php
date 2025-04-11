<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Model\Product;
use App\Utils\Pagination;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class ProductService
{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function create(array $data)
    {
        try {
            $Product = new Product($this->pdo);
            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? '',
                "products" => $data['products'] ?? '',
            ]);

            $Product = $Product->create($fields);

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

    /**
     * @param int $page Aqui é o offset
     */

    public function getAll($page)
    {
        try {
            $Product = new Product($this->pdo);
            $Products = $Product->getAll($page);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);

            if (!$Products) {
                throw new Exception("Não foi encontrado nenhum produto.");
            }

            $totalProducts = $Product->getTotalProducts();

            if(!$totalProducts){
                throw new Exception("Não foi possível encontrar o total de produtos");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'variations' => []
                    ];
                }

                $path = $Media->getPathToFile($product);
                $extension = $MediaService->getExtension($product['file_type']);
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

            $limitPage = 40;

            $qtdPage = Pagination::calculateTotalPages($totalProducts['total'],  $limitPage);

            $pages = [
                "qtdPage" => $qtdPage,
                "total" => $totalProducts['total']
            ];

            $formattedResult = array_values($result);

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult, 'page' => $pages];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function getAllCategory(array $params)
    {
        try {
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $Products = $Product->getAllCategory($params);
            $MediaService = new MediaService($this->pdo);

            
            // var_dump($Products);exit;

            if (!$Products) {
                throw new Exception("Não há produtos cadastrados nessa categoria");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'variations' => []
                    ];
                }

                $path = $Media->getPathToFile($product);
                $extension = $MediaService->getExtension($product['file_type']);
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

            $total = $Product->getTotalByCategory($params['id_category']);

            if(!$total){
                throw new Exception("Não foi possível pegar o total de produtos.");
            }

            $limitPage = 40;

            $qtdPage = Pagination::calculateTotalPages($total['total'],  $limitPage);

            $pages = [
                "qtdPage" => $qtdPage,
                "total" => $total['total']
            ];

            $formattedResult = array_values($result);

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult, 'page' => $pages];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllBrand(array $params)
    {
        try{
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $Products = $Product->getAllBrand($params);
            $MediaService = new MediaService($this->pdo);

            if(!$Products){
                throw new Exception("Não há produtos cadastrados nessa marca");
            }

            $result = [];

            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'variations' => []
                    ];
                }

                $path = $Media->getPathToFile($product);
                $extension = $MediaService->getExtension($product['file_type']);
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

            $limitPage = 40;

            $total = $Product->getTotalByBrand($params['id_brand']);

            if(!$total){
                throw new Exception("Não foi possível resgatar total de produtos por marca");
            }

            $qtdPages = Pagination::calculateTotalPages($total['total'], $limitPage);

            $pages = [
                "qtdPage" => $qtdPages,
                "total" => $total['total']
            ];

            $formattedResult = array_values($result);

            return [
                "message" => "Produtos resgatados com sucesso.",
                'content' => $formattedResult,
                "page" => $pages
            ];
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public function getProduct($id)
    {
        try {
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $product = $Product->getById($id);

            if (!$product) {
                throw new Exception("Não foi possível resgatar dados do produto");
            }

            $path = $Media->getPathToFile($product);
            $extension = $MediaService->getExtension($product['file_type']);
            $product['image_path'] = $path . '.' . $extension; 
                
            return ['message' => "Produto Resgatado", 'content' => $product];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllBy(array $params)
    {
        try{
            $params['type_by'] = strtoupper($params['type_by']);

            $types = [
                "CATEGORY",
                "BRAND"
            ];

            if(!in_array($params['type_by'], $types)){
                throw new Exception("Não foi encontrado um tipo válido");
            }

            $paramsToBy = [];

            $product = [];

            switch($params['type_by']){
                case 'CATEGORY':
                    $paramsToBy['id_category'] = $params['id_by'];
                    $paramsToBy['page'] = $params['page'];
                    $product = $this->getAllCategory($paramsToBy);
                break;
                case "BRAND":
                    $paramsToBy['id_brand'] = $params['id_by'];
                    $paramsToBy['page'] = $params['page'];

                    $product = $this->getAllBrand($paramsToBy);
                break;
            }

            return $product;

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getProductAndVariations(array $params)
    {
        try{
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $ProductResult = $Product->getMain($params['id_product']);
            $ProductVariation = $Product->getVariations($params['id_product']);
            foreach ($ProductVariation as $prod) {
                $prod['pictures'] = $Product->getPicturesProduct($prod['id_product_variant']);
                foreach($prod['pictures'] as &$picture){
                    $path = $Media->getPathToFile($picture);
                    $extension = $MediaService->getExtension($picture['file_type']);
                    $picture['image_path'] = $path.'.'. $extension;
                    unset($picture['id_media']);
                    unset($picture['file_type']);
                }
                $prod['value_variant'] = $Product->getValueVariant($prod['id_product_variant']);
                $ProductResult['variations'][] = $prod;
            }

            if(!$ProductResult){
                throw new Exception("Não foi possível encontrar o produto");
            }

            return $ProductResult;

        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}
