<?php

namespace App\Model;

use App\Model\Database;

use PDO;

class Picture_products extends Database
{
    public static function createImage(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO PRODUCT_PICTURES (id_product, path) VALUES (:id_product, :path)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $data['id_product'], PDO::PARAM_INT);
        $stmt->bindParam(":path", $data['path'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function createVideo(array $data):bool
    {
        $pdo = self::getConnection();
        $sql = "INSERT INTO PRODUCT_PICTURES (id_product, path, type) VALUES (:id_product, :path, :type)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_product", $data['id_product'], PDO::PARAM_INT);
        $stmt->bindParam(":path", $data['path'], PDO::PARAM_STR);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}