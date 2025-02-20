<?php

namespace App\Model;

use App\Helpers\DatabaseErrorHelpers;
use Exception;
use PDO;
use PDOException;

require_once('./config.php');

class Database
{
    protected static function getConnection()
    {
        $dsn = "mysql:host=" . HOST . ";dbname=" . DBNAME . ";charset=utf8mb4;";

        $pdo = new PDO($dsn, USERNAME, PASSWORD);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}
