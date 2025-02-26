<?php

namespace App\Model;

use Exception;
use PDO;
use PDOException;

class Product extends Database
{
    public static function create(array $data)
    {   
        $pdo = self::getConnection();

        $pdo->beginTransaction();

        try {

            $products = $data['products'];
            $sql = "INSERT INTO PRODUCTS (name, description, id_branch) VALUES (:name, :description, :id_branch)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":name", $products['name'], PDO::PARAM_STR);
            $stmt->bindParam(":description", $products['description'], PDO::PARAM_STR);
            $stmt->bindParam(":id_branch", $products['id_branch'], PDO::PARAM_INT);

            $stmt->execute();

            $productId = $pdo->lastInsertId();

            if (empty($productId)) {
                throw new Exception("Erro ao criar produto.");
            }

            $productVariant = self::createVariant($products['variations'], $productId, $pdo);

            if (!$productVariant) {
                throw new Exception("Erro ao criar variação");
            }

            foreach ($products['variant'] as $index => $variant) {
                $productVariantId = $productVariant[$index];
                $productPictures = self::createProductPictures($variant['pictures'], $productVariantId, $variant['value_variant'], $pdo);

                if (!$productPictures) {
                    throw new Exception("Não foi possível cadastrar imagem do produto");
                }
            }

            $categoryProduct = self::relationCategory($pdo, $productId, $data['id_category']);

            if(!$categoryProduct){
                throw new Exception("Não foi possível relacionar categoria");
            }

            $pdo->commit();

            return $productId;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
         catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public static function createVariant(array $variants, int $productId, PDO $pdo)
    {
        $sql = "INSERT INTO PRODUCT_VARIANTS (id_product, sku, price, qtd_stock, is_default, discount) 
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
            $productVariantId = $pdo->lastInsertId();
            $productVariants[] = $productVariantId;
            self::createRelationVariant($productVariantId, $variant['value_variant'], $pdo);
        }

        return $productVariants;
    }

    public static function createRelationVariant(int $productVariantId, int $value_variant, PDO $pdo)
    {
        $sql = "INSERT INTO PRODUCT_VARIANTS_ATTRIBUTES (id_variant_attribute_value, id_product_variant) VALUES
        (:id_variant_attribute_value, :id_product_variant)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_variant_attribute_value", $productVariantId, PDO::PARAM_INT);
        $stmt->bindParam(":id_product_variant", $value_variant, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function createProductPictures(array $pictures, $id_product_variant, $id_variant_attribute_value, PDO $pdo)
    {
        try {
            $sql = "INSERT INTO PRODUCT_PICTURES (id_product_variant, id_variant_attribute_value, id_media, position, is_main) 
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

    private static function relationCategory($pdo, $productId, $categoryId)
    {

        $sql = "INSERT INTO PRODUCT_CATEGORIES (id_product, id_category) VALUES (:id_product, :id_category)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $productId, PDO::PARAM_INT);
        $stmt->bindParam(":id_category", $categoryId, PDO::PARAM_INT);

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }

    public static function deleteProduct(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE PRODUCTS SET status = 0 WHERE id_product = :id_product";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $data['id_product'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function getDatabaseConnection()
    {
        return self::getConnection();
    }
}
