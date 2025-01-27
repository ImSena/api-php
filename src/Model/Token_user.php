<?php

namespace App\Model;

use App\Model\Database;
use Exception;
use Pdo;

class Token_user extends Database
{
    public static function create(array $data)
    {

        $pdo = self::getConnection();

        $sql = "INSERT INTO TOKENS_USERS (id_user, type, token) VALUES (:id_user, :type, :token)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_STR);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":token", $data['token'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function select(string $token)
    {
        $pdo = self::getConnection();

        $sql = "SELECT status FROM TOKENS_USERS WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function inactiveAll(string $id_user)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE TOKENS_USERS SET status = 'INACTIVE' WHERE id_user = :id_user";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function inactiveToken(string $token)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE TOKENS_USERS SET status = 'INACTIVE' WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
