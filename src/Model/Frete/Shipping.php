<?php

namespace App\Model\Frete;
use App\Model\Base\BaseModel;

class Shipping extends BaseModel{

    public function getTokenShipping(){
        $pdo = $this->getPdo();

        $sql = "SELECT token_shipping FROM store WHERE is_active = 1 LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

}