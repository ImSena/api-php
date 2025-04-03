<?php

namespace App\Model\Stripe;

use App\Model\Database;
use PDO;

class Store extends Database{
    public function createStore(array $data){
        $pdo = $this->getPdo();

        $sql = "INSERT INTO store (name, domain) VALUES (:name, :domain)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":name", $data['store_name'], PDO::PARAM_STR);
        $stmt->bindParam(":domain", $data['domain'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }


    public function findStores(){
        $pdo = $this->getPdo();

        $sql = "SELECT id_store, name, domain, stripe_account_id FROM store";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(string $storeId)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT 
                    id_store, 
                    name, 
                    domain, 
                    stripe_account_id, 
                    (SELECT email FROM admins WHERE permission = 'SUPER' LIMIT 1) as email
                FROM store 
                WHERE id_store = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $storeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateAccount(array $data){
        $pdo = $this->getPdo();
        $sql = "UPDATE store SET stripe_account_id = :account_id WHERE id_store = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":account_id", $data["stripe_account_id"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id_store'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
}