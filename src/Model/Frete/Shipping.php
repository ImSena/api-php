<?php

namespace App\Model\Frete;
use App\Model\Base\BaseModel;

class Shipping extends BaseModel{

    public function getTokenShipping(){

        $sql = "SELECT token_shipping FROM store WHERE is_active = 1 LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

}