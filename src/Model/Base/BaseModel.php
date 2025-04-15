<?php

namespace App\Model\Base;

use App\Interfaces\Model;
use PDO;

abstract class BaseModel implements Model
{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function getPdo(): PDO{
        return $this->pdo;
    }
}