<?php

namespace App\Service;

use App\Model\Media;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Service\Base\BaseService;
use App\Utils\Pagination;
use App\Utils\Validator;
use Exception;

class ProductService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function () use ($data) {
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
        });
    }

    /**
     * @param int $page Aqui é o offset
     */

    public function getAll($page)
    {
        return $this->execute(function () use ($page) {
            $Product = new Product($this->pdo);
            $Products = $Product->getAll($page);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);
            $Category = new CategoryService($this->pdo);

            if (!$Products) {
                throw new Exception("Não foi encontrado nenhum produto.");
            }

            $totalProducts = $Product->getTotalProducts();

            if (!$totalProducts) {
                throw new Exception("Não foi possível encontrar o total de produtos");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                $id_category = $ProductCategories->getCategory($id_product);
                $id_category = $id_category['id_category'];
                $name_category = $Category->getCategory($id_category);
                $name_category = $name_category['name'];

                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'category' => $name_category,
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
                    'price_discount' => '0.00',
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
        });
    }

    public function getRecents()
    {
        return $this->execute(function () {
            $Product = new Product($this->pdo);
            $Products = $Product->getAllRecents();
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);
            $Category = new CategoryService($this->pdo);

            if (!$Products) {
                throw new Exception("Não foi encontrado nenhum produto.");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                $id_category = $ProductCategories->getCategory($id_product);
                $id_category = $id_category['id_category'];
                $name_category = $Category->getCategory($id_category);
                $name_category = $name_category['name'];

                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'category' => $name_category,
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
                    'price_discount' => '0.00',
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            $formattedResult = array_values($result);

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult];
        });
    }

    public function getPopular()
    {
        return $this->execute(function () {
            $Product = new Product($this->pdo);
            $Products = $Product->getAllPopular();
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);
            $Category = new CategoryService($this->pdo);

            if (!$Products) {
                throw new Exception("Não foi encontrado nenhum produto.");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                $id_category = $ProductCategories->getCategory($id_product);
                $id_category = $id_category['id_category'];
                $name_category = $Category->getCategory($id_category);
                $name_category = $name_category['name'];

                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        'category' => $name_category,
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
                    'price_discount' => '0.00',
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            $formattedResult = array_values($result);

            return ['message' => "Produtos Resgatados", 'content' => $formattedResult];
        });
    }

    public function getAllCategory(array $params)
    {
        return $this->execute(function () use ($params) {
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $Products = $Product->getAllCategory($params);
            $MediaService = new MediaService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);
            $Category = new CategoryService($this->pdo);

            if (!$Products) {
                throw new Exception("Não há produtos cadastrados nessa categoria");
            }

            $result = [];
            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                $id_category = $ProductCategories->getCategory($id_product);
                $id_category = $id_category['id_category'];
                $name_category = $Category->getCategory($id_category);
                $name_category = $name_category['name'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        "categoria" => $name_category,
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
                    'price_discount' => '0.00',
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            $total = $Product->getTotalByCategory($params['id_category']);

            if (!$total) {
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
        });
    }

    public function getAllBrand(array $params)
    {
        return $this->execute(function () use ($params) {
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $Products = $Product->getAllBrand($params);
            $MediaService = new MediaService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);
            $Category = new CategoryService($this->pdo);

            if (!$Products) {
                throw new Exception("Não há produtos cadastrados nessa marca");
            }

            $result = [];

            foreach ($Products as $product) {
                $id_product = $product['id_product'];
                $id_category = $ProductCategories->getCategory($id_product);
                $id_category = $id_category['id_category'];
                $name_category = $Category->getCategory($id_category);
                $name_category = $name_category['name'];
                if (!isset($result[$id_product])) {
                    $result[$id_product] = [
                        'id_product' => $product['id_product'],
                        'brand' => $product['brand_name'],
                        'name' => $product['name'],
                        "categoria" => $name_category,
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
                    'price_discount' => '0.00',
                    'image_path' => $product['image_path'] ?? null,
                    "is_default" => $product['is_default'] ?? null
                ];
            }

            $limitPage = 40;

            $total = $Product->getTotalByBrand($params['id_brand']);

            if (!$total) {
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
        });
    }

    public function getProduct($id)
    {
        $this->execute(function () use ($id) {
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
        });
    }

    public function getAllBy(array $params)
    {
        return $this->execute(function () use ($params) {
            $params['type_by'] = strtoupper($params['type_by']);

            $types = [
                "CATEGORY",
                "BRAND"
            ];

            if (!in_array($params['type_by'], $types)) {
                throw new Exception("Não foi encontrado um tipo válido");
            }

            $paramsToBy = [];

            $product = [];

            switch ($params['type_by']) {
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
        });
    }

    public function getProductAndVariations(array $params)
    {
        return $this->execute(function () use ($params) {
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $ProductResult = $Product->getMain($params['id_product']);
            $ProductVariation = $Product->getVariations($params['id_product']);
            foreach ($ProductVariation as $prod) {
                $prod['pictures'] = $Product->getPicturesProduct($prod['id_product_variant']);
                $prod['price_discount'] = '0.00';
                foreach ($prod['pictures'] as &$picture) {
                    $path = $Media->getPathToFile($picture);
                    $extension = $MediaService->getExtension($picture['file_type']);
                    $picture['image_path'] = $path . '.' . $extension;
                }
                $prod['value_variant'] = $Product->getValueVariant($prod['id_product_variant']);
                $ProductResult['variations'][] = $prod;
            }

            if (!$ProductResult) {
                throw new Exception("Não foi possível encontrar o produto");
            }

            return $ProductResult;
        });
    }

    public function insertQuantity(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "id" => $data['id'],
                "quantity" => $data['quantity']
            ]);

            $Product = new Product($this->pdo);

            if (!$Product->insertQuantity($fields)) {
                throw new Exception("Não foi possível atualizar pedido");
            }

            return "Quantidade inserida com sucesso!";
        });
    }

    public function getProductQuote(int $id_product)
    {
        return $this->execute(function () use ($id_product) {
            $Product = new Product($this->pdo);

            $result = $Product->getProductQuote($id_product);

            if (!$result) {
                throw new Exception("Não foi possível encontrar produto");
            }

            return $result;
        });
    }

    public function editProduct(array $data)
    {
        return $this->execute(function () use ($data) {
            $Product = new Product($this->pdo);

            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? '',
            ]);

            $productData = Validator::validate([
                "name" => $data['products']['name'] ?? '',
                "description" => $data['products']['description'] ?? '',
                "id_brand" => $data['products']['id_brand'] ?? '',
                "weight" => $data['products']['weight'] ?? '',
                "length" => $data['products']['length'] ?? '',
                "width" => $data['products']['width'] ?? '',
                "height" => $data['products']['height'] ?? '',
            ]);

            $productData['variations'] = [];

            foreach ($data['products']['variations'] as $variation) {
                $validatedVariation = Validator::validate([
                    "id_product_variant" => $variation['id_product_variant'] ?? '',
                    "sku" => $variation['sku'] ?? '',
                    "price" => $variation['price'] ?? '',
                    "qtd_stock" => $variation['qtd_stock'] ?? '',
                    "is_default" => $variation['is_default'] ?? '',
                    "discount" => $variation['discount'] ?? '',
                ]);

                $validatedVariation['pictures'] = [];

                foreach ($variation['pictures'] ?? [] as $picture) {
                    $validatedPicture = Validator::validate([
                        "id_media" => $picture['id_media'] ?? '',
                        "position" => $picture['position'] ?? '',
                        "is_main" => $picture['is_main'] ?? '',
                    ]);

                    $validatedVariation['pictures'][] = $validatedPicture;
                }

                $productData['variations'][] = $validatedVariation;
            }

            $fields['products'] = $productData;
            $fields['id_product'] = $data['id_product'];

            $product = $Product->editProduct($fields);

            if (!$product) {
                throw new Exception("Não foi possível editar o produto.");
            }

            $productVariation = $Product->editVariations($fields);

            if (!$productVariation) {
                throw new Exception("Não foi possível editar variação");
            }

            $categoryProduct = $Product->editCategoryProduct($fields);

            if (!$categoryProduct) {
                throw new Exception("Não foi possível atualizar categoria do produto");
            }

            $picturesProduct = $Product->editPicturesProduct($fields);

            if (!$picturesProduct) {
                throw new Exception("Não foi possível atualizar imagens");
            }

            return "Produto editado com sucesso.";
        }, true);
    }

    public function searchProduct(string $search)
    {
        return $this->execute(function () use ($search) {
            $Product = new Product($this->pdo);

            $search = $search ? $search : 'moeda';

            $productResult =  $Product->searchByName($search);

            return $productResult;
        });
    }

    public function delete(int $id_product){
        return $this->execute(function() use ($id_product){
            $Product = new Product($this->pdo);

            $resultDel = $Product->deleteProduct($id_product);

            if(!$resultDel){
                throw new Exception("Não foi possível deletar o produto");
            }

            return $resultDel;
        });
        
    }
}
