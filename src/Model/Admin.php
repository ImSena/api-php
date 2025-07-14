<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use App\Model\Database;
use Pdo;

class Admin extends BaseModel
{
    public function create(array $data)
    {
        $sql = "INSERT INTO admins (name, email, password, permission) VALUES (:name, :email, :password, :permission)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":permission", $data['permission'], PDO::PARAM_STR);

        $stmt->execute();

        return $this->pdo->lastInsertId() > 0 ? true : false;
    }

    public function select(array $data){
        $sql = "SELECT name, id_admin, permission, email, status, password FROM admins WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function updateAccess($data, $id)
    {
        $sql = "UPDATE admins SET password = :password, updated_at = :updated_at WHERE id_admin = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function activeAdmin($status, $id)
    {

        $sql = "UPDATE admins SET status = :status, updated_at = :updated_at WHERE id_admin = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getInfoAdmin($permission){

        $sql = "SELECT name, email FROM admins WHERE permission = :permission ORDER BY id_admin LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":permission", $permission, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}
