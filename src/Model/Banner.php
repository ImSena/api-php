<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Banner extends BaseModel
{
    public function create(array $data)
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO (id_media, name, is_mobile, is_default) VALUES (:id_media, :name, :is_mobile, :is_default)";

        $stmt = $pdo->prepare($sql);

        $name = isset($data['name']) ? $data['name'] : null;

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
        $stmt->bindParam(":name", $name, $name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":is_mobile", $data['is_mobile'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getBanners()
    {
        $pdo = $this->getPdo();

        $sql = "SELECT id_media, name, is_mobile, is_default FROM banners WHERE is_active > 0";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
