<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class OrderShipping extends BaseModel
{

    public function createShipping(array $data)
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO 
        order_shipping (id_order, carrier, shipping_type, shipping_cost) 
        VALUES (:id_order, :carrier, :shipping_type, :shipping_cost)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_order", $data['id_order'], PDO::PARAM_INT);
        $stmt->bindParam(":carrier", $data['carrier'], PDO::PARAM_STR);
        $stmt->bindParam(":shipping_type", $data['shipping_type'], PDO::PARAM_STR);
        $stmt->bindParam(":shipping_cost", $data['shipping_cost'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getOrderShipping(int $idOrder)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT carrier, shipping_type, shipping_cost FROM order_shipping WHERE id_order = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $idOrder, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
