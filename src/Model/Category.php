<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use App\Model\Database;
use PDO;

class Category extends BaseModel
{
    public function create(array $data)
    {
        $hasParentCategory = isset($data['parent_category']);

        $sql = $hasParentCategory
            ? "INSERT INTO categories (name, parent_category_id) VALUES (:name, :parent_category)"
            : "INSERT INTO categories (name) VALUES (:name)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);

        if ($hasParentCategory) {
            $stmt->bindParam(":parent_category", $data['parent_category'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return !empty($this->pdo->lastInsertId());
    }

    public function getAllParent()
    {
        $sql = "SELECT id_category, name FROM categories WHERE parent_category_id IS NULL";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllCategories()
    {
        $sql = "SELECT id_category, name, parent_category_id FROM categories WHERE parent_category_id IS NOT NULL";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllCategoriesHasProducts($verifyItem = false, $isParent = false)
    {
        $sql = "SELECT DISTINCT c.id_category, c.name, c.parent_category_id
                FROM categories c
                LEFT JOIN product_categories pc ON pc.id_category = c.id_category
                LEFT JOIN products p ON p.id_product = pc.id_product
                LEFT JOIN product_variants pv ON pv.id_product = p.id_product
                WHERE ";
        
        $sql .= $isParent ? "parent_category_id IS NULL" : "parent_category_id IS NOT NULL";
        
        if(!$verifyItem){
            $sql .= " AND pv.qtd_stock > 0";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function update(array $data)
    {
        $sql = "UPDATE categories SET parent_category_id = :parent_category_id, name = :name, updated_at = :updated_at WHERE id_category = :id";

        $stmt = $this->pdo->prepare($sql);

        $parentCategory = (!empty($data['parent_category']) && is_numeric($data['parent_category'])) ? (int) $data['parent_category'] : null;

        $stmt->bindParam(":id", $data['id_category'], PDO::PARAM_INT);
        $stmt->bindValue(":parent_category_id", $parentCategory, is_null($parentCategory) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":description", $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(array $data)
    {
        $sql = "DELETE FROM categories WHERE id_category = :id_category";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id_category", $data['id_category'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getCategory(int $id)
    {
        $sql = "SELECT id_category, name FROM categories WHERE id_category = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
