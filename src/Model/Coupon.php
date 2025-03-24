<?php

namespace App\Model;

use PDO;

class Coupon extends Database
{
    public static function getCoupon(int $id)
    {
        $pdo = self::getConnection();

        $sql = "SELECT name, discount FROM coupon WHERE id_coupon = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function create(array $id){
        
    }
}