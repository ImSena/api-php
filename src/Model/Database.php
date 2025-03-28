<?php

namespace App\Model;
use PDO;

require_once('./config.php');

class Database
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    protected function getConnection()
    {
        $dsn = "mysql:host=" . HOST . ";dbname=" . DBNAME . ";charset=utf8mb4;";

        $pdo = new PDO($dsn, USERNAME, PASSWORD);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);

        return $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }


}
