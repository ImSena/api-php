<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Banner extends BaseModel
{
    public function create(array $data)
    {
        $sql = "INSERT INTO banners (id_media, name, is_mobile, is_default) VALUES (:id_media, :name, :is_mobile, :is_default)";

        $stmt = $this->pdo->prepare($sql);

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
        $sql = "SELECT id_banner, id_media, name, is_mobile, is_default FROM banners WHERE is_active > 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function update(array $data)
    {
        $sql = "UPDATE banners SET 
        id_media = :id_media, 
        name = :name, 
        is_mobile = :is_mobile, 
        is_default = :is_default,
        updated_at = :updated_at
        WHERE id_banner = :id_banner";

        $stmt = $this->pdo->prepare($sql);

        $name = isset($data['name']) ? $data['name'] : null;

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
        $stmt->bindParam(":name", $name, $name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":is_mobile", $data['is_mobile'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":id_banner", $data['id_banner'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function setDefault(bool $value = false): bool
    {
        $sql = "UPDATE banners SET is_default = :value WHERE is_default != :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_BOOL);
        $stmt->execute();

        return true;
    }

    public function delete(int $id)
    {
        $sql = "UPDATE banners SET is_active = false, updated_at = :updated_at WHERE id_banner = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
