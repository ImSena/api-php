<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\PaymentService;
use PDO;

class PaymentsController
{

    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function pay(Request $request, Response $response, $param)
    {
        $params['id_order'] = isset($param[0]) ? (int) $param[0] : null;
        $body = $request::body();
        $body['id_order'] = $params['id_order'];


        if(!isset($params['id_order'])){
            return $response::json([
                'success' => false,
                'message' => "Por favor, informe o id do pagamento"
            ], 400);
        }

        $paymentService = new PaymentService($this->pdo);
        $paymentService = $paymentService->payOrder($body);

        if(isset($paymentService['error'])){
            return $response::json([
                "success" => false,
                "message" => $paymentService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $paymentService['message'],
            "session_url" => $paymentService['session_url']
        ]);
    }

    public function getPayments(Request $request, Response $response, $param)
    {

    }

    public function getDetails(Request $request, Response $response, $param)
    {

    }
}