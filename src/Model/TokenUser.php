<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use App\Model\Database;
use Exception;
use Pdo;

class TokenUser extends BaseModel
{
    public function create(array $data)
    {

        $pdo = $this->getPdo();

        $sql = "INSERT INTO tokens_users (id_user, type, token) VALUES (:id_user, :type, :token)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_STR);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":token", $data['token'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function select(string $token)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT status FROM tokens_users WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }
    public function selectLastToken(array $data)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT created_at FROM tokens_users WHERE id_user = :id_user AND status = 'ACTIVE'";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
    public function inactiveAll(string $id_user, string $type)
    {
        $pdo = $this->getPdo();

        $sql = "UPDATE tokens_users SET status = 'INACTIVE' WHERE id_user = :id_user AND type = :type";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function inactiveToken(string $token)
    {
        $pdo = $this->getPdo();

        $sql = "UPDATE tokens_users SET status = 'INACTIVE' WHERE token = :token";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
