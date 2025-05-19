<?php

namespace App\Model;

use App\Model\Base\BaseModel;

class Errors extends BaseModel
{
    public function register(array $data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO errors (source, context, message, payload)
            VALUES (:source, :context, :message, :payload)
        ");

        $stmt->execute([
            'source' => $data['source'],
            'context' => $data['context'],
            'message' => $data['message'],
            'payload' => $data['payload'] ?? null
        ]);

        return $stmt->rowCount() > 0;
    }
}
