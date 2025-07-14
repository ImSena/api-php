<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class ProductCategory extends BaseModel
{
    public function getProduct(int $id)
    {
        $sql = "SELECT id_product FROM product_categories WHERE id_category = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getCategory(int $id)
    {
        $sql = "SELECT id_category FROM product_categories WHERE id_product = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCategoryVariant(int $id)
    {
        $sql = "SELECT 
                pv.id_product_variant,
                c.name AS category_name
                FROM 
                product_variants pv
                JOIN 
                products p ON pv.id_product = p.id_product
                JOIN 
                product_categories pc ON p.id_product = pc.id_product
                JOIN 
                categories c ON pc.id_category = c.id_category
                WHERE 
                pv.id_product_variant = :id_product_variant";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(":id_product_variant", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
