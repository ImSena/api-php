<?php

namespace App\Factory;

use PDO;

require_once __DIR__ . '/../../config.php';

class ConnectionFactory
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . HOST . ";dbname=" . DBNAME . ";charset=utf8mb4";

            self::$pdo = new PDO($dsn, USERNAME, PASSWORD);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        }

        return self::$pdo;
    }
}
