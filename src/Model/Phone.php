<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Phone extends BaseModel
{
    public function getAllByIdUser(int $id){
        $pdo = $this->getPdo();

        $sql = "SELECT * FROM phones WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}