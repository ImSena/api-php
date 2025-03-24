<?php

namespace App\Model;

use PDO;

class Brand extends Database
{
    public static function create(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO brands (name) VALUES (:name)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function getAll():array
    {
        $pdo = self::getConnection();
        $sql = "SELECT id_brand, name FROM brands";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function update(array $data):bool
    {
        $pdo = self::getConnection();
        $sql = "UPDATE brands SET name = :name WHERE id_brand = :id_brand";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_brand", $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function delete(int $int):bool
    {
        $pdo = self::getConnection();
        $sql = "DELETE FROM brands WHERE id_brand = :id_brand";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_brand", $int, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}