<?php
require_once __DIR__ . "/vendor/autoload.php";
use App\Factory\ConnectionFactory;
use App\Notifications\NotificationsManager;

$con = ConnectionFactory::getConnection();

$notify = new NotificationsManager($con);

$notificacao = $notify->getDefaultNotifier();

$data = [
    "order_number" => "5488569",
    "order_date" => "13/05/2025",
    "order_status_url"=>"localhost/api-payments",
    "item.product_url" => "tete",
    "item.product_name" => "teste"

];

$send = $notificacao->sendOrderCreated($data, "teste@escalaweb.com.br");

var_dump($send);exit;
