<?php

namespace App\Controllers\Frete;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\Frete\ShippingService;
use PDO;

class ShippingController
{

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function getQuote(Request $request, Response $response)
    {

        $body = $request::body();

        $shippingService = new ShippingService($this->pdo);

        $result = $shippingService->getQuote($body);

        if (isset($result['error'])) {
            return [
                $response::json([
                    "success" => false,
                    "message" => $result['error']
                ], 400)
            ];
        }

        $response::json([
            "success" => true,
            "message" => "Cotação de frete resgatada com sucesso",
            "content" => $result
        ]);
    }
}
