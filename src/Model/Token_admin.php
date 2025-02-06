<?php

namespace App\Model;

use App\Model\Database;
use Exception;
use Pdo;

class Token_admin extends Database
{
    public static function create(array $data)
    {

        $pdo = self::getConnection();

        $sql = "INSERT INTO TOKENS_ADMINS (id_admin, type, token) VALUES (:id_admin, :type, :token)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $data['id_admin'], PDO::PARAM_STR);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":token", $data['token'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function select(string $token)
    {
        $pdo = self::getConnection();

        $sql = "SELECT status FROM TOKENS_ADMINS WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function selectLastToken(array $data)
    {
        $pdo = self::getConnection();
        $sql = "SELECT created_at FROM TOKENS_ADMINS WHERE id_admin = :id_admin AND status = 'ACTIVE'";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $data['id_admin'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function inactiveAll(string $id_admin, string $type)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE TOKENS_ADMINS SET status = 'INACTIVE' WHERE id_admin = :id_admin AND type = :type";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $id_admin, PDO::PARAM_INT);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function inactiveToken(string $token)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE TOKENS_ADMINS SET status = 'INACTIVE' WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
