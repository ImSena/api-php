<?php

namespace App\Model;
use App\Model\Database;
use PDO;

class Category extends Database
{
    public static function create(array $data)
    {
        $pdo = self::getConnection();

        $hasParentCategory = isset($data['parent_category']);

        $sql = $hasParentCategory
        ? "INSERT INTO CATEGORIES (name, description, parent_category_id) VALUES (:name, :description, :parent_category)"
        : "INSERT INTO CATEGORIES (name, description) VALUES (:name, :description)";
        
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":description", $data['description'], PDO::PARAM_STR);

        if($hasParentCategory){
            $stmt->bindParam(":parent_category", $data['parent_category'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }

    public static function getAllParent()
    {
        $pdo = self::getConnection();

        $sql = "SELECT id_category, name FROM CATEGORIES WHERE parent_category_id IS NULL";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function delete(array $data)
    {
        $pdo = self::getConnection();
        $sql = "DELETE FROM CATEGORIES WHERE id_category = :id_category";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_category", $data['id_category'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}