<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class ProductCategory extends BaseModel
{
    public function getProduct(int $id) {
        $pdo = $this->getPdo();

        $sql = "SELECT id_product FROM product_categories WHERE id_category = :id LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getCategory(int $id){
        $pdo = $this->getPdo();

        $sql = "SELECT id_category FROM product_categories WHERE id_product = :id LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
