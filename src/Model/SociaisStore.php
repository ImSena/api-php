<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class SociaisStore extends BaseModel
{
    public function createSociais(array $data): bool
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO sociais_midias (type, link) VALUES (:type, :link)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":link", $data['link'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function findByType(string $type): ?array
    {
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM sociais_midias WHERE type = :type LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getSociais(): ?array
    {
        $pdo = $this->getPdo();
        $sql = "SELECT type, link FROM sociais_midias";
        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
