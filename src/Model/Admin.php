<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use App\Model\Database;
use Pdo;

class Admin extends BaseModel
{
    public function create(array $data)
    {
        $pdo = $this->getPdo();
        $sql = "INSERT INTO admins (name, email, password, permission) VALUES (:name, :email, :password, :permission)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":permission", $data['permission'], PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId() > 0 ? true : false;
    }

    public function select(array $data){
        $pdo = $this->getPdo();
        $sql = "SELECT name, id_admin, permission, email, status, password FROM admins WHERE email = :email";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function updateAccess($data, $id)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE admins SET password = :password WHERE id_admin = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function activeAdmin($status, $id)
    {
        $pdo = $this->getPdo();

        $sql = "UPDATE admins SET status = :status WHERE id_admin = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getInfoAdmin($permission){
        $pdo = $this->getPdo();

        $sql = "SELECT name, email FROM admins WHERE permission = :permission ORDER BY id_admin LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":permission", $permission, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}
