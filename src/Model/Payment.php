<?php

namespace App\Model;

use PDO;

class Payment extends Database
{
    public function register(array $data)
    {
        $pdo = $this->getPdo();

        $sql = "INSERT INTO payments (
                        id_order, 
                        id_transaction, 
                        body_transaction, 
                        status, 
                        amount, 
                        payment_method, 
                        payment_date
                ) 
                VALUES (
                    :id_order,
                    :id_transaction,
                    :body_transaction,
                    :status, 
                    :amount,
                    :payment_method,
                    :payment_date
                )";
            
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_order", $data['id_order'], PDO::PARAM_INT);
        $stmt->bindParam(":id_transaction", $data['id_transaction'], PDO::PARAM_STR);
        $stmt->bindParam(":body_transaction", $data['body_transaction'], PDO::PARAM_STR);
        $stmt->bindParam(":status", $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(":amount", $data['amount'], PDO::PARAM_STR);
        $stmt->bindParam(":payment_method", $data['payment_method'], PDO::PARAM_STR);
        $stmt->bindParam(":payment_date", $data['payment_date'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}