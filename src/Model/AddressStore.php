<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class AddressStore extends BaseModel
{
    public function createAddress(array $data):bool
    {
        $pdo = $this->getPdo();
        $sql = "INSERT INTO address_store 
        (public_area, number, complement, district, city, state, zip_code, is_default, is_active, is_show)
        VALUES (:public_area, :number, :complement, :district, :city, :state, :zip_code, :is_default, :is_active, :is_show)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}