<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Phone extends BaseModel
{
    public function getAllByIdUser(int $id)
    {
        $sql = "SELECT * FROM phones WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}