<?php

namespace App\Controllers;

use App\Service\AdminService;
use App\Service\NotificationsService;

class HomeController
{
    public function index()
    {
        header("Location: documentation");
    }

    public function teste()
    {
        $result = NotificationsService::sendNotificationsAdmin("ORDER_PROCESSING", []);
        var_dump($result);
    }
}