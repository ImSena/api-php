<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class EmailStore extends BaseModel
{

    public function createEmailStore(array $data): bool
    {
        $sql = "INSERT INTO emails_store (email, is_default, is_show) VALUES (:email, :is_default, :is_show)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function setIsDefault($value = false): bool
    {
        $sql = "UPDATE emails_store SET is_default = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function getEmails()
    {
        $sql = "SELECT id_email_store, email, is_default, is_show FROM emails_store";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateEmail(array $data):bool
    {
        $sql = "UPDATE emails_store SET 
        email = :email,
        is_default = :is_default,
        is_show = :is_show,
        updated_at = :updated_at
        WHERE id_email_store = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);
        $stmt->bindParam(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);

        return $stmt->execute();
    }
}
