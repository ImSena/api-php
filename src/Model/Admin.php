<?php

namespace App\Model;

use App\Model\Database;
use Pdo;

class Admin extends Database
{

    public static function create(array $data)
    {

        $pdo = self::getConnection();
        $sql = "INSERT INTO ADMIN (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function login(array $data){
        $pdo = self::getConnection();
        $sql = "SELECT * FROM ADMIN WHERE email = :email";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }
}
