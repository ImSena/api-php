<?php

namespace App\Model;

use Exception;
use PDO;

class Product extends Database
{
    public static function create(array $data)
    {
        $pdo = self::getConnection();

        $pdo->beginTransaction();

        try {
            $sql = "INSERT INTO PRODUCTS (name, description, price, qtd_stock, sku) VALUES (:name, :description, :price, :qtd_stock, :sku)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(":description", $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(":price", $data['price'], PDO::PARAM_STR);
            $stmt->bindParam(":qtd_stock", $data['qtd_stock'], PDO::PARAM_INT);
            $stmt->bindParam(":sku", $data['sku'], PDO::PARAM_STR);

            $stmt->execute();

            $productId = $pdo->lastInsertId();

            if (empty($productId)) {
                throw new Exception("Erro ao criar produto.");
            }

            $categorySuccess = self::relationCategory($pdo, $productId, $data['category']);

            if (!$categorySuccess) {
                throw new Exception("Erro ao associar produto à categoria");
            }

            $pdo->commit();

            return $productId;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
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

        $sql = "DELETE FROM PRODUCTS WHERE id_product = :id_product";

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
