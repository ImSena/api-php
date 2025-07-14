<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Phone extends BaseModel
{
    public function getAllByIdUser(int $id)
    {
        $sql = "SELECT * FROM phones WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function edit(array $data)
    {
        $sql = "UPDATE phones SET type = :type, number = :number, updated_at = :updated_at WHERE id_phone = :id AND id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":type", $data['contact']['type'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['contact']['number'], PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['contact']['id_phone'], PDO::PARAM_INT);
        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_INT);

        return $stmt->execute();

    }
}