<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class SociaisStore extends BaseModel
{
    public function createSociais(array $data): bool
    {

        $sql = "INSERT INTO sociais_midias (type, link) VALUES (:type, :link)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":link", $data['link'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function findByType(string $type)
    {
        $sql = "SELECT * FROM sociais_midias WHERE type = :type LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getSociais(): ?array
    {
        $sql = "SELECT type, link FROM sociais_midias";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getSocial($type)
    {
        $sql = "SELECT type, link FROM sociais_midias WHERE type = :type";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(":type", $type, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function updateSocial(array $data):bool
    {
        $sql = "UPDATE sociais_midias SET link = :link, updated_at = :updated_at WHERE type = :type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":link", $data['link'], PDO::PARAM_STR);
        $stmt->bindParam("type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete(string $type)
    {
        $sql = "DELETE FROM sociais_midias WHERE type = :type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":type", $type, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
