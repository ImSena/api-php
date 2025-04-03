<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\PaymentService;

class PaymentsController{
    public static function pay(Request $request, Response $response, $param)
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

        $paymentService = PaymentService::payOrder($body);

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

    public static function getPayments(Request $request, Response $response, $param)
    {

    }

    public static function getDetails(Request $request, Response $response, $param)
    {

    }
}