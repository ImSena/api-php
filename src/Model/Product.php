<?php

namespace App\Model;

use PDO;

class Product extends Database
{
    public static function create(array $data)
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO PRODUCT (name, value, qtd) VALUES (:name, :value, :qtd)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":value", $data['value'], PDO::PARAM_STR);
        $stmt->bindParam(":qtd", $data['qtd'], PDO::PARAM_INT);

        $stmt->execute();

        return $pdo->lastInsertId() > 0 ? true : false;
    } 
}