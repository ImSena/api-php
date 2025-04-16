<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Brand extends BaseModel
{
    
    public function create(array $data):bool
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO brands (name) VALUES (:name)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAll():array
    {
        $pdo = $this->getPdo();
        $sql = "SELECT id_brand, name FROM brands";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function update(array $data):bool
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE brands SET name = :name WHERE id_brand = :id_brand";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_brand", $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function delete(int $int):bool
    {
        $pdo = $this->getPdo();
        $sql = "DELETE FROM brands WHERE id_brand = :id_brand";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_brand", $int, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}