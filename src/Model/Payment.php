<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Payment extends BaseModel
{    
    public function register(array $data)
    {
        $sql = "INSERT INTO payments (
                        id_order, 
                        id_transaction, 
                        body_transaction, 
                        status, 
                        amount, 
                        payment_method, 
                        payment_date,
                        send_email
                ) 
                VALUES (
                    :id_order,
                    :id_transaction,
                    :body_transaction,
                    :status, 
                    :amount,
                    :payment_method,
                    :payment_date,
                    :send_email
                )";
            
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id_order", $data['id_order'], PDO::PARAM_INT);
        $stmt->bindParam(":id_transaction", $data['id_transaction'], PDO::PARAM_STR);
        $stmt->bindParam(":body_transaction", $data['body_transaction'], PDO::PARAM_STR);
        $stmt->bindParam(":status", $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(":amount", $data['amount'], PDO::PARAM_STR);
        $stmt->bindParam(":payment_method", $data['payment_method'], PDO::PARAM_STR);
        $stmt->bindParam(":payment_date", $data['payment_date'], PDO::PARAM_STR);
        $stmt->bindParam(":send_email", $data['send_email'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}