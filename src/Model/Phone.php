<?php

namespace App\Model;

use PDO;

class Phone extends Database
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