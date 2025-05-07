<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class PhoneStore extends BaseModel
{
    public function createPhone(array $data): bool
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO phones_store (type, number, is_default, is_show) VALUES (:type, :number, :is_default, :is_show)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function setIsDefault($value = false)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE phones_store SET is_default = :value";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_BOOL);

        return $stmt->execute();
    }
}
