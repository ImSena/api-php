<?php
require_once __DIR__ . "/vendor/autoload.php";

use App\Factory\ConnectionFactory;
use App\Notifications\NotificationsManager;

$con = ConnectionFactory::getConnection();

$notify = new NotificationsManager($con);

$notificacao = $notify->getDefaultNotifier();

$data = [
    "order_number" => "5488569",
    "order_date" => "13/05/2025 - 12:00:25",
    "order_url" => "localhost/api-payments",
    "items" => [
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ],
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ]
    ],
    "subtotal" => "R$50,00",
    "shipping" => "R$12,00",
    "total" => "R$62,00",
    'shipping_method' => [
        'carrier' => 'Correios',
        'service' => 'Sedex'
    ],
    'shipping_address' => [
        'public_area' => 'Rua Exemplo',
        'number' => '123',
        'complement' => 'Apto 101',
        'district' => 'Centro',
        'city' => 'São Paulo',
        'state' => 'SP'
    ]
];

$dataPayment = [
    "order_number" => "5488569",
    "order_date" => "13/05/2025 - 12:00:25",
    "order_url" => "localhost/api-payments",
    "items" => [
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ],
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ]
    ],
    "customer" => [
        "name" => "Tester"
    ],
    "payment_method" => "Cartão",
    "subtotal" => "R$50,00",
    "shipping" => "R$12,00",
    "total" => "R$62,00",
    'shipping_method' => [
        'carrier' => 'Correios',
        'service' => 'Sedex'
    ],
    'shipping_address' => [
        'public_area' => 'Rua Exemplo',
        'number' => '123',
        'complement' => 'Apto 101',
        'district' => 'Centro',
        'city' => 'São Paulo',
        'state' => 'SP'
    ]
];

$dataDenied = [
    "order_number" => "5488569",
    "order_date" => "13/05/2025 - 12:00:25",
    "store_url" => "localhost/api-payments",
    "items" => [
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ],
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ]
    ],
    "customer" => [
        "name" => "Tester"
    ],
    "payment_method" => "Cartão",
    "subtotal" => "R$50,00",
    "shipping" => "R$12,00",
    "total" => "R$62,00",
    'shipping_method' => [
        'carrier' => 'Correios',
        'service' => 'Sedex'
    ],
    'shipping_address' => [
        'public_area' => 'Rua Exemplo',
        'number' => '123',
        'complement' => 'Apto 101',
        'district' => 'Centro',
        'city' => 'São Paulo',
        'state' => 'SP'
    ]
];

$dataShipped = [
    "order_number" => "5488569",
    "order_date" => "13/05/2025 - 12:00:25",
    "order_url" => "localhost/api-payments",
    "items" => [
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ],
        [
            "product_url" => "https://nsararidades.com.br/",
            "product_image" => "http://localhost/api-php/uploads/Ecommerce/produtos/01/17466376306265518.png",
            "product_name" => "Moeda real",
            "quantity" => 1,
            "price" => "R$50,00",
        ]
    ],
    "customer" => [
        "name" => "Tester"
    ],
    "payment_method" => "Cartão",
    "subtotal" => "R$50,00",
    "shipping" => "R$12,00",
    "total" => "R$62,00",
    'shipping_method' => [
        'carrier' => 'Correios',
        'service' => 'Sedex'
    ],
    'shipping_address' => [
        'public_area' => 'Rua Exemplo',
        'number' => '123',
        'complement' => 'Apto 101',
        'district' => 'Centro',
        'city' => 'São Paulo',
        'state' => 'SP'
    ]
];

$send = $notificacao->sendOrderCreated($data, "escalaweb4@gmail.com");
$send = $notificacao->sendPaymentConfirmed($dataPayment, "escalaweb4@gmail.com");
$send = $notificacao->sendPaymentDenied($dataDenied, "escalaweb4@gmail.com");
$send = $notificacao->sendOrderShipped($dataPayment, "escalaweb4@gmail.com");
$send = $notificacao->sendOrderDeliverd($dataDenied, "teste@escalaweb.com.br");

var_dump($send);
exit;
