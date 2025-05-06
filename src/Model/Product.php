<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use Exception;
use PDO;
use PDOException;

class Product extends BaseModel
{
    public function create(array $data)
    {
        $pdo = $this->getPdo();

        $pdo->beginTransaction();

        try {

            $products = $data['products'];
            $sql = "INSERT INTO products (name, description, id_brand, weight, length, width, height) VALUES (:name, :description, :id_brand, :weight, :length, :width, :height)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":name", $products['name'], PDO::PARAM_STR);
            $stmt->bindParam(":description", $products['description'], PDO::PARAM_STR);
            $stmt->bindParam(":id_brand", $products['id_brand'], PDO::PARAM_INT);
            $stmt->bindParam(":weight", $products['weight'], PDO::PARAM_STR);
            $stmt->bindParam(":length", $products['length'], PDO::PARAM_STR);
            $stmt->bindParam(":width", $products['width'], PDO::PARAM_STR);
            $stmt->bindParam(":height", $products['height'], PDO::PARAM_STR);

            $stmt->execute();

            $productId = $pdo->lastInsertId();

            if (empty($productId)) {
                throw new Exception("Erro ao criar produto.");
            }

            $productVariant = $this->createVariant($products['variations'], $productId, $pdo);

            if (!$productVariant) {
                throw new Exception("Erro ao criar variação");
            }

            foreach ($products['variations'] as $index => $variant) {
                $productVariantId = $productVariant[$index];
                $productPictures = $this->createProductPictures($variant['pictures'], $productVariantId, $variant['value_variant'], $pdo);

                if (!$productPictures) {
                    throw new Exception("Não foi possível cadastrar imagem do produto");
                }
            }

            $categoryProduct = $this->relationCategory($pdo, $productId, $data['id_category']);

            if (!$categoryProduct) {
                throw new Exception("Não foi possível relacionar categoria");
            }

            $pdo->commit();

            return $productId;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    private function createVariant(array $variants, int $productId, PDO $pdo)
    {
        $sql = "INSERT INTO product_variants (id_product, sku, price, qtd_stock, is_default, discount) 
            VALUES (:id_product, :sku, :price, :stock, :is_default, :discount)";

        $stmt = $pdo->prepare($sql);
        $productVariants = [];

        foreach ($variants as $variant) {
            $stmt->execute([
                ':id_product' => $productId,
                ':sku' => $variant['sku'],
                ':price' => $variant['price'],
                ':stock' => $variant['stock'],
                ':is_default' => $variant['is_default'],
                ':discount' => $variant['discount']
            ]);
            $productVariantId = (int) $pdo->lastInsertId();
            $productVariants[] = $productVariantId;

            if (!empty($variant['value_variant'])) {
                $this->createRelationVariant($productVariantId, $variant['value_variant'], $pdo);
            }
        }

        return $productVariants;
    }
    private function createRelationVariant(int $productVariantId, int $value_variant, PDO $pdo)
    {
        $sql = "INSERT INTO product_variants_attributes (id_variant_attribute_value, id_product_variant) VALUES
        (:id_variant_attribute_value, :id_product_variant)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product_variant", $productVariantId, PDO::PARAM_INT);
        $stmt->bindParam(":id_variant_attribute_value", $value_variant, PDO::PARAM_INT);

        $stmt->execute();
    }
    private function createProductPictures(array $pictures, $id_product_variant, $id_variant_attribute_value, PDO $pdo)
    {
        try {
            $sql = "INSERT INTO product_pictures (id_product_variant, id_variant_attribute_value, id_media, position, is_main) 
            VALUES (:id_product_variant, :id_variant_attribute_value, :id_media, :position, :is_main)";

            $stmt = $pdo->prepare($sql);

            foreach ($pictures as $picture) {
                $stmt->execute([
                    ':id_product_variant' => $id_product_variant,
                    ':id_variant_attribute_value' => $id_variant_attribute_value,
                    ':id_media' => $picture['id_media'],
                    ':position' => $picture['position'],
                    ':is_main' => $picture['is_main']
                ]);
            }
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    private function relationCategory($pdo, $productId, $categoryId)
    {

        $sql = "INSERT INTO product_categories (id_product, id_category) VALUES (:id_product, :id_category)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $productId, PDO::PARAM_INT);
        $stmt->bindParam(":id_category", $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }
    /**
     * @param int $page Indique o offset da pagina
     */
    public function getAll(int $page)
    {
        $pdo = $this->getPdo();

        $limit = 40;
        $page = isset($page) ? (int) $page : 1;
        $offset = ($page - 1) * $limit;


        $sql = "SELECT 
                    p.id_product,
                    p.id_brand,
                    p.name,
                    pv.id_product_variant,
                    pv.sku,
                    pv.price,
                    pv.qtd_stock,
                    pv.discount,
                    pv.is_default,
                    m.id_media,
                    m.file_type,
                    b.name AS brand_name
                FROM products AS p
                LEFT JOIN product_variants AS pv 
                    ON p.id_product = pv.id_product
                LEFT JOIN product_pictures AS pp 
                    ON pv.id_product_variant = pp.id_product_variant 
                    AND pp.is_main = 1
                LEFT JOIN media AS m 
                    ON pp.id_media = m.id_media
                LEFT JOIN brands AS b
                    ON p.id_brand = b.id_brand
                WHERE p.status > 0
                ORDER BY p.id_product, pv.id_product_variant
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);


        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function getTotalProducts()
    {
        $pdo = $this->getPdo();

        $sql = "SELECT COUNT(id_product) AS total FROM products";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getById(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                    pv.id_product_variant,
                    p.name,
                    pv.sku,
                    pv.price,
                    pv.qtd_stock,
                    pv.discount,
                    m.id_media,
                    m.file_type,
                    b.name AS brand_name
                FROM products AS p
                LEFT JOIN product_variants AS pv 
                    ON p.id_product = pv.id_product
                LEFT JOIN product_pictures AS pp 
                    ON pv.id_product_variant = pp.id_product_variant 
                    AND pp.is_main = 1
                LEFT JOIN media AS m 
                    ON pp.id_media = m.id_media
                LEFT JOIN brands AS b
                    ON p.id_brand = b.id_brand
                WHERE p.status > 0 AND pv.id_product_variant = :id
                ORDER BY p.id_product, pv.id_product_variant
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }
    public function getAllCategory($params)
    {
        $pdo = $this->getPdo();

        $limit = 40;
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT 
                p.id_product,
                p.id_brand,
                p.name,
                pv.id_product_variant,
                pv.sku,
                pv.price,
                pv.qtd_stock,
                pv.discount,
                pv.is_default,
                m.id_media,
                m.file_type,
                b.name AS brand_name
            FROM products AS p
            LEFT JOIN product_variants AS pv 
                ON p.id_product = pv.id_product
            LEFT JOIN product_pictures AS pp 
                ON pv.id_product_variant = pp.id_product_variant 
                AND pp.is_main = 1
            LEFT JOIN media AS m 
                ON pp.id_media = m.id_media
            LEFT JOIN brands AS b
                ON p.id_brand = b.id_brand
            LEFT JOIN product_categories AS pc
                ON p.id_product = pc.id_product
            WHERE p.status > 0 
                AND pc.id_category = :id_category 
            ORDER BY p.id_product, pv.id_product_variant
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':id_category', $params['id_category'], PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * @param int $id Esse id é da categoria especificada.
     */
    public function getTotalByCategory(int $id)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT COUNT(id_product) AS total FROM product_categories WHERE id_category = :id_category";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_category", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
    public function getAllBrand(array $params)
    {
        $pdo = $this->getPdo();

        $limit = 40;
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT 
                p.id_product,
                p.name,
                pv.id_product_variant,
                pv.sku,
                pv.price,
                pv.qtd_stock,
                pv.discount,
                pv.is_default,
                m.id_media,
                m.file_type,
                b.name AS brand_name
            FROM products AS p
            LEFT JOIN product_variants AS pv 
                ON p.id_product = pv.id_product
            LEFT JOIN product_pictures AS pp 
                ON pv.id_product_variant = pp.id_product_variant 
                AND pp.is_main = 1
            LEFT JOIN media AS m 
                ON pp.id_media = m.id_media
            LEFT JOIN brands AS b
                ON p.id_brand = b.id_brand
            WHERE p.status > 0 
                AND p.id_brand = :id_brand 
            ORDER BY p.id_product, pv.id_product_variant
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_brand", $params['id_brand'], PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function getTotalByBrand(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT COUNT(id_product) AS total FROM products WHERE id_brand = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function deleteProduct(array $data): bool
    {
        $pdo = $this->getPdo();

        $sql = "UPDATE products SET status = 0, updated_at = :updated WHERE id_product = :id_product";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $data['id_product'], PDO::PARAM_INT);
        $stmt->bindParam(":updated", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function getDatabaseConnection()
    {
        return $this->getPdo();
    }

    public function getVariations(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT id_product_variant, sku, price, qtd_stock, is_default, discount FROM product_variants WHERE id_product = :id_product";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_product", $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMain(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                    b.name AS brand, 
                    p.name AS name, 
                    p.description 
                FROM products AS p 
                LEFT JOIN brands AS b 
                ON p.id_brand = b.id_brand 
                WHERE p.id_product = :id
                ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param int $id Esse id é o id do produto
     */
    public function getPicturesProduct(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                    pp.id_media,
                    pp.is_main,
                    pp.position,
                    m.file_type
                FROM product_pictures AS pp 
                LEFT JOIN media AS m ON m.id_media = pp.id_media
                WHERE id_product_variant = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * @param int $id Esse id é o id do produto
     */
    public function getValueVariant(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                    pva.id_variant_attribute_value,
                    vav.value,
                    vav.viewer,
                    va.name
                FROM 
                    product_variants_attributes AS pva 
                LEFT JOIN variant_attributes_values AS vav 
                    ON pva.id_variant_attribute_value = vav.id_variant_attribute_value
                LEFT JOIN variant_attributes AS va
                    ON vav.id_variant_attribute = va.id_variant_attribute
                WHERE pva.id_product_variant = :id
                ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function insertQuantity(array $data)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE product_variants SET qtd_stock = qtd_stock + :quantity, updated_at = :updated_at WHERE id_product_variant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":quantity", $data['quantity'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getProductQuote(int $id_product)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                pv.id_product_variant, 
                p.weight, 
                p.width, 
                p.length, 
                p.height, 
                (pv.price - pv.discount) AS price 
                FROM product_variants AS pv 
                INNER JOIN products AS p ON pv.id_product = p.id_product
                WHERE pv.id_product_variant = :id LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id_product, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function editProduct(array $data)
    {
        $pdo = $this->getPdo();
        $product = $data['products'];

        $sql = "UPDATE products 
        SET id_brand = :id_brand, 
        name = :name, 
        description = :description, 
        weight = :weight, 
        width = :width, 
        height = :height,
        updated_at = :updated_at
        WHERE id_product = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_brand", $product['id_brand'], PDO::PARAM_INT);
        $stmt->bindParam(":name", $product['name'], PDO::PARAM_STR);
        $stmt->bindParam(":description", $product['description'], PDO::PARAM_STR);
        $stmt->bindParam(":weight", $product['weight'], PDO::PARAM_STR);
        $stmt->bindParam(":width", $product['width'], PDO::PARAM_STR);
        $stmt->bindParam(":height", $product['height'], PDO::PARAM_STR);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id_product'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function editVariations(array $data)
    {
        $pdo = $this->getPdo();

        $product = $data['products']['variations'];

        $sql = "UPDATE product_variants SET 
                sku = :sku,
                price = :price,
                qtd_stock = :qtd,
                is_default = :is_default,
                discount = :discount,
                updated_at = :updated_at
                WHERE id_product_variant = :id
                ";
        $stmt = $pdo->prepare($sql);

        $checks = [];
        foreach ($product as $variation) {
            $stmt->bindParam(":sku", $variation['sku'], PDO::PARAM_STR);
            $stmt->bindParam(":price", $variation['price'], PDO::PARAM_STR);
            $stmt->bindParam(":qtd", $variation['qtd_stock'], PDO::PARAM_INT);
            $stmt->bindParam(":is_default", $variation['is_default'], PDO::PARAM_BOOL);
            $stmt->bindParam(":discount", $variation['discount'], PDO::PARAM_STR);
            $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
            $stmt->bindParam(":id", $variation['id_product_variant'], PDO::PARAM_INT);

            $stmt->execute();

            $checks[] = $stmt->rowCount() > 0;
        }

        return !in_array(false, $checks, true);
    }

    public function editCategoryProduct(array $data)
    {
        $pdo = $this->getPdo();

        $category = $data['id_category'];
        $id_product = $data['id_product'];

        $sql = "UPDATE product_categories 
        SET id_category = :id_category,
        updated_at = :updated_at
        WHERE id_product = :id_product";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_category", $category, PDO::PARAM_INT);
        $stmt->bindParam(":id_product", $id_product, PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function editPicturesProduct(array $data)
    {
        $pdo = $this->getPdo();

        $variations = $data['products']['variations'];

        foreach ($variations as $variation) {
            $id_product_variant = $variation['id_product_variant'];
            $pictures = $variation['pictures'] ?? [];

            $stmt = $pdo->prepare("SELECT id_media FROM product_pictures WHERE id_product_variant = :id");
            $stmt->bindParam(":id", $id_product_variant, PDO::PARAM_INT);
            $stmt->execute();
            $existingMedia = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $incomingMedia = array_column($pictures, 'id_media');

            $toDelete = array_diff($existingMedia, $incomingMedia);

            if (!empty($toDelete)) {
                $in = implode(',', array_fill(0, count($toDelete), '?'));
                $sqlDelete = "DELETE FROM product_pictures WHERE id_product_variant = ? AND id_media IN ($in)";
                $stmtDelete = $pdo->prepare($sqlDelete);
                $stmtDelete->execute(array_merge([$id_product_variant], $toDelete));
            }

            $toInsert = array_diff($incomingMedia, $existingMedia);
            foreach ($pictures as $picture) {
                if (in_array($picture['id_media'], $toInsert)) {
                    $stmtInsert = $pdo->prepare("
                    INSERT INTO product_pictures (id_product_variant, id_media, position, is_main, created_at)
                    VALUES (:id_product_variant, :id_media, :position, :is_main, :created_at)
                ");
                    $stmtInsert->execute([
                        ':id_product_variant' => $id_product_variant,
                        ':id_media' => $picture['id_media'],
                        ':position' => $picture['position'] ?? 0,
                        ':is_main' => $picture['is_main'] ? 1 : 0,
                        ':created_at' => $this->currentDatetime
                    ]);
                } else {
                    $stmtUpdate = $pdo->prepare("
                    UPDATE product_pictures
                    SET position = :position, is_main = :is_main, updated_at = :updated_at
                    WHERE id_product_variant = :id_product_variant AND id_media = :id_media
                ");
                    $stmtUpdate->execute([
                        ':position' => $picture['position'] ?? 0,
                        ':is_main' => $picture['is_main'] ? 1 : 0,
                        ':updated_at' => $this->currentDatetime,
                        ':id_product_variant' => $id_product_variant,
                        ':id_media' => $picture['id_media']
                    ]);
                }
            }
        }

        return true;
    }
}
