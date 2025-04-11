<?php

namespace App\Model\Stripe;

use PDO;

class Store{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    private function getPdo(){
        return $this->pdo;
    }

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

    public function getInfoStore()
    {
        $pdo = $this->getPdo();
        $sql = "SELECT name, domain FROM store WHERE stripe_account_id IS NOT NULL ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }


}