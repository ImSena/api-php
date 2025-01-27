<?php

namespace App\Model;

use App\Model\Database;
use Pdo;

class Admin extends Database
{

    public static function create(array $data)
    {

        $pdo = self::getConnection();
        $sql = "INSERT INTO ADMINS (name, email, password, permission) VALUES (:name, :email, :password, :permission)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":permission", $data['permission'], PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public static function select(array $data){
        $pdo = self::getConnection();
        $sql = "SELECT * FROM ADMINS WHERE email = :email";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function updateAccess($data, $id)
    {
        $pdo = self::getConnection();
        $sql = "UPDATE ADMINS SET password = :password WHERE id_admin = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function activeAdmin($status, $id)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE ADMINS SET status = :status WHERE id_admin = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
