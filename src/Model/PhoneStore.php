<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class PhoneStore extends BaseModel
{
    public function createPhone(array $data): bool
    {
        $sql = "INSERT INTO phones_store (type, number, is_default, is_show) VALUES (:type, :number, :is_default, :is_show)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function setIsDefault($value = false)
    {
       $sql = "UPDATE phones_store SET is_default = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function getPhones()
    {
       $sql = "SELECT id_phone_store, type, number, is_default, is_show FROM phones_store";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
