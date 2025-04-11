<?php

namespace App\Model;

use PDO;

class Phone
{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    private function getPdo(){
        return $this->pdo;
    }
    public function getAllByIdUser(int $id){
        $pdo = $this->getPdo();

        $sql = "SELECT * FROM phones WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}