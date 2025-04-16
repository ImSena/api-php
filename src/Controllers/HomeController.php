<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Service\AdminService;
use App\Service\NotificationsService;
use PDO;

class HomeController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function index()
    {
        header("Location: documentation");
    }
}