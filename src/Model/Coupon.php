<?php

namespace App\Model;

use PDO;

class Coupon
{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    private function getPdo(){
        return $this->pdo;
    }

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