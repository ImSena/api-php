<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Brand extends BaseModel
{
    
    public function create(array $data):bool
    {
        $sql = "INSERT INTO brands (name) VALUES (:name)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAll():array
    {        $sql = "SELECT id_brand, name FROM brands";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function update(array $data):bool
    {        $sql = "UPDATE brands SET name = :name, updated_at = :updated_at WHERE id_brand = :id_brand";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_brand", $data['id'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function delete(int $int):bool
    {        $sql = "DELETE FROM brands WHERE id_brand = :id_brand";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id_brand", $int, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}