<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use Exception;
use PDO;
use PDOException;

class StoreMedia extends BaseModel
{

    public function create(array $data)
    {
        $pdo = $this->getPdo();
        $sql = "INSERT INTO store_media (id_media, type) VALUES (:id_media, :type)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function inactiveMedia(array $data)
    {

        $pdo = $this->getPdo();
        $sql = "UPDATE store_media SET is_active = :is_active, updated_at = :updated_at WHERE type = :type";

        $stmt = $pdo->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar a query SQL.");
        }

        $stmt->bindValue(":is_active", false, PDO::PARAM_BOOL);
        $stmt->bindValue(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

    }
}
