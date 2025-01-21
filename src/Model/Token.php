<?php

namespace App\Model;

use App\Model\Database;
use Exception;
use Pdo;

class Token extends Database
{
    public static function create(string $token)
    {

        $pdo = self::getConnection();

        $sql = "INSERT INTO TOKEN (id_token) VALUES (:token)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function select(string $token)
    {
        $pdo = self::getConnection();

        $sql = "SELECT status FROM TOKEN WHERE id_token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function inactive(string $token)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE TOKEN SET status = 'INACTIVE' WHERE id_token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
