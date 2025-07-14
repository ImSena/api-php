<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use App\Model\Database;
use Exception;
use Pdo;

class TokenAdmin extends BaseModel
{
    public function create(array $data)
    {
        $sql = "INSERT INTO tokens_admins (id_admin, type, token) VALUES (:id_admin, :type, :token)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $data['id_admin'], PDO::PARAM_STR);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":token", $data['token'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function select(string $token)
    {
        $sql = "SELECT status FROM tokens_admins WHERE token = :token";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function selectLastToken(array $data)
    {
        $sql = "SELECT created_at FROM tokens_admins WHERE id_admin = :id_admin AND status = 'ACTIVE'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $data['id_admin'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function inactiveAll(string $id_admin, string $type)
    {

        $sql = "UPDATE tokens_admins SET status = 'INACTIVE', updated_at = :updated_at WHERE id_admin = :id_admin AND type = :type";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id_admin", $id_admin, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function inactiveToken(string $token)
    {

        $sql = "UPDATE tokens_admins SET status = 'INACTIVE', updated_at = :updated_at WHERE token = :token";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
