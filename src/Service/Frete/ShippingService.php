<?php

namespace App\Service\Frete;

use PDO;

require_once __DIR__ . "/../../../config.php";

class ShippingService
{
    private PDO $pdo;
    private bool $production;
    private string $url;

    private string $token;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
        $this->production = true;
        $this->url = $this->production ? 'https://sandbox.melhorenvio.com.br/api/v2/me/' : 'https://melhorenvio.com.br/api/v2/me/';
        $this->token = TOKEN_SHIPPING;
    }

    public function createQuote(array $data){

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $this->url.'shipment/calculate', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '
            ]
        ]);


    }
}