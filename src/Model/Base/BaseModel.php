<?php

namespace App\Model\Base;

use DateTime;
use PDO;

require_once __DIR__ . '/../../../config.php';

abstract class BaseModel
{
    protected PDO $pdo;
    protected string $currentDatetime;

    protected string $database;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
        $this->currentDatetime = (new DateTime())->format('Y-m-d H:i:s');
        $this->database = DBNAME;
    }

}