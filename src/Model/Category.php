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

        $sql = "SELECT id_category, name, description FROM CATEGORIES WHERE parent_category_id IS NULL";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getAllCategories()
    {
        $pdo = self::getConnection();

        $sql = "SELECT id_category, name, parent_category_id, description FROM CATEGORIES WHERE parent_category_id IS NOT NULL";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public static function update(array $data){
        $pdo = self::getConnection();

        $sql = "UPDATE CATEGORIES SET parent_category_id = :parent_category_id, name = :name, description = :description WHERE id_category = :id";

        $stmt = $pdo->prepare($sql);

        $parentCategory = (!empty($data['parent_category']) && is_numeric($data['parent_category'])) ? (int) $data['parent_category'] : null;

        $stmt->bindParam(":id", $data['id_category'], PDO::PARAM_INT);
        $stmt->bindValue(":parent_category_id", $parentCategory, is_null($parentCategory) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":description", $data['description'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
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