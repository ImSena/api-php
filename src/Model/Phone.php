<?php

namespace App\Model;

use PDO;

class Phone extends Database
{
    public static function getAllByIdUser(int $id){
        $pdo = self::getConnection();

        $sql = "SELECT * FROM phones WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}