<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Coupon extends BaseModel
{
    public function getCoupon(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT name, discount FROM coupon WHERE id_coupon = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(array $id){
        return true;
    }
}